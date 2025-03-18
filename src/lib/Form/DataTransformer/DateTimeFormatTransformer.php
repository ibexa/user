<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Form\DataTransformer;

use Ibexa\User\UserSetting\Setting\DateTimeFormatSerializer;
use Ibexa\User\UserSetting\Setting\Value\DateTimeFormat;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DateTimeFormatTransformer implements DataTransformerInterface
{
    private DateTimeFormatSerializer $serializer;

    /**
     * @param \Ibexa\User\UserSetting\Setting\DateTimeFormatSerializer $serializer
     */
    public function __construct(DateTimeFormatSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value): ?array
    {
        if (null === $value) {
            return null;
        }

        if (!\is_string($value)) {
            throw new TransformationFailedException(
                sprintf('Received %s instead of %s', \gettype($value), 'string')
            );
        }

        $value = $this->serializer->deserialize($value);

        return [
            'date_format' => $value->getDateFormat(),
            'time_format' => $value->getTimeFormat(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (!\is_array($value)) {
            throw new TransformationFailedException(
                sprintf('Received %s instead of an array', \gettype($value))
            );
        }

        return $this->serializer->serialize(new DateTimeFormat(
            $value['date_format'],
            $value['time_format']
        ));
    }
}
