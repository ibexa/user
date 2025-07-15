<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Form\Data;

use Symfony\Component\Validator\Constraints as Assert;

class UserPasswordForgotData
{
    public function __construct(
        #[Assert\NotBlank]
        private ?string $email = null
    ) {
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
}
