<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\UserSetting\Setting;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Ibexa\Contracts\User\UserSetting\FormMapperInterface;
use Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SubitemsLimit implements ValueDefinitionInterface, FormMapperInterface
{
    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function __construct(TranslatorInterface $translator, ConfigResolverInterface $configResolver)
    {
        $this->translator = $translator;
        $this->configResolver = $configResolver;
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
        return (string)$this->configResolver->getParameter('subitems_module.limit');
    }

    /**
     * {@inheritdoc}
     */
    public function mapFieldForm(FormBuilderInterface $formBuilder, ValueDefinitionInterface $value): FormBuilderInterface
    {
        return $formBuilder->create(
            'value',
            NumberType::class,
            [
                'attr' => ['min' => 1],
                'required' => true,
                'label' => $this->getTranslatedDescription(),
            ]
        );
    }

    /**
     * @return string
     */
    private function getTranslatedName(): string
    {
        return $this->translator->trans(
            /** @Desc("Sub-items") */
            'settings.subitems_limit.value.title',
            [],
            'user_settings'
        );
    }

    /**
     * @return string
     */
    private function getTranslatedDescription(): string
    {
        return $this->translator->trans(
            /** @Desc("Number of items displayed in the table") */
            'settings.subitems_limit.value.description',
            [],
            'user_settings'
        );
    }
}
