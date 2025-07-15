<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Validator\Constraints;

use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

/**
 * Will check if logged user and password are match.
 */
class UserPasswordValidator extends ConstraintValidator
{
    public function __construct(
        private readonly UserService $userService,
        private readonly TokenStorageInterface $tokenStorage
    ) {
    }

    /**
     * Checks if the passed password exists for logged user.
     */
    public function validate(mixed $password, Constraint $constraint): void
    {
        if (null === $password || '' === $password) {
            $this->context->addViolation($constraint->message);

            return;
        }

        assert(is_string($password));

        $user = $this->tokenStorage->getToken()->getUser()->getAPIUser();

        if (!$user instanceof User) {
            throw new ConstraintDefinitionException('The User object must implement the UserReference interface.');
        }

        if (!$this->userService->checkUserCredentials($user, $password)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
