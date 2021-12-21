<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Pagination\Pagerfanta;

use Ibexa\User\UserSetting\UserSettingService;

class GroupedUserSettingsAdapter implements \Pagerfanta\Adapter\AdapterInterface
{
    private UserSettingService $userSettingService;

    public function __construct(UserSettingService $userSettingService)
    {
        $this->userSettingService = $userSettingService;
    }

    public function getNbResults(): int
    {
        return $this->userSettingService->countGroupedUserSettings();
    }

    public function getSlice($offset, $length): array
    {
        return $this->userSettingService->loadGroupedUserSettings($offset, $length);
    }
}
