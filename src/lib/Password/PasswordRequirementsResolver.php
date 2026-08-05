<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Password;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\FieldType\User\Type as UserType;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final readonly class PasswordRequirementsResolver implements TranslationContainerInterface
{
    /**
     * Built-in password rules: constraint key in the core PasswordValueValidator
     * schema => requirement identifier and its English label. Adding a rule here
     * is all that is needed — translations are generated from this list.
     */
    /** Constraint key in the core PasswordValueValidator schema, unlike {@see PasswordRequirement::MIN_LENGTH}. */
    private const string MIN_LENGTH_CONSTRAINT = 'minLength';

    private const array RULES = [
        self::MIN_LENGTH_CONSTRAINT => [PasswordRequirement::MIN_LENGTH, 'At least %length% characters long'],
        'requireAtLeastOneUpperCaseCharacter' => [PasswordRequirement::UPPER_CASE, 'At least one uppercase letter'],
        'requireAtLeastOneLowerCaseCharacter' => [PasswordRequirement::LOWER_CASE, 'At least one lowercase letter'],
        'requireAtLeastOneNumericCharacter' => [PasswordRequirement::NUMERIC, 'At least one number'],
        'requireAtLeastOneNonAlphanumericCharacter' => [PasswordRequirement::NON_ALPHANUMERIC, 'At least one special character'],
        'requireNewPassword' => [PasswordRequirement::NEW_PASSWORD, 'Different from your current password'],
        'requireNotCompromisedPassword' => [PasswordRequirement::NOT_COMPROMISED, 'Not found in known data breaches'],
    ];

    /**
     * @return \Ibexa\User\Password\PasswordRequirement[]
     */
    public function getRequirements(ContentType $contentType): array
    {
        $fieldDefinition = $contentType->getFirstFieldDefinitionOfType(UserType::FIELD_TYPE_IDENTIFIER);
        if ($fieldDefinition === null) {
            return [];
        }

        $constraints = $fieldDefinition->getValidatorConfiguration()['PasswordValueValidator'] ?? [];
        $fieldSettings = $fieldDefinition->getFieldSettings();

        $requirements = [];
        foreach (self::RULES as $constraintKey => [$identifier]) {
            if ($this->isEnabled($constraintKey, $constraints, $fieldSettings)) {
                $requirements[] = new PasswordRequirement($identifier, $this->getParameters($constraintKey, $constraints));
            }
        }

        return $requirements;
    }

    /**
     * @param array<string, mixed> $constraints
     * @param array<string, mixed> $fieldSettings
     */
    private function isEnabled(string $constraintKey, array $constraints, array $fieldSettings): bool
    {
        return match ($constraintKey) {
            self::MIN_LENGTH_CONSTRAINT => (int)($constraints[self::MIN_LENGTH_CONSTRAINT] ?? 0) > 0,
            // A configured password TTL implies this rule, {@see \Ibexa\Core\FieldType\User\Type::isNewPasswordRequired()}
            'requireNewPassword' => !empty($constraints['requireNewPassword'])
                || (int)($fieldSettings[UserType::PASSWORD_TTL_SETTING] ?? 0) > 0,
            // Covers boolean on/off flags only; a numeric rule needs its own arm, like minLength above
            default => !empty($constraints[$constraintKey]),
        };
    }

    /**
     * @param array<string, mixed> $constraints
     *
     * @return array<string, scalar>
     */
    private function getParameters(string $constraintKey, array $constraints): array
    {
        return $constraintKey === self::MIN_LENGTH_CONSTRAINT
            ? ['%length%' => (int)$constraints[self::MIN_LENGTH_CONSTRAINT]]
            : [];
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        $messages = [];
        foreach (self::RULES as [$identifier, $label]) {
            $messages[] = Message::create(
                (new PasswordRequirement($identifier))->getTranslationKey(),
                'ibexa_password_requirements'
            )->setDesc($label);
        }

        return $messages;
    }
}
