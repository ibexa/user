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
    #[Assert\NotBlank]
    private ?string $email;

    /**
     * @param string|null $email
     */
    public function __construct(?string $email = null)
    {
        $this->email = $email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }
}
