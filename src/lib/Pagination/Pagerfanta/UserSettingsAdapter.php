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
class UserSettingsAdapter implements AdapterInterface
{
    private UserSettingService $userSettingService;

    /**
     * @param \Ibexa\User\UserSetting\UserSettingService $userSettingService
     */
    public function __construct(UserSettingService $userSettingService)
    {
        $this->userSettingService = $userSettingService;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function getNbResults(): int
    {
        return $this->userSettingService->countUserSettings();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function getSlice($offset, $length): array
    {
        return $this->userSettingService->loadUserSettings($offset, $length);
    }
}
