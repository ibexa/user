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
    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /** @var \Ibexa\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface */
    private $userLanguagePreferenceProvider;

    /** @var \Ibexa\User\Form\ChoiceList\Loader\AvailableLocaleChoiceLoader */
    private $availableLocaleChoiceLoader;

    /**
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     * @param \Ibexa\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider
     * @param \Ibexa\User\Form\ChoiceList\Loader\AvailableLocaleChoiceLoader $availableLocaleChoiceLoader
     */
    public function __construct(
        TranslatorInterface $translator,
        UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider,
        AvailableLocaleChoiceLoader $availableLocaleChoiceLoader
    ) {
        $this->translator = $translator;
        $this->userLanguagePreferenceProvider = $userLanguagePreferenceProvider;
        $this->availableLocaleChoiceLoader = $availableLocaleChoiceLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->getTranslatedName();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        return $this->getTranslatedDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayValue(string $storageValue): string
    {
        return $storageValue;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * @return string
     */
    private function getTranslatedName(): string
    {
        return $this->translator->trans(
            /** @Desc("Language") */
            'settings.language.value.title',
            [],
            'ibexa_user_settings'
        );
    }

    /**
     * @return string
     */
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

class_alias(Language::class, 'EzSystems\EzPlatformUser\UserSetting\Setting\Language');
