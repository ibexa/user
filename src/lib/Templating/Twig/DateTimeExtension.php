<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Templating\Twig;

use DateTimeImmutable;
use Ibexa\User\UserSetting\DateTimeFormat\FormatterInterface;
use Ibexa\User\UserSetting\Setting\DateTimeFormatSerializer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DateTimeExtension extends AbstractExtension
{
    /** @var \Ibexa\User\UserSetting\Setting\DateTimeFormatSerializer */
    private $dateTimeFormatSerializer;

    /** @var \Ibexa\User\UserSetting\DateTimeFormat\FormatterInterface */
    private $shortDateTimeFormatter;

    /** @var \Ibexa\User\UserSetting\DateTimeFormat\FormatterInterface */
    private $shortDateFormatter;

    /** @var \Ibexa\User\UserSetting\DateTimeFormat\FormatterInterface */
    private $shortTimeFormatter;

    /** @var \Ibexa\User\UserSetting\DateTimeFormat\FormatterInterface */
    private $fullDateTimeFormatter;

    /** @var \Ibexa\User\UserSetting\DateTimeFormat\FormatterInterface */
    private $fullDateFormatter;

    /** @var \Ibexa\User\UserSetting\DateTimeFormat\FormatterInterface */
    private $fullTimeFormatter;

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
                function ($date, $timezone = null) {
                    return $this->format($this->shortDateTimeFormatter, $date, $timezone);
                }
            ),
            new TwigFilter(
                'ibexa_short_date',
                function ($date, $timezone = null) {
                    return $this->format($this->shortDateFormatter, $date, $timezone);
                }
            ),
            new TwigFilter(
                'ibexa_short_time',
                function ($date, $timezone = null) {
                    return $this->format($this->shortTimeFormatter, $date, $timezone);
                }
            ),
            new TwigFilter(
                'ibexa_full_datetime',
                function ($date, $timezone = null) {
                    return $this->format($this->fullDateTimeFormatter, $date, $timezone);
                }
            ),
            new TwigFilter(
                'ibexa_full_date',
                function ($date, $timezone = null) {
                    return $this->format($this->fullDateFormatter, $date, $timezone);
                }
            ),
            new TwigFilter(
                'ibexa_full_time',
                function ($date, $timezone = null) {
                    return $this->format($this->fullTimeFormatter, $date, $timezone);
                }
            ),
            new TwigFilter(
                'ez_short_datetime',
                function ($date, $timezone = null) {
                    return $this->format($this->shortDateTimeFormatter, $date, $timezone);
                },
                [
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_short_datetime',
                ]
            ),
            new TwigFilter(
                'ez_short_date',
                function ($date, $timezone = null) {
                    return $this->format($this->shortDateFormatter, $date, $timezone);
                },
                [
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_short_date',
                ]
            ),
            new TwigFilter(
                'ez_short_time',
                function ($date, $timezone = null) {
                    return $this->format($this->shortTimeFormatter, $date, $timezone);
                },
                [
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_short_time',
                ]
            ),
            new TwigFilter(
                'ez_full_datetime',
                function ($date, $timezone = null) {
                    return $this->format($this->fullDateTimeFormatter, $date, $timezone);
                },
                [
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_full_datetime',
                ]
            ),
            new TwigFilter(
                'ez_full_date',
                function ($date, $timezone = null) {
                    return $this->format($this->fullDateFormatter, $date, $timezone);
                },
                [
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_full_date',
                ]
            ),
            new TwigFilter(
                'ez_full_time',
                function ($date, $timezone = null) {
                    return $this->format($this->fullTimeFormatter, $date, $timezone);
                },
                [
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_full_time',
                ]
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
    public function format(FormatterInterface $formatter, $date = null, ?string $timezone = null): string
    {
        if ($date === null) {
            $date = new DateTimeImmutable();
        }

        if (\is_int($date)) {
            $date = new DateTimeImmutable('@' . $date);
        }

        if (!$date instanceof \DateTimeInterface) {
            throw new \RuntimeException('The date argument passed to the format function must be an int or a DateTimeInterface');
        }

        return $formatter->format($date, $timezone);
    }
}

class_alias(DateTimeExtension::class, 'EzSystems\EzPlatformUser\Templating\Twig\DateTimeExtension');
