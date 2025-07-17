<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\User\Form\Type\ChoiceList\Loader;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\User\Form\ChoiceList\Loader\AvailableLocaleChoiceLoader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AvailableLocaleChoiceLoaderTest extends TestCase
{
    private ValidatorInterface&MockObject $validator;

    private ConstraintViolationInterface&MockObject $constraintViolation;

    private ConfigResolverInterface&MockObject $configResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->constraintViolation = $this->createMock(ConstraintViolationInterface::class);
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
    }

    /**
     * @param array<int, string> $availableTranslations
     * @param array<int, string> $additionalTranslations
     * @param array<string, string> $expectedLocales
     *
     * @dataProvider providerForGetChoiceList
     */
    public function testGetChoiceList(
        array $availableTranslations,
        array $additionalTranslations,
        array $expectedLocales
    ): void {
        $this->validator
            ->method('validate')
            ->willReturnCallback(fn ($locale): ConstraintViolationList => $locale === 'foo_BAR' ? new ConstraintViolationList([$this->constraintViolation]) : new ConstraintViolationList());

        $this->configResolver
            ->method('getParameter')
            ->with('user_preferences.additional_translations')
            ->willReturn($additionalTranslations);

        $availableLocaleChoiceLoader = new AvailableLocaleChoiceLoader(
            $this->validator,
            $this->configResolver,
            $availableTranslations
        );

        self::assertSame($expectedLocales, $availableLocaleChoiceLoader->getChoiceList());
    }

    /**
     * @return array<string, array{
     *     0: array<int, string>,
     *     1: array<int, string>,
     *     2: array<string, string>
     * }>
     */
    public function providerForGetChoiceList(): array
    {
        return [
            'available_translations' => [
                ['en', 'nb_NO'],
                [],
                [
                    'English' => 'en',
                    'Norwegian Bokmål (Norway)' => 'nb_NO',
                ],
            ],
            'available_and_additional_translations' => [
                ['en', 'nb_NO'],
                ['de_DE'],
                [
                    'English' => 'en',
                    'Norwegian Bokmål (Norway)' => 'nb_NO',
                    'German (Germany)' => 'de_DE',
                ],
            ],
            'unsupported_translation' => [
                ['en', 'nb_NO'],
                ['de_DE', 'foo_BAR'],
                [
                    'English' => 'en',
                    'Norwegian Bokmål (Norway)' => 'nb_NO',
                    'German (Germany)' => 'de_DE',
                ],
            ],
            'acholi_exlusion' => [
                ['en', 'ach-UG'],
                [],
                [
                    'English' => 'en',
                ],
            ],
        ];
    }
}
