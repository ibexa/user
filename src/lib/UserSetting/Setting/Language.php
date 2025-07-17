<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting\Setting;

use Ibexa\Contracts\User\UserSetting\FormMapperInterface;
use Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface;
use Ibexa\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface;
use Ibexa\User\Form\ChoiceList\Loader\AvailableLocaleChoiceLoader;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Language implements ValueDefinitionInterface, FormMapperInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider,
        private readonly AvailableLocaleChoiceLoader $availableLocaleChoiceLoader
    ) {
    }

    public function getName(): string
    {
        return $this->getTranslatedName();
    }

    public function getDescription(): string
    {
        return $this->getTranslatedDescription();
    }

    public function getDisplayValue(string $storageValue): string
    {
        return $storageValue;
    }

    public function getDefaultValue(): string
    {
        $defaultLocale = '';
        $preferredLocales = $this->userLanguagePreferenceProvider->getPreferredLocales();

        $list = $this->availableLocaleChoiceLoader->getChoiceList();
        $commonLocales = array_intersect($preferredLocales, $list);
        if (!empty($commonLocales)) {
            $defaultLocale = reset($commonLocales);
        }

        return $defaultLocale;
    }

    public function mapFieldForm(FormBuilderInterface $formBuilder, ValueDefinitionInterface $value): FormBuilderInterface
    {
        return $formBuilder->create(
            'value',
            LocaleType::class,
            [
                'required' => true,
                'label' => $this->getTranslatedDescription(),
                'choice_loader' => $this->availableLocaleChoiceLoader,
            ]
        );
    }

    private function getTranslatedName(): string
    {
        return $this->translator->trans(
            /** @Desc("Language") */
            'settings.language.value.title',
            [],
            'ibexa_user_settings'
        );
    }

    private function getTranslatedDescription(): string
    {
        return $this->translator->trans(
            /** @Desc("Language") */
            'settings.language.value.description',
            [],
            'ibexa_user_settings'
        );
    }
}
