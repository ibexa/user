<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting\Setting;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface;
use Ibexa\User\Form\DataTransformer\DateTimeFormatTransformer;
use Ibexa\User\Form\Type\UserSettings\ShortDateTimeFormatType;
use Ibexa\User\UserSetting\DateTimeFormat\FormatterInterface;
use Ibexa\User\UserSetting\Setting\Value\DateTimeFormat;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ShortDateTimeFormat extends AbstractDateTimeFormat
{
    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    /**
     * @param \Ibexa\User\UserSetting\Setting\DateTimeFormatSerializer $serializer
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     * @param \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface $configResolver
     * @param \Ibexa\User\UserSetting\DateTimeFormat\FormatterInterface $formatter
     */
    public function __construct(
        DateTimeFormatSerializer $serializer,
        TranslatorInterface $translator,
        ConfigResolverInterface $configResolver,
        FormatterInterface $formatter
    ) {
        parent::__construct($serializer, $formatter);
        $this->translator = $translator;
        $this->configResolver = $configResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValue(): string
    {
        $format = $this->configResolver->getParameter('user_preferences.short_datetime_format');

        return $this->serializer->serialize(
            new DateTimeFormat($format['date_format'], $format['time_format'])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function mapFieldForm(
        FormBuilderInterface $formBuilder,
        ValueDefinitionInterface $value
    ): FormBuilderInterface {
        $valueForm = $formBuilder->create(
            'value',
            ShortDateTimeFormatType::class,
            [
                'label' => false,
            ]
        );

        $valueForm->addModelTransformer(new DateTimeFormatTransformer($this->serializer));

        return $valueForm;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTranslatedName(): string
    {
        return $this->translator->trans(
            /** @Desc("Short date and time format") */
            'settings.short_datetime_format.value.title',
            [],
            'ibexa_user_settings'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTranslatedDescription(): string
    {
        return $this->translator->trans(
            /** @Desc("Short date and time format") */
            'settings.short_datetime_format.value.description',
            [],
            'ibexa_user_settings'
        );
    }

    /**
     * @return string[]
     */
    protected function getAllowedTimeFormats(): array
    {
        return $this->configResolver->getParameter('user_preferences.allowed_short_time_formats');
    }

    /**
     * @return string[]
     */
    protected function getAllowedDateFormats(): array
    {
        return $this->configResolver->getParameter('user_preferences.allowed_short_date_formats');
    }
}

class_alias(ShortDateTimeFormat::class, 'EzSystems\EzPlatformUser\UserSetting\Setting\ShortDateTimeFormat');
