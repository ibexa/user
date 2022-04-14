<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\User\Permission;

use Ibexa\Contracts\Core\Persistence\User\Policy;
use Ibexa\Core\Persistence\Legacy\User\Role\LimitationHandler;


class UserPermissionsLimitationHandler extends LimitationHandler
{
    public const USER_GROUP_PREFIX = 'UserGroup_';
    public const ROLE_PREFIX = 'Role_';

    /**
     * Translate API STATE limitation to Legacy StateGroup_<identifier> limitations.
     */
    public function toLegacy(Policy $policy): void
    {
        if ($policy->limitations !== '*' && isset($policy->limitations[UserPermissionsLimitation::IDENTIFIER])) {
            foreach ($policy->limitations[UserPermissionsLimitation::IDENTIFIER]['roles'] as $roleId) {
                $policy->limitations[UserPermissionsLimitation::IDENTIFIER][] = self::ROLE_PREFIX . $roleId;
            }
            foreach ($policy->limitations[UserPermissionsLimitation::IDENTIFIER]['user_groups'] as $groupId) {
                $policy->limitations[UserPermissionsLimitation::IDENTIFIER][] = self::USER_GROUP_PREFIX . $groupId;
            }
            unset($policy->limitations[UserPermissionsLimitation::IDENTIFIER]['roles']);
            unset($policy->limitations[UserPermissionsLimitation::IDENTIFIER]['user_groups']);
        }
    }

    /**
     * Translate Legacy StateGroup_<identifier> limitations to API STATE limitation.
     */
    public function toSPI(Policy $policy): void
    {
        if ($policy->limitations === '*' || empty($policy->limitations)) {
            return;
        }
        if (!isset($policy->limitations[UserPermissionsLimitation::IDENTIFIER])) {
            return;
        }
        $values = [
            'roles' => [],
            'user_groups' => []
        ];
        foreach ($policy->limitations[UserPermissionsLimitation::IDENTIFIER] as $value) {
            if (strpos($value, self::ROLE_PREFIX) === 0) {
                $values['roles'][] = (int) substr($value, strlen(self::ROLE_PREFIX));
            }
            if (strpos($value, self::USER_GROUP_PREFIX) === 0) {
                $values['user_groups'][] = (int) substr($value, strlen(self::USER_GROUP_PREFIX));
            }
        }
        $policy->limitations[UserPermissionsLimitation::IDENTIFIER] = $values;
    }
}
