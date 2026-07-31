<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Password;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\User\Password\PasswordRequirement;
use Ibexa\Contracts\User\Password\PasswordRequirementsResolverInterface;
use Ibexa\Core\FieldType\User\Type as UserType;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class PasswordRequirementsResolver implements PasswordRequirementsResolverInterface, TranslationContainerInterface
{
    public function getRequirements(ContentType $contentType): array
    {
        $fieldDefinition = $contentType->getFirstFieldDefinitionOfType(UserType::FIELD_TYPE_IDENTIFIER);
        if ($fieldDefinition === null) {
            return [];
        }

        $constraints = $fieldDefinition->getValidatorConfiguration()['PasswordValueValidator'] ?? [];
        $requirements = [];

        $minLength = (int)($constraints['minLength'] ?? 0);
        if ($minLength > 0) {
            $requirements[] = new PasswordRequirement(PasswordRequirement::MIN_LENGTH, ['%length%' => $minLength]);
        }

        if (!empty($constraints['requireAtLeastOneUpperCaseCharacter'])) {
            $requirements[] = new PasswordRequirement(PasswordRequirement::UPPER_CASE);
        }

        if (!empty($constraints['requireAtLeastOneLowerCaseCharacter'])) {
            $requirements[] = new PasswordRequirement(PasswordRequirement::LOWER_CASE);
        }

        if (!empty($constraints['requireAtLeastOneNumericCharacter'])) {
            $requirements[] = new PasswordRequirement(PasswordRequirement::NUMERIC);
        }

        if (!empty($constraints['requireAtLeastOneNonAlphanumericCharacter'])) {
            $requirements[] = new PasswordRequirement(PasswordRequirement::NON_ALPHANUMERIC);
        }

        // A configured password TTL implies this rule, {@see \Ibexa\Core\FieldType\User\Type::isNewPasswordRequired()}
        if (
            !empty($constraints['requireNewPassword'])
            || (int)($fieldDefinition->getFieldSettings()[UserType::PASSWORD_TTL_SETTING] ?? 0) > 0
        ) {
            $requirements[] = new PasswordRequirement(PasswordRequirement::NEW_PASSWORD);
        }

        if (!empty($constraints['requireNotCompromisedPassword'])) {
            $requirements[] = new PasswordRequirement(PasswordRequirement::NOT_COMPROMISED);
        }

        return $requirements;
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        $descriptions = [
            PasswordRequirement::MIN_LENGTH => 'At least %length% characters long',
            PasswordRequirement::UPPER_CASE => 'At least one uppercase letter',
            PasswordRequirement::LOWER_CASE => 'At least one lowercase letter',
            PasswordRequirement::NUMERIC => 'At least one number',
            PasswordRequirement::NON_ALPHANUMERIC => 'At least one special character',
            PasswordRequirement::NEW_PASSWORD => 'Different from your current password',
            PasswordRequirement::NOT_COMPROMISED => 'Not found in known data breaches',
        ];

        $messages = [];
        foreach ($descriptions as $identifier => $description) {
            $messages[] = Message::create(
                (new PasswordRequirement($identifier))->getTranslationKey(),
                'ibexa_password_requirements'
            )->setDesc($description);
        }

        return $messages;
    }
}
