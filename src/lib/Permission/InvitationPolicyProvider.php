<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Permission;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigBuilderInterface;
use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class InvitationPolicyProvider implements PolicyProviderInterface, TranslationContainerInterface
{
    public function addPolicies(ConfigBuilderInterface $configBuilder)
    {
        $configBuilder->addConfig([
            'user' => [
                'invite' => ['UserPermissions'],
            ],
        ]);
    }

    public static function getTranslationMessages()
    {
        return [
            (new Message('role.policy.user', 'forms'))->setDesc('User'),
            (new Message('role.policy.user.all_functions', 'forms'))->setDesc('User / All functions'),
            (new Message('role.policy.user.invite', 'forms'))->setDesc('User / Invite'),
        ];
    }
}
