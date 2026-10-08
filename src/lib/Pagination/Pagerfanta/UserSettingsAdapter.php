<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Pagination\Pagerfanta;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\User\UserSetting\UserSettingService;
use Pagerfanta\Adapter\AdapterInterface;

class UserSettingsAdapter implements AdapterInterface
{
    /** @var UserSettingService */
    private $userSettingService;

    /**
     * @param UserSettingService $userSettingService
     */
    public function __construct(UserSettingService $userSettingService)
    {
        $this->userSettingService = $userSettingService;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    public function getNbResults(): int
    {
        return $this->userSettingService->countUserSettings();
    }

    /**
     * {@inheritdoc}
     *
     * @throws UnauthorizedException
     */
    public function getSlice(
        $offset,
        $length
    ): array {
        return $this->userSettingService->loadUserSettings($offset, $length);
    }
}

class_alias(UserSettingsAdapter::class, 'EzSystems\EzPlatformUser\Pagination\Pagerfanta\UserSettingsAdapter');
