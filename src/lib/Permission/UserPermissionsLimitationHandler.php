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
    public const string USER_GROUP_PREFIX = 'UserGroup_';
    public const string ROLE_PREFIX = 'Role_';

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
            'user_groups' => [],
        ];
        foreach ($policy->limitations[UserPermissionsLimitation::IDENTIFIER] as $value) {
            if (str_starts_with((string) $value, self::ROLE_PREFIX)) {
                $values['roles'][] = (int) substr((string) $value, strlen(self::ROLE_PREFIX));
            }
            if (str_starts_with((string) $value, self::USER_GROUP_PREFIX)) {
                $values['user_groups'][] = (int) substr((string) $value, strlen(self::USER_GROUP_PREFIX));
            }
        }
        $policy->limitations[UserPermissionsLimitation::IDENTIFIER] = $values;
    }
}
