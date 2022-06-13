<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\User\Form\Type;

use Ibexa\ContentForms\Form\EventSubscriber\UserFieldsSubscriber;
use Ibexa\ContentForms\Form\Type\Content\BaseContentType;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\User\Form\Data\UserRegisterData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type for content edition (create/update).
 * Underlying data will be either \Ibexa\ContentForms\Data\Content\ContentCreateData or \Ibexa\ContentForms\Data\Content\ContentUpdateData
 * depending on the context (create or update).
 */
class UserRegisterType extends AbstractType
{
    private ConfigResolverInterface $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

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
        $allowedFieldsId = $this
            ->configResolver
            ->getParameter('user_registration.form.allowed_field_definitions_identifiers');

        $builder
            ->add('register', SubmitType::class, ['label' => /** @Desc("Register") */ 'user.register_button'])
            ->addEventSubscriber(new UserFieldsSubscriber());

        $builder->get('fieldsData')->addEventListener(
            FormEvents::PRE_SET_DATA,
            static function (FormEvent $event) use ($allowedFieldsId) {
                $fieldsData = $event->getForm();
                foreach ($fieldsData as $fieldData) {
                    if (!in_array($fieldData->getName(), $allowedFieldsId, true)) {
                        $fieldsData->remove($fieldData->getName());
                    }
                }
            },
            -10
        );
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
