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
    public function __construct(
        /**
         * @UserAssert\UserPassword()
         */
        #[Assert\NotBlank]
        private ?string $oldPassword = null,
        #[Assert\NotBlank]
        private ?string $newPassword = null
    ) {
    }

    public function setOldPassword(?string $oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }

    public function setNewPassword(?string $newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }
}
