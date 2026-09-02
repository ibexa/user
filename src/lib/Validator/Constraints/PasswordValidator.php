<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Validator\Constraints;

use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\PasswordValidationContext;
use Ibexa\User\Password\PasswordRequirement;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PasswordValidator extends ConstraintValidator
{
    /**
     * Message templates from {@see \Ibexa\Core\Repository\Validator\UserPasswordValidator}
     * and {@see \Ibexa\Core\Repository\User\PasswordValidator}.
     */
    private const array REQUIREMENT_CODE_MAP = [
        'User password must be at least %length% characters long' => PasswordRequirement::MIN_LENGTH,
        'User password must include at least one upper case letter' => PasswordRequirement::UPPER_CASE,
        'User password must include at least one lower case letter' => PasswordRequirement::LOWER_CASE,
        'User password must include at least one number' => PasswordRequirement::NUMERIC,
        'User password must include at least one special character' => PasswordRequirement::NON_ALPHANUMERIC,
        'New password cannot be the same as old password' => PasswordRequirement::NEW_PASSWORD,
        'This password has been leaked in a data breach, it must not be used. Please use another password.' => PasswordRequirement::NOT_COMPROMISED,
    ];

    public function __construct(
        private readonly UserService $userService
    ) {
    }

    /**
     * @param \Ibexa\User\Validator\Constraints\Password $constraint
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentValidationException
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!is_string($value) || empty($value)) {
            return;
        }

        $passwordValidationContext = new PasswordValidationContext([
            'contentType' => $constraint->contentType,
            'user' => $constraint->user,
        ]);

        $validationErrors = $this->userService->validatePassword(
            $value,
            $passwordValidationContext
        );

        foreach ($validationErrors as $validationError) {
            $message = $validationError->getTranslatableMessage();
            $messageTemplate = $message->getMessageTemplate();

            $violationBuilder = $this->context
                ->buildViolation($messageTemplate)
                ->setParameters($message->getValues());

            $code = self::REQUIREMENT_CODE_MAP[$messageTemplate] ?? null;
            if ($code !== null) {
                $violationBuilder->setCode($code);
            }

            $violationBuilder->addViolation();
        }
    }
}
