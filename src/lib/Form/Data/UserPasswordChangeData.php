<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Form\Data;

use Ibexa\User\Validator\Constraints as UserAssert;
use Symfony\Component\Validator\Constraints as Assert;

class UserPasswordChangeData
{
    /**
     * @UserAssert\UserPassword()
     *
     * @var string
     */
    #[Assert\NotBlank]
    private $oldPassword;

    /**
     * @var string
     */
    #[Assert\NotBlank]
    private $newPassword;

    /**
     * @param string|null $oldPassword
     * @param string|null $newPassword
     */
    public function __construct(?string $oldPassword = null, ?string $newPassword = null)
    {
        $this->oldPassword = $oldPassword;
        $this->newPassword = $newPassword;
    }

    /**
     * @param string|null $oldPassword
     */
    public function setOldPassword(?string $oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }

    /**
     * @param string|null $newPassword
     */
    public function setNewPassword(?string $newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    /**
     * @return string|null
     */
    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    /**
     * @return string|null
     */
    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }
}
