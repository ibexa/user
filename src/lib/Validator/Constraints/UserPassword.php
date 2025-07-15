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
class UserPassword extends Constraint implements TranslationContainerInterface
{
    public string $message = 'ezplatform.change_user_password.not_match';

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            Message::create('ezplatform.change_user_password.not_match', 'validators')
                ->setDesc('Current password is incorrect.'),
        ];
    }
}
