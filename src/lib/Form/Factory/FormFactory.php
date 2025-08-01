<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Form\Factory;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\User\Form\Data\UserPasswordChangeData;
use Ibexa\User\Form\Data\UserPasswordForgotData;
use Ibexa\User\Form\Data\UserPasswordForgotWithLoginData;
use Ibexa\User\Form\Data\UserPasswordResetData;
use Ibexa\User\Form\Data\UserSettingUpdateData;
use Ibexa\User\Form\Type\UserPasswordChangeType;
use Ibexa\User\Form\Type\UserPasswordForgotType;
use Ibexa\User\Form\Type\UserPasswordForgotWithLoginType;
use Ibexa\User\Form\Type\UserPasswordResetType;
use Ibexa\User\Form\Type\UserSettingUpdateType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Util\StringUtil;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FormFactory
{
    public function __construct(
        protected FormFactoryInterface $formFactory,
        protected UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function changeUserPassword(
        ContentType $contentType,
        UserPasswordChangeData $data = null,
        ?string $name = null,
        ?User $user = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(UserPasswordChangeType::class);

        return $this->formFactory->createNamed(
            $name,
            UserPasswordChangeType::class,
            $data,
            [
                'content_type' => $contentType,
                'user' => $user,
            ]
        );
    }

    /**
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function forgotUserPassword(
        UserPasswordForgotData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(UserPasswordForgotType::class);

        return $this->formFactory->createNamed($name, UserPasswordForgotType::class, $data);
    }

    /**
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function forgotUserPasswordWithLogin(
        UserPasswordForgotWithLoginData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(UserPasswordForgotWithLoginType::class);

        return $this->formFactory->createNamed($name, UserPasswordForgotWithLoginType::class, $data);
    }

    /**
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function resetUserPassword(
        UserPasswordResetData $data = null,
        ?string $name = null,
        ?ContentType $contentType = null,
        ?User $user = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(UserPasswordResetType::class);

        $userContentType = $contentType ?? $data->getContentType();

        return $this->formFactory->createNamed(
            $name,
            UserPasswordResetType::class,
            $data,
            [
                'content_type' => $userContentType,
                'user' => $user,
            ]
        );
    }

    public function updateUserSetting(
        string $userSettingIdentifier,
        UserSettingUpdateData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(UserSettingUpdateType::class);

        return $this->formFactory->createNamed(
            $name,
            UserSettingUpdateType::class,
            $data,
            ['user_setting_group_identifier' => $userSettingIdentifier]
        );
    }
}
