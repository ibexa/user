<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting\DateTimeFormat;

use Ibexa\User\UserSetting\Setting\DateTimeFormatSerializer;
use Ibexa\User\UserSetting\UserSettingService;

class ShortDateFormatterFactory extends AbstractDateTimeFormatterFactory implements DateTimeFormatterFactoryInterface
{
    public function __construct(
        UserSettingService $userSettingService,
        private readonly DateTimeFormatSerializer $dateTimeFormatSerializer
    ) {
        parent::__construct($userSettingService);
    }

    protected function getFormat(): string
    {
        return (string)$this->dateTimeFormatSerializer->deserialize(
            $this->userSettingService->getUserSetting('short_datetime_format')->value
        )->getDateFormat();
    }
}
