<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting\Setting;

use Ibexa\Contracts\User\UserSetting\FormMapperInterface;
use Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CharacterCounter implements ValueDefinitionInterface, FormMapperInterface
{
    public const ENABLED_OPTION = 'enabled';
    public const DISABLED_OPTION = 'disabled';

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /**
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
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
        switch ($storageValue) {
            case self::ENABLED_OPTION:
                return $this->getTranslatedOptionEnabled();
            case self::DISABLED_OPTION:
                return $this->getTranslatedOptionDisabled();
            default:
                throw new InvalidArgumentException(
                    '$storageValue',
                    sprintf('There is no \'%s\' option', $storageValue)
                );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValue(): string
    {
        return 'enabled';
    }

    /**
     * {@inheritdoc}
     */
    public function mapFieldForm(FormBuilderInterface $formBuilder, ValueDefinitionInterface $value): FormBuilderInterface
    {
        $choices = [
            $this->getTranslatedOptionEnabled() => self::ENABLED_OPTION,
            $this->getTranslatedOptionDisabled() => self::DISABLED_OPTION,
        ];

        return $formBuilder->create(
            'value',
            ChoiceType::class,
            [
                'multiple' => false,
                'required' => true,
                'label' => $this->getTranslatedDescription(),
                'choices' => $choices,
            ]
        );
    }

    /**
     * @return string
     */
    private function getTranslatedName(): string
    {
        return $this->translator->trans(
            /** @Desc("Character counter") */
            'settings.character_counter.value.title',
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
            /** @Desc("Enable character count in Online Editor") */
            'settings.character_counter.value.description',
            [],
            'ibexa_user_settings'
        );
    }

    /**
     * @return string
     */
    private function getTranslatedOptionEnabled(): string
    {
        return $this->translator->trans(
            /** @Desc("Enabled") */
            'settings.character_counter.value.enabled',
            [],
            'ibexa_user_settings'
        );
    }

    /**
     * @return string
     */
    private function getTranslatedOptionDisabled(): string
    {
        return $this->translator->trans(
            /** @Desc("Disabled") */
            'settings.character_counter.value.disabled',
            [],
            'ibexa_user_settings'
        );
    }
}

class_alias(CharacterCounter::class, 'EzSystems\EzPlatformUser\UserSetting\Setting\CharacterCounter');
