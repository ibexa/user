<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\User\Behat\Context;

use Behat\Behat\Context\Context;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\User\UserSetting\UserSettingService;

class UserSettingsContext implements Context
{
    private UserSettingService $userSettingService;

    private PermissionResolver $permissionResolver;

    private UserService $userService;

    public function __construct(UserSettingService $userSettingService, PermissionResolver $permissionResolver, UserService $userService)
    {
        $this->userSettingService = $userSettingService;
        $this->permissionResolver = $permissionResolver;
        $this->userService = $userService;
    }

    /**
     * @When I set autosave interval value to :autosaveInterval for user :userLogin
     */
    public function iSetAutosaveDraftIntervalValue(string $autosaveInterval, string $userLogin): void
    {
        $currentUser = $this->permissionResolver->getCurrentUserReference();
        $user = $this->userService->loadUserByLogin($userLogin);
        $this->permissionResolver->setCurrentUserReference($user);
        $this->userSettingService->setUserSetting('autosave_interval', $autosaveInterval);
        $this->permissionResolver->setCurrentUserReference($currentUser);
    }
}
