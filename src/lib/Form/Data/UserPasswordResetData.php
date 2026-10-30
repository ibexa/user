<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Form\Data;

use Symfony\Component\Validator\Constraints as Assert;

class UserPasswordResetData
{
    public function __construct(
        #[Assert\NotBlank]
        private ?string $newPassword = null
    ) {}

    public function setNewPassword(?string $newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }
}
