<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Validator\Constraints;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class EmailInvitation extends Constraint implements TranslationContainerInterface
{
    public string $message = 'ibexa.user.invitation.user_with_email_exists';

    /**
     * @param array<string, mixed>|null $options Deprecated, use named arguments instead
     * @param array<string>|null $groups
     */
    #[HasNamedArguments]
    public function __construct(
        ?array $options = null,
        ?string $message = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        if (null !== $options) {
            trigger_deprecation(
                'ibexa/user',
                '6.0',
                'Passing an options array to "%s" is deprecated, use named arguments instead.',
                static::class
            );

            $message ??= $options['message'] ?? null;
            $groups ??= $options['groups'] ?? null;
            $payload ??= $options['payload'] ?? null;
        }

        parent::__construct(null, $groups, $payload);

        $this->message = $message ?? $this->message;
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create('ibexa.user.invitation.user_with_email_exists', 'validators')
                ->setDesc("The email '%email%' is already in your member list."),
        ];
    }
}
