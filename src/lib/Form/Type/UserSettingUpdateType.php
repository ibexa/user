<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Form\Type;

use Ibexa\User\Form\Data\UserSettingUpdateData;
use Ibexa\User\UserSetting\FormMapperRegistry;
use Ibexa\User\UserSetting\ValueDefinitionRegistry;
use RuntimeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSettingUpdateType extends AbstractType
{
    /** @var \Ibexa\User\UserSetting\FormMapperRegistry */
    protected $formMapperRegistry;

    /** @var \Ibexa\User\UserSetting\ValueDefinitionRegistry */
    protected $valueDefinitionRegistry;

    /**
     * @param \Ibexa\User\UserSetting\FormMapperRegistry $formMapperRegistry
     * @param \Ibexa\User\UserSetting\ValueDefinitionRegistry $valueDefinitionRegistry
     */
    public function __construct(
        FormMapperRegistry $formMapperRegistry,
        ValueDefinitionRegistry $valueDefinitionRegistry
    ) {
        $this->formMapperRegistry = $formMapperRegistry;
        $this->valueDefinitionRegistry = $valueDefinitionRegistry;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formMapper = $this->formMapperRegistry->getFormMapper($options['user_setting_identifier']);
        $valueDefinition = $this->valueDefinitionRegistry->getValueDefinition($options['user_setting_identifier']);

        $builder
            ->add('identifier', HiddenType::class, [])
            ->add($formMapper->mapFieldForm($builder, $valueDefinition))
            ->add('update', SubmitType::class, [])
        ;

        if (!$builder->has('value')) {
            throw new RuntimeException("FormMapper should create a 'value' field");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('user_setting_identifier')
            ->setAllowedTypes('user_setting_identifier', 'string')
            ->setAllowedValues('user_setting_identifier', array_keys($this->formMapperRegistry->getFormMappers()))
            ->setDefaults([
                'data_class' => UserSettingUpdateData::class,
                'translation_domain' => 'forms',
            ])
        ;
    }
}

class_alias(UserSettingUpdateType::class, 'EzSystems\EzPlatformUser\Form\Type\UserSettingUpdateType');
