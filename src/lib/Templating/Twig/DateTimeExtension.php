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
use Ibexa\User\UserSetting\Setting\DateTimeFormatSerializer;
use RuntimeException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DateTimeExtension extends AbstractExtension
{
    private DateTimeFormatSerializer $dateTimeFormatSerializer;

    private FormatterInterface $shortDateTimeFormatter;

    private FormatterInterface $shortDateFormatter;

    private FormatterInterface $shortTimeFormatter;

    private FormatterInterface $fullDateTimeFormatter;

    private FormatterInterface $fullDateFormatter;

    private FormatterInterface $fullTimeFormatter;

    /**
     * @param \Ibexa\User\UserSetting\Setting\DateTimeFormatSerializer $dateTimeFormatSerializer
     * @param \Ibexa\User\UserSetting\DateTimeFormat\FormatterInterface $shortDateTimeFormatter
     * @param \Ibexa\User\UserSetting\DateTimeFormat\FormatterInterface $shortDateFormatter
     * @param \Ibexa\User\UserSetting\DateTimeFormat\FormatterInterface $shortTimeFormatter
     * @param \Ibexa\User\UserSetting\DateTimeFormat\FormatterInterface $fullDateTimeFormatter
     * @param \Ibexa\User\UserSetting\DateTimeFormat\FormatterInterface $fullDateFormatter
     * @param \Ibexa\User\UserSetting\DateTimeFormat\FormatterInterface $fullTimeFormatter
     */
    public function __construct(
        DateTimeFormatSerializer $dateTimeFormatSerializer,
        FormatterInterface $shortDateTimeFormatter,
        FormatterInterface $shortDateFormatter,
        FormatterInterface $shortTimeFormatter,
        FormatterInterface $fullDateTimeFormatter,
        FormatterInterface $fullDateFormatter,
        FormatterInterface $fullTimeFormatter
    ) {
        $this->dateTimeFormatSerializer = $dateTimeFormatSerializer;
        $this->shortDateTimeFormatter = $shortDateTimeFormatter;
        $this->shortDateFormatter = $shortDateFormatter;
        $this->shortTimeFormatter = $shortTimeFormatter;
        $this->fullDateTimeFormatter = $fullDateTimeFormatter;
        $this->fullDateFormatter = $fullDateFormatter;
        $this->fullTimeFormatter = $fullTimeFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'ibexa_short_datetime',
                function ($date, $timezone = null): string {
                    return $this->format($this->shortDateTimeFormatter, $date, $timezone);
                }
            ),
            new TwigFilter(
                'ibexa_short_date',
                function ($date, $timezone = null): string {
                    return $this->format($this->shortDateFormatter, $date, $timezone);
                }
            ),
            new TwigFilter(
                'ibexa_short_time',
                function ($date, $timezone = null): string {
                    return $this->format($this->shortTimeFormatter, $date, $timezone);
                }
            ),
            new TwigFilter(
                'ibexa_full_datetime',
                function ($date, $timezone = null): string {
                    return $this->format($this->fullDateTimeFormatter, $date, $timezone);
                }
            ),
            new TwigFilter(
                'ibexa_full_date',
                function ($date, $timezone = null): string {
                    return $this->format($this->fullDateFormatter, $date, $timezone);
                }
            ),
            new TwigFilter(
                'ibexa_full_time',
                function ($date, $timezone = null): string {
                    return $this->format($this->fullTimeFormatter, $date, $timezone);
                }
            ),
        ];
    }

    /**
     * @param \Ibexa\User\UserSetting\DateTimeFormat\FormatterInterface $formatter
     * @param mixed|null $date
     * @param string|null $timezone
     *
     * @return string
     *
     * @throws \Exception
     */
    public function format(FormatterInterface $formatter, $date = null, string $timezone = null): string
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
