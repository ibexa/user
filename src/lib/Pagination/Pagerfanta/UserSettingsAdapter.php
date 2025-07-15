<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Pagination\Pagerfanta;

use Ibexa\User\UserSetting\UserSettingService;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * @phpstan-implements \Pagerfanta\Adapter\AdapterInterface<\Ibexa\User\UserSetting\UserSetting>
 */
readonly class UserSettingsAdapter implements AdapterInterface
{
    public function __construct(
        private UserSettingService $userSettingService
    ) {
    }

    public function getNbResults(): int
    {
        return $this->userSettingService->countUserSettings();
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function getSlice(int $offset, int $length): array
    {
        return $this->userSettingService->loadUserSettings($offset, $length);
    }
}
