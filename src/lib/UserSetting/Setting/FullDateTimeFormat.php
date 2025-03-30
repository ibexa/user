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
use Ibexa\User\Form\Type\UserSettings\FullDateTimeFormatType;
use Ibexa\User\UserSetting\DateTimeFormat\FormatterInterface;
use Ibexa\User\UserSetting\Setting\Value\DateTimeFormat;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FullDateTimeFormat extends AbstractDateTimeFormat
{
    private TranslatorInterface $translator;

    private ConfigResolverInterface $configResolver;

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
        $format = $this->configResolver->getParameter('user_preferences.full_datetime_format');

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
        $form = $formBuilder->create(
            'value',
            FullDateTimeFormatType::class,
            [
                'label' => false,
            ]
        );

        $form->addModelTransformer(new DateTimeFormatTransformer($this->serializer));

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTranslatedName(): string
    {
        return $this->translator->trans(
            /** @Desc("Full date and time format") */
            'settings.full_datetime_format.value.title',
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
            /** @Desc("Full date and time format") */
            'settings.full_datetime_format.value.description',
            [],
            'ibexa_user_settings'
        );
    }

    /**
     * @return string[]
     */
    protected function getAllowedTimeFormats(): array
    {
        return $this->configResolver->getParameter('user_preferences.allowed_full_time_formats');
    }

    /**
     * @return string[]
     */
    protected function getAllowedDateFormats(): array
    {
        return $this->configResolver->getParameter('user_preferences.allowed_full_date_formats');
    }
}
