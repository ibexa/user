<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\User\Form\Type;

use Ibexa\ContentForms\Form\EventSubscriber\UserFieldsSubscriber;
use Ibexa\ContentForms\Form\Type\Content\BaseContentType;
use Ibexa\User\Form\Data\UserRegisterData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type for content edition (create/update).
 * Underlying data will be either \Ibexa\ContentForms\Data\Content\ContentCreateData or \Ibexa\ContentForms\Data\Content\ContentUpdateData
 * depending on the context (create or update).
 */
class UserRegisterType extends AbstractType
{
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezplatform_content_forms_user_register';
    }

    public function getParent()
    {
        return BaseContentType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('register', SubmitType::class, ['label' => /** @Desc("Register") */ 'user.register_button'])
            ->addEventSubscriber(new UserFieldsSubscriber());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => UserRegisterData::class,
                'translation_domain' => 'ezplatform_content_forms_user_registration',
                'intent' => 'register',
            ])
            ->setRequired(['languageCode']);
    }
}

class_alias(UserRegisterType::class, 'EzSystems\EzPlatformUser\Form\Type\UserRegisterType');
