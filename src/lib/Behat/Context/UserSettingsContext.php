<?php


namespace Ibexa\User\Behat\Context;


use Behat\Behat\Context\Context;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\User\UserSetting\UserSettingService;

class UserSettingsContext implements Context
{
    /**
     * @var UserSettingService
     */
    private $userSettingService;
    /**
     * @var PermissionResolver
     */
    private $permissionResolver;
    /**
     * @var UserService
     */
    private $userService;

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