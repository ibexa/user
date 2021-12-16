<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Form\Data;

use Symfony\Component\Validator\Constraints as Assert;

class UserPasswordForgotWithLoginData
{
    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $login;

    /**
     * @param string|null $login
     */
    public function __construct(?string $login = null)
    {
        $this->login = $login;
    }

    /**
     * @param string|null $login
     */
    public function setLogin(?string $login): void
    {
        $this->login = $login;
    }

    /**
     * @return string|null
     */
    public function getLogin(): ?string
    {
        return $this->login;
    }
}

class_alias(UserPasswordForgotWithLoginData::class, 'EzSystems\EzPlatformUser\Form\Data\UserPasswordForgotWithLoginData');
