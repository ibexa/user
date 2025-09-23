<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting\DateTimeFormat;

use DateTimeInterface;
use IntlDateFormatter;

class Formatter implements FormatterInterface
{
    /** @var \IntlDateFormatter */
    private $formatter;

    /**
     * @param string $locale
     * @param string $timezone
     * @param string $format
     */
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

    /**
     * {@inheritdoc}
     */
    public function format(DateTimeInterface $datetime, ?string $timezone = null): string
    {
        if ($timezone) {
            $currentTimezone = $this->formatter->getTimeZone();
            $this->formatter->setTimeZone($timezone);
        }

        $result = $this->formatter->format($datetime);

        if ($timezone) {
            $this->formatter->setTimeZone($currentTimezone);
        }

        return $result;
    }
}

class_alias(Formatter::class, 'EzSystems\EzPlatformUser\UserSetting\DateTimeFormat\Formatter');
