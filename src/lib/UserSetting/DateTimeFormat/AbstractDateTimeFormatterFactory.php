<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting\DateTimeFormat;

use Ibexa\User\UserSetting\UserSettingService;

abstract class AbstractDateTimeFormatterFactory implements DateTimeFormatterFactoryInterface
{
    protected UserSettingService $userSettingService;

    /**
     * @param \Ibexa\User\UserSetting\UserSettingService $userSettingService
     */
    public function __construct(UserSettingService $userSettingService)
    {
        $this->userSettingService = $userSettingService;
    }

    /**
     * @return string
     */
    abstract protected function getFormat(): string;

    /**
     * {@inheritdoc}
     */
    public function getFormatter(): FormatterInterface
    {
        $language = $this->userSettingService->getUserSetting('language')->value;
        $timezone = $this->userSettingService->getUserSetting('timezone')->value;
        $format = $this->getFormat();

        return new Formatter(
            $language,
            $timezone,
            $format
        );
    }
}
