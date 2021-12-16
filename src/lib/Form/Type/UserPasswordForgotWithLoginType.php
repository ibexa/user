<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Form\Type;

use Ibexa\User\Form\Data\UserPasswordForgotWithLoginData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPasswordForgotWithLoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('login', TextType::class, [
                'required' => true,
                'label' => /** @Desc("Enter your login:") */ 'ezplatform.forgot_user_password.login',
            ])
            ->add(
                'reset',
                SubmitType::class,
                ['label' => /** @Desc("Reset") */ 'ezplatform.forgot_user_password.reset']
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserPasswordForgotWithLoginData::class,
            'translation_domain' => 'forms',
        ]);
    }
}

class_alias(UserPasswordForgotWithLoginType::class, 'EzSystems\EzPlatformUser\Form\Type\UserPasswordForgotWithLoginType');
