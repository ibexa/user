<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Permission;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class UserPermissionsLimitation extends Limitation implements TranslationContainerInterface
{
    public const string IDENTIFIER = 'UserPermissions';

    public function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(
                'policy.limitation.identifier.userpermissions',
                'ibexa_content_forms_policies'
            ))->setDesc('Roles and/or User Groups'),
        ];
    }
}
