<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Validator\Constraints;

use Ibexa\ContentForms\Validator\ValidationErrorsProcessor;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\PasswordValidationContext;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PasswordValidator extends ConstraintValidator
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param \Ibexa\User\Validator\Constraints\Password $constraint
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentValidationException
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!\is_string($value) || empty($value)) {
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
        if (!empty($validationErrors)) {
            $validationErrorsProcessor = $this->createValidationErrorsProcessor();
            $validationErrorsProcessor->processValidationErrors($validationErrors);
        }
    }

    protected function createValidationErrorsProcessor(): ValidationErrorsProcessor
    {
        return new ValidationErrorsProcessor($this->context);
    }
}
