<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting\DateTimeFormat;

use DateTimeInterface;

interface FormatterInterface
{
    public function format(DateTimeInterface $datetime, ?string $timezone = null): string;
}
