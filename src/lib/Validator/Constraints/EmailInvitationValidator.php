<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Validator\Constraints;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\UserService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EmailInvitationValidator extends ConstraintValidator
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $email The value that should be validated
     * @param \Symfony\Component\Validator\Constraint $constraint The constraint for the validation
     */
    public function validate(mixed $email, Constraint $constraint): void
    {
        assert(is_string($email));

        try {
            $this->userService->loadUserByEmail($email);
            $this->context->addViolation($constraint->message, ['%email%' => $email]);
        } catch (NotFoundException) {
            // Do nothing
        }
    }
}
