<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\User\Form\Type\Invitation;

use Ibexa\User\Form\Data\UserInvitationData;
use Ibexa\User\Form\DataTransformer\LimitationValueTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UserInvitationType extends AbstractType
{
    public const LIMITATION_TYPE_NONE = 'none';
    public const LIMITATION_TYPE_SECTION = 'section';
    public const LIMITATION_TYPE_LOCATION = 'location';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => /** @Desc("Enter email") */ 'ibexa.user.invitation.email',
            ])
            ->add('siteaccess', SiteAccessChoiceType::class, [
                'label' => /** @Desc("Select Site") */ 'ibexa.user.invitation.site_access',
            ])
            ->add('role', RoleChoiceType::class, [
                'required' => false,
                'label' => /** @Desc("Select Role") */ 'ibexa.user.invitation.role',
            ])
            ->add('user_group', UserGroupChoiceType::class, [
                'required' => false,
                'label' => /** @Desc("Select Group") */ 'ibexa.user.invitation.group',
            ])
            ->add('limitation_type', ChoiceType::class, [
                'multiple' => false,
                'expanded' => true,
                'choices' => [
                    self::LIMITATION_TYPE_NONE => null,
                    self::LIMITATION_TYPE_SECTION => self::LIMITATION_TYPE_SECTION,
                    self::LIMITATION_TYPE_LOCATION => self::LIMITATION_TYPE_LOCATION,
                ],
            ])
            ->add(
                'sections',
                SectionsChoiceType::class,
                [
                    'required' => false,
                    'multiple' => true,
                    'label' => /** @Desc("Pick Sections") */ 'ibexa.user.invitation.pick_sections',
                ]
            )
            ->add(
                'location_path',
                TextType::class,
                [
                    'required' => false,
                    'label' => /** @Desc("Type subtree root id") */ 'ibexa.user.invitation.location_id',
                ]
            )
            ->add('submit', SubmitType::class, [
                'label' => /** @Desc("Send invitation") */ 'ibexa.user.invitation.send_invitation',
            ]);

        $builder->addModelTransformer(new LimitationValueTransformer());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserInvitationData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
