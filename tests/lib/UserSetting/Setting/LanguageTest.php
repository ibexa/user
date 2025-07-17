<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\User\UserSetting;

use Ibexa\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface;
use Ibexa\User\Form\ChoiceList\Loader\AvailableLocaleChoiceLoader;
use Ibexa\User\UserSetting\Setting\Language;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

final class LanguageTest extends TestCase
{
    private UserLanguagePreferenceProviderInterface&MockObject $userLanguagePreferenceProvider;

    private AvailableLocaleChoiceLoader&MockObject $availableLocaleChoiceLoader;

    protected function setUp(): void
    {
        $this->userLanguagePreferenceProvider = $this->createMock(
            UserLanguagePreferenceProviderInterface::class
        );
        $this->availableLocaleChoiceLoader = $this->createMock(
            AvailableLocaleChoiceLoader::class
        );
    }

    /**
     * @dataProvider providerForDefaultValue
     *
     * @param string[] $availableLocales
     * @param string[] $preferredLocales
     */
    public function testGetDefaultValue(
        array $preferredLocales,
        array $availableLocales,
        string $expectedDefaultValue
    ): void {
        $this->userLanguagePreferenceProvider->method('getPreferredLocales')->willReturn($preferredLocales);
        $this->availableLocaleChoiceLoader->method('getChoiceList')->willReturn($availableLocales);

        $language = new Language(
            $this->createMock(TranslatorInterface::class),
            $this->userLanguagePreferenceProvider,
            $this->availableLocaleChoiceLoader,
        );

        self::assertSame($expectedDefaultValue, $language->getDefaultValue());
    }

    /**
     * @return iterable<string, array<mixed>>
     */
    public function providerForDefaultValue(): iterable
    {
        yield 'intersection' => [['en_GB', 'en'], ['en', 'de', 'el', 'en_US'], 'en'];
        yield 'no available locale' => [['en_GB', 'en'], ['de', 'el', 'en_US'], ''];
        yield 'user preferred language priority' => [['en_GB', 'en', 'de'], ['de', 'en', 'el'], 'en'];
    }
}
