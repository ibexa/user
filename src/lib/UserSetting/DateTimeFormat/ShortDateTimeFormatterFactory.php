<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting\DateTimeFormat;

use Ibexa\User\UserSetting\Setting\DateTimeFormatSerializer;
use Ibexa\User\UserSetting\UserSettingService;

class ShortDateTimeFormatterFactory extends AbstractDateTimeFormatterFactory implements DateTimeFormatterFactoryInterface
{
    /** @var \Ibexa\User\UserSetting\Setting\DateTimeFormatSerializer */
    private $dateTimeFormatSerializer;

    /**
     * @param \Ibexa\User\UserSetting\UserSettingService $userSettingService
     * @param \Ibexa\User\UserSetting\Setting\DateTimeFormatSerializer $dateTimeFormatSerializer
     */
    public function __construct(
        UserSettingService $userSettingService,
        DateTimeFormatSerializer $dateTimeFormatSerializer
    ) {
        parent::__construct($userSettingService);
        $this->dateTimeFormatSerializer = $dateTimeFormatSerializer;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormat(): string
    {
        return (string)$this->dateTimeFormatSerializer->deserialize(
            $this->userSettingService->getUserSetting('short_datetime_format')->value
        );
    }
}

class_alias(ShortDateTimeFormatterFactory::class, 'EzSystems\EzPlatformUser\UserSetting\DateTimeFormat\ShortDateTimeFormatterFactory');
