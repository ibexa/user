<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\User\Validator\Constraint;

use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\User\PasswordValidationContext;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\User\Password\PasswordRequirement;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Core\Repository\Validator\UserPasswordValidator;
use Ibexa\User\Validator\Constraints\Password;
use Ibexa\User\Validator\Constraints\PasswordValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class PasswordValidatorTest extends TestCase
{
    private UserService&MockObject $userService;

    private ExecutionContextInterface&MockObject $executionContext;

    private PasswordValidator $validator;

    protected function setUp(): void
    {
        $this->userService = $this->createMock(UserService::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new PasswordValidator($this->userService);
        $this->validator->initialize($this->executionContext);
    }

    /**
     * @dataProvider dataProviderForValidateNotSupportedValueType
     */
    public function testValidateShouldBeSkipped(\stdClass|string|null $value): void
    {
        $this->userService
            ->expects(self::never())
            ->method('validatePassword');

        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $this->validator->validate($value, new Password());
    }

    public function testValid(): void
    {
        $password = 'pass';
        $contentType = $this->createMock(ContentType::class);
        $user = $this->createMock(User::class);

        $this->userService
            ->expects(self::once())
            ->method('validatePassword')
            ->willReturnCallback(
                static function (string $actualPassword, PasswordValidationContext $actualContext) use (
                    $password,
                    $contentType,
                    $user
                ): array {
                    self::assertEquals($password, $actualPassword);
                    self::assertInstanceOf(PasswordValidationContext::class, $actualContext);
                    self::assertSame($contentType, $actualContext->contentType);
                    self::assertSame($user, $actualContext->user);

                    return [];
                }
            );

        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $this->validator->validate(
            $password,
            new Password([
                'contentType' => $contentType,
                'user' => $user,
            ])
        );
    }

    public function testInvalid(): void
    {
        $contentType = $this->createMock(ContentType::class);
        $password = 'pass';
        $errorParameter = 'foo';
        $errorMessage = 'error';

        $this->userService
            ->expects(self::once())
            ->method('validatePassword')
            ->willReturnCallback(function (string $actualPassword, PasswordValidationContext $actualContext) use (
                $password,
                $contentType,
                $errorMessage,
                $errorParameter
            ): array {
                $this->assertEquals($password, $actualPassword);
                $this->assertInstanceOf(PasswordValidationContext::class, $actualContext);
                $this->assertSame($contentType, $actualContext->contentType);

                return [
                    new ValidationError($errorMessage, null, ['%foo%' => $errorParameter]),
                ];
            });

        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $this->executionContext
            ->expects(self::once())
            ->method('buildViolation')
            ->willReturn($constraintViolationBuilder);
        $this->executionContext
            ->expects(self::once())
            ->method('buildViolation')
            ->with($errorMessage)
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->expects(self::once())
            ->method('setParameters')
            ->with(['%foo%' => $errorParameter])
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->expects(self::never())
            ->method('setCode');
        $constraintViolationBuilder
            ->expects(self::once())
            ->method('addViolation');

        $this->validator->validate('pass', new Password([
            'contentType' => $contentType,
        ]));
    }

    public function testPluralValidationErrorUsesPluralMessageTemplate(): void
    {
        $contentType = $this->createMock(ContentType::class);

        $this->userService
            ->method('validatePassword')
            ->willReturn([
                new ValidationError('singular error', 'plural error', ['%limit%' => 2]),
            ]);

        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $constraintViolationBuilder
            ->expects(self::once())
            ->method('setParameters')
            ->with(['%limit%' => 2])
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->expects(self::never())
            ->method('setCode');
        $constraintViolationBuilder
            ->expects(self::once())
            ->method('addViolation');

        $this->executionContext
            ->expects(self::once())
            ->method('buildViolation')
            ->with('plural error')
            ->willReturn($constraintViolationBuilder);

        $this->validator->validate('pass', new Password([
            'contentType' => $contentType,
        ]));
    }

    /**
     * @dataProvider dataProviderForKnownValidationErrorsGetRequirementCode
     */
    public function testKnownValidationErrorsGetRequirementCode(
        string $errorMessage,
        string $expectedCode
    ): void {
        $contentType = $this->createMock(ContentType::class);

        $this->userService
            ->method('validatePassword')
            ->willReturn([new ValidationError($errorMessage)]);

        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $constraintViolationBuilder
            ->method('setParameters')
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->expects(self::once())
            ->method('setCode')
            ->with($expectedCode)
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->expects(self::once())
            ->method('addViolation');

        $this->executionContext
            ->expects(self::once())
            ->method('buildViolation')
            ->with($errorMessage)
            ->willReturn($constraintViolationBuilder);

        $this->validator->validate('pass', new Password([
            'contentType' => $contentType,
        ]));
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public function dataProviderForKnownValidationErrorsGetRequirementCode(): array
    {
        return [
            'min length' => [
                'User password must be at least %length% characters long',
                PasswordRequirement::MIN_LENGTH,
            ],
            'upper case' => [
                'User password must include at least one upper case letter',
                PasswordRequirement::UPPER_CASE,
            ],
            'lower case' => [
                'User password must include at least one lower case letter',
                PasswordRequirement::LOWER_CASE,
            ],
            'numeric' => [
                'User password must include at least one number',
                PasswordRequirement::NUMERIC,
            ],
            'non alphanumeric' => [
                'User password must include at least one special character',
                PasswordRequirement::NON_ALPHANUMERIC,
            ],
            'new password' => [
                'New password cannot be the same as old password',
                PasswordRequirement::NEW_PASSWORD,
            ],
            'not compromised' => [
                'This password has been leaked in a data breach, it must not be used. Please use another password.',
                PasswordRequirement::NOT_COMPROMISED,
            ],
        ];
    }

    /**
     * Guards against core rewording validation messages, which would silently
     * break the message template → requirement code mapping.
     */
    public function testEveryCoreCharacterRuleErrorProducesRequirementCode(): void
    {
        $coreValidator = new UserPasswordValidator([
            'minLength' => 10,
            'requireAtLeastOneUpperCaseCharacter' => 1,
            'requireAtLeastOneLowerCaseCharacter' => 1,
            'requireAtLeastOneNumericCharacter' => 1,
            'requireAtLeastOneNonAlphanumericCharacter' => 1,
            'requireNewPassword' => null,
            'requireNotCompromisedPassword' => false,
        ]);
        $validationErrors = $coreValidator->validate('');
        self::assertCount(5, $validationErrors);

        $this->userService
            ->method('validatePassword')
            ->willReturn($validationErrors);

        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $constraintViolationBuilder
            ->method('setParameters')
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->expects(self::exactly(count($validationErrors)))
            ->method('setCode')
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->expects(self::exactly(count($validationErrors)))
            ->method('addViolation');

        $this->executionContext
            ->method('buildViolation')
            ->willReturn($constraintViolationBuilder);

        $this->validator->validate('pass', new Password([
            'contentType' => $this->createMock(ContentType::class),
        ]));
    }

    /**
     * @return array<array{0: stdClass|string|null}>
     */
    public function dataProviderForValidateNotSupportedValueType(): array
    {
        return [
            [new stdClass()],
            [null],
            [''],
        ];
    }
}
