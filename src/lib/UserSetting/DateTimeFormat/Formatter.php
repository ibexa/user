<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting\DateTimeFormat;

use DateTimeInterface;
use IntlDateFormatter;
use LogicException;

readonly class Formatter implements FormatterInterface
{
    private IntlDateFormatter $formatter;

    public function __construct(string $locale, string $timezone, string $format)
    {
        $this->formatter = new IntlDateFormatter(
            $locale,
            IntlDateFormatter::LONG,
            IntlDateFormatter::LONG,
            $timezone,
            null,
            $format
        );
    }

    public function format(DateTimeInterface $datetime, ?string $timezone = null): string
    {
        if ($timezone) {
            $currentTimezone = $this->formatter->getTimeZone();
            $this->formatter->setTimeZone($timezone);
        }

        $result = $this->formatter->format($datetime);
        if (false === $result) {
            throw new LogicException('Failed to format date time');
        }

        if ($timezone) {
            $this->formatter->setTimeZone($currentTimezone);
        }

        return $result;
    }
}
