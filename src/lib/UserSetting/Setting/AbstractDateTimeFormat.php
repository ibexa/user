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
    protected DateTimeFormatSerializer $serializer;

    /** @var \Ibexa\User\UserSetting\DateTimeFormat\Formatter|null */
    protected FormatterInterface $formatter;

    /**
     * @param \Ibexa\User\UserSetting\Setting\DateTimeFormatSerializer $serializer
     * @param \Ibexa\User\UserSetting\DateTimeFormat\FormatterInterface $formatter
     */
    public function __construct(DateTimeFormatSerializer $serializer, FormatterInterface $formatter)
    {
        $this->serializer = $serializer;
        $this->formatter = $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->getTranslatedName();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        return $this->getTranslatedDescription();
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * @return string
     */
    abstract protected function getTranslatedName(): string;

    /**
     * @return string
     */
    abstract protected function getTranslatedDescription(): string;
}
