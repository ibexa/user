<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Templating\Twig;

use DateTimeImmutable;
use DateTimeInterface;
use Ibexa\User\UserSetting\DateTimeFormat\FormatterInterface;
use RuntimeException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DateTimeExtension extends AbstractExtension
{
    public function __construct(
        private readonly FormatterInterface $shortDateTimeFormatter,
        private readonly FormatterInterface $shortDateFormatter,
        private readonly FormatterInterface $shortTimeFormatter,
        private readonly FormatterInterface $fullDateTimeFormatter,
        private readonly FormatterInterface $fullDateFormatter,
        private readonly FormatterInterface $fullTimeFormatter
    ) {
    }

    #[\Override]
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'ibexa_short_datetime',
                fn ($date, $timezone = null): string => $this->format($this->shortDateTimeFormatter, $date, $timezone)
            ),
            new TwigFilter(
                'ibexa_short_date',
                fn ($date, $timezone = null): string => $this->format($this->shortDateFormatter, $date, $timezone)
            ),
            new TwigFilter(
                'ibexa_short_time',
                fn ($date, $timezone = null): string => $this->format($this->shortTimeFormatter, $date, $timezone)
            ),
            new TwigFilter(
                'ibexa_full_datetime',
                fn ($date, $timezone = null): string => $this->format($this->fullDateTimeFormatter, $date, $timezone)
            ),
            new TwigFilter(
                'ibexa_full_date',
                fn ($date, $timezone = null): string => $this->format($this->fullDateFormatter, $date, $timezone)
            ),
            new TwigFilter(
                'ibexa_full_time',
                fn ($date, $timezone = null): string => $this->format($this->fullTimeFormatter, $date, $timezone)
            ),
        ];
    }

    /**
     * @throws \Exception
     */
    public function format(FormatterInterface $formatter, mixed $date = null, ?string $timezone = null): string
    {
        if ($date === null) {
            $date = new DateTimeImmutable();
        }

        if (is_int($date)) {
            $date = new DateTimeImmutable('@' . $date);
        }

        if (!$date instanceof DateTimeInterface) {
            throw new RuntimeException('The date argument passed to the format function must be an int or a DateTimeInterface');
        }

        return $formatter->format($date, $timezone);
    }
}
