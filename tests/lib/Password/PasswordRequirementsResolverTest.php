<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\User\Password;

use Ibexa\Contracts\Core\Persistence\User\Handler as UserHandler;
use Ibexa\Contracts\Core\Repository\PasswordHashService;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\User\Type as UserType;
use Ibexa\Core\Repository\User\PasswordValidatorInterface;
use Ibexa\User\Password\PasswordRequirement;
use Ibexa\User\Password\PasswordRequirementsResolver;
use PHPUnit\Framework\TestCase;

final class PasswordRequirementsResolverTest extends TestCase
{
    private PasswordRequirementsResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new PasswordRequirementsResolver();
    }

    public function testContentTypeWithoutUserFieldDefinition(): void
    {
        $contentType = $this->createMock(ContentType::class);
        $contentType
            ->expects(self::once())
            ->method('getFirstFieldDefinitionOfType')
            ->with('ibexa_user')
            ->willReturn(null);

        self::assertSame([], $this->resolver->getRequirements($contentType));
    }

    /**
     * @dataProvider dataProviderForGetRequirements
     *
     * @param array<string, mixed> $constraints
     * @param array<string, mixed> $fieldSettings
     * @param string[] $expectedIdentifiers
     */
    public function testGetRequirements(
        array $constraints,
        array $fieldSettings,
        array $expectedIdentifiers
    ): void {
        $requirements = $this->resolver->getRequirements(
            $this->createContentType($constraints, $fieldSettings)
        );

        self::assertSame(
            $expectedIdentifiers,
            array_map(
                static fn (PasswordRequirement $requirement): string => $requirement->getIdentifier(),
                $requirements
            )
        );
    }

    /**
     * @return array<string, array{
     *     0: array<string, mixed>,
     *     1: array<string, mixed>,
     *     2: string[],
     * }>
     */
    public function dataProviderForGetRequirements(): array
    {
        return [
            'all rules disabled' => [
                [
                    'minLength' => null,
                    'requireAtLeastOneUpperCaseCharacter' => null,
                    'requireAtLeastOneLowerCaseCharacter' => null,
                    'requireAtLeastOneNumericCharacter' => null,
                    'requireAtLeastOneNonAlphanumericCharacter' => null,
                    'requireNewPassword' => null,
                    'requireNotCompromisedPassword' => false,
                ],
                [],
                [],
            ],
            'all rules enabled' => [
                [
                    'minLength' => 10,
                    'requireAtLeastOneUpperCaseCharacter' => 1,
                    'requireAtLeastOneLowerCaseCharacter' => 1,
                    'requireAtLeastOneNumericCharacter' => 1,
                    'requireAtLeastOneNonAlphanumericCharacter' => 1,
                    'requireNewPassword' => 1,
                    'requireNotCompromisedPassword' => true,
                ],
                [],
                [
                    PasswordRequirement::MIN_LENGTH,
                    PasswordRequirement::UPPER_CASE,
                    PasswordRequirement::LOWER_CASE,
                    PasswordRequirement::NUMERIC,
                    PasswordRequirement::NON_ALPHANUMERIC,
                    PasswordRequirement::NEW_PASSWORD,
                    PasswordRequirement::NOT_COMPROMISED,
                ],
            ],
            'zero min length is disabled' => [
                ['minLength' => 0],
                [],
                [],
            ],
            'new password implied by password TTL' => [
                ['requireNewPassword' => null],
                ['PasswordTTL' => 90],
                [PasswordRequirement::NEW_PASSWORD],
            ],
            'missing validator configuration' => [
                [],
                [],
                [],
            ],
        ];
    }

    /**
     * Guards against core adding a new rule to the PasswordValueValidator schema
     * that this resolver would silently not expose.
     */
    public function testCoversEveryCoreValidatorSchemaRule(): void
    {
        $schema = (new UserType(
            $this->createMock(UserHandler::class),
            $this->createMock(PasswordHashService::class),
            $this->createMock(PasswordValidatorInterface::class)
        ))->getValidatorConfigurationSchema()['PasswordValueValidator'];

        $allRulesEnabled = array_map(
            static fn (array $rule) => $rule['type'] === 'int' ? 1 : true,
            $schema
        );
        $allRulesEnabled['minLength'] = 10;

        $requirements = $this->resolver->getRequirements(
            $this->createContentType($allRulesEnabled, [])
        );

        self::assertCount(
            count($schema),
            $requirements,
            'Every rule in the core PasswordValueValidator schema must produce a password requirement.'
        );
    }

    public function testMinLengthRequirementCarriesParameters(): void
    {
        $requirements = $this->resolver->getRequirements(
            $this->createContentType(['minLength' => 16], [])
        );

        self::assertCount(1, $requirements);
        self::assertSame(PasswordRequirement::MIN_LENGTH, $requirements[0]->getIdentifier());
        self::assertSame(['%length%' => 16], $requirements[0]->getParameters());
        self::assertSame('password_requirement.min_length', $requirements[0]->getTranslationKey());
    }

    /**
     * @param array<string, mixed> $constraints
     * @param array<string, mixed> $fieldSettings
     */
    private function createContentType(array $constraints, array $fieldSettings): ContentType
    {
        $fieldDefinition = $this->createMock(FieldDefinition::class);
        $fieldDefinition
            ->expects(self::once())
            ->method('getValidatorConfiguration')
            ->willReturn($constraints === [] ? [] : ['PasswordValueValidator' => $constraints]);
        $fieldDefinition
            ->expects(self::once())
            ->method('getFieldSettings')
            ->willReturn($fieldSettings);

        $contentType = $this->createMock(ContentType::class);
        $contentType
            ->expects(self::once())
            ->method('getFirstFieldDefinitionOfType')
            ->with('ibexa_user')
            ->willReturn($fieldDefinition);

        return $contentType;
    }
}
