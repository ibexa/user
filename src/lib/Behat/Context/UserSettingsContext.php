<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Behat\Context;

use Behat\Behat\Context\Context;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\User\UserSetting\UserSettingService;

class UserSettingsContext implements Context
{
    public function __construct(
        private readonly UserSettingService $userSettingService,
        private readonly PermissionResolver $permissionResolver,
        private readonly UserService $userService
    ) {
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
