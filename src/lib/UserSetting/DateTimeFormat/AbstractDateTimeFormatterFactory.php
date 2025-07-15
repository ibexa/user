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
    public function __construct(
        protected UserSettingService $userSettingService
    ) {
    }

    abstract protected function getFormat(): string;

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
