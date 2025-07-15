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
use JMS\TranslationBundle\Annotation\Desc;
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
    public function __construct(
        private readonly ConfigResolverInterface $configResolver
    ) {
    }

    public function getName(): string
    {
        return $this->getBlockPrefix();
    }

    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'ezplatform_content_forms_user_register';
    }

    #[\Override]
    public function getParent(): string
    {
        return BaseContentType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('register', SubmitType::class, ['label' => /** @Desc("Register") */ 'user.register_button'])
            ->addEventSubscriber(new UserFieldsSubscriber());

        $builder->get('fieldsData')->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event): void {
                $allowedFieldsId = $this
                    ->configResolver
                    ->getParameter('user_registration.form.allowed_field_definitions_identifiers');

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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => UserRegisterData::class,
                'translation_domain' => 'ibexa_content_forms_user_registration',
                'intent' => 'register',
            ])
            ->setRequired(['languageCode']);
    }
}
