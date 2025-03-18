<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting\Setting\Value;

final class DateTimeFormat
{
    private ?string $dateFormat;

    private ?string $timeFormat;

    /**
     * @param string|null $dateFormat
     * @param string|null $timeFormat
     */
    public function __construct(?string $dateFormat = null, ?string $timeFormat = null)
    {
        $this->dateFormat = $dateFormat;
        $this->timeFormat = $timeFormat;
    }

    /**
     * @return string|null
     */
    public function getDateFormat(): ?string
    {
        return $this->dateFormat;
    }

    public function setDateFormat(?string $dateFormat): void
    {
        $this->dateFormat = $dateFormat;
    }

    /**
     * @return string|null
     */
    public function getTimeFormat(): ?string
    {
        return $this->timeFormat;
    }

    public function setTimeFormat(?string $timeFormat): void
    {
        $this->timeFormat = $timeFormat;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $parts = [];

        if ($this->dateFormat) {
            $parts[] = $this->dateFormat;
        }

        if ($this->timeFormat) {
            $parts[] = $this->timeFormat;
        }

        return implode(' ', $parts);
    }
}
