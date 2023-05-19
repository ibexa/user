<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Validator\Constraints;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class EmailInvitation extends Constraint implements TranslationContainerInterface
{
    public string $message = 'ibexa.user.invitation.user_with_email_exists';

    public static function getTranslationMessages()
    {
        return [
            Message::create('ibexa.user.invitation.user_with_email_exists', 'validators')
                ->setDesc("The email '%email%' is already in your member list."),
        ];
    }
}
