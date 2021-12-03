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
    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface */
    private $tokenStorage;

    /**
     * @param \Ibexa\Contracts\Core\Repository\UserService $userService
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tokenStorage
     */
    public function __construct(UserService $userService, TokenStorageInterface $tokenStorage)
    {
        $this->userService = $userService;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Checks if the passed password exists for logged user.
     *
     * @param string $password The password that should be validated
     * @param \Symfony\Component\Validator\Constraint|\Ibexa\User\Validator\Constraints\UserPassword $constraint The constraint for the validation
     */
    public function validate($password, Constraint $constraint)
    {
        if (null === $password || '' === $password) {
            $this->context->addViolation($constraint->message);

            return;
        }

        $user = $this->tokenStorage->getToken()->getUser()->getAPIUser();

        if (!$user instanceof User) {
            throw new ConstraintDefinitionException('The User object must implement the UserReference interface.');
        }

        if (!$this->userService->checkUserCredentials($user, $password)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}

class_alias(UserPasswordValidator::class, 'EzSystems\EzPlatformUser\Validator\Constraints\UserPasswordValidator');
