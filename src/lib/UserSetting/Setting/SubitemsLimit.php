<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting\Setting;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\User\UserSetting\FormMapperInterface;
use Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SubitemsLimit implements ValueDefinitionInterface, FormMapperInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly ConfigResolverInterface $configResolver
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
        return (string)$this->configResolver->getParameter('subitems_module.limit');
    }

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

    private function getTranslatedName(): string
    {
        return $this->translator->trans(
            /** @Desc("Sub-items") */
            'settings.subitems_limit.value.title',
            [],
            'ibexa_user_settings'
        );
    }

    private function getTranslatedDescription(): string
    {
        return $this->translator->trans(
            /** @Desc("Number of items displayed in the sub-items") */
            'settings.subitems_limit.value.description',
            [],
            'ibexa_user_settings'
        );
    }
}
