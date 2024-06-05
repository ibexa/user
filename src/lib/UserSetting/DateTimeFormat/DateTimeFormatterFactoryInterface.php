<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting\DateTimeFormat;

interface DateTimeFormatterFactoryInterface
{
    /**
     * @return \Ibexa\User\UserSetting\DateTimeFormat\FormatterInterface
     */
    public function getFormatter(): FormatterInterface;
}
