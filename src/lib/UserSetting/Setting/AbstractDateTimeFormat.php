<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting\Setting;

use DateTimeImmutable;
use Ibexa\Contracts\User\UserSetting\FormMapperInterface;
use Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface;
use Ibexa\User\UserSetting\DateTimeFormat\FormatterInterface;

abstract class AbstractDateTimeFormat implements ValueDefinitionInterface, FormMapperInterface
{
    public function __construct(
        protected DateTimeFormatSerializer $serializer,
        protected FormatterInterface $formatter
    ) {
    }

    public function getName(): string
    {
        return $this->getTranslatedName();
    }

    public function getDescription(): string
    {
        return $this->getTranslatedDescription();
    }

    public function getDisplayValue(string $storageValue): string
    {
        $dateTimeFormat = $this->serializer->deserialize($storageValue);

        $allowedDateFormats = array_flip($this->getAllowedDateFormats());
        $allowedTimeFormats = array_flip($this->getAllowedTimeFormats());

        $dateFormatLabel = $dateTimeFormat->getDateFormat();
        if (isset($allowedDateFormats[$dateFormatLabel])) {
            $dateFormatLabel = $allowedDateFormats[$dateFormatLabel];
        }

        $timeFormatLabel = $dateTimeFormat->getTimeFormat();
        if (isset($allowedTimeFormats[$timeFormatLabel])) {
            $timeFormatLabel = $allowedTimeFormats[$timeFormatLabel];
        }

        $demoValue = $this->formatter->format(new DateTimeImmutable());

        return "$demoValue ($dateFormatLabel $timeFormatLabel)";
    }

    /**
     * @return string[]
     */
    abstract protected function getAllowedTimeFormats(): array;

    /**
     * @return string[]
     */
    abstract protected function getAllowedDateFormats(): array;

    abstract protected function getTranslatedName(): string;

    abstract protected function getTranslatedDescription(): string;
}
