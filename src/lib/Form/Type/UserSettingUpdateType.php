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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSettingUpdateType extends AbstractType
{
    public const BTN_UPDATE_AND_EDIT = 'update_and_edit';

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
        $groupDefinition = $this->valueDefinitionRegistry->getValueDefinitionGroup(
            $options['user_setting_group_identifier']
        );

        $builder
            ->add('identifier', HiddenType::class, []);

        foreach ($groupDefinition->getValueDefinitions() as $identifier => $valueDefinition) {
            $formMapper = $this->formMapperRegistry->getFormMapper($identifier);
            $valueField = $formMapper->mapFieldForm($builder, $valueDefinition);

            $sub = $builder->create(
                $identifier,
                FormType::class,
            )->setPropertyPath('values[' . $identifier . ']');

            $sub->add(
                $valueField
            );

            $builder->add($sub);
        }
        $builder->add('update', SubmitType::class, []);
        $builder->add(self::BTN_UPDATE_AND_EDIT, SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('user_setting_group_identifier')
            ->setAllowedTypes('user_setting_group_identifier', 'string')
            ->setAllowedValues(
                'user_setting_group_identifier',
                array_keys($this->valueDefinitionRegistry->getValueDefinitionGroups())
            )
            ->setDefaults([
                'data_class' => UserSettingUpdateData::class,
                'translation_domain' => 'forms',
            ])
        ;
    }
}

class_alias(UserSettingUpdateType::class, 'EzSystems\EzPlatformUser\Form\Type\UserSettingUpdateType');
