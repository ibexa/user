<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\User\Password;

final readonly class PasswordRequirement
{
    public const string MIN_LENGTH = 'min_length';
    public const string UPPER_CASE = 'upper_case';
    public const string LOWER_CASE = 'lower_case';
    public const string NUMERIC = 'numeric';
    public const string NON_ALPHANUMERIC = 'non_alphanumeric';
    public const string NEW_PASSWORD = 'new_password';
    public const string NOT_COMPROMISED = 'not_compromised';

    private const string TRANSLATION_KEY_PREFIX = 'password_requirement.';

    /**
     * @param array<string, scalar> $parameters
     */
    public function __construct(
        private string $identifier,
        private array $parameters = []
    ) {
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return array<string, scalar>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getTranslationKey(): string
    {
        return self::TRANSLATION_KEY_PREFIX . $this->identifier;
    }
}
