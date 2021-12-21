<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Form\DataTransformer;

use Ibexa\User\Form\Data\UserSettingGroupUpdateData;
use Ibexa\User\UserSetting\Setting\DateTimeFormatSerializer;
use Ibexa\User\UserSetting\Setting\Value\DateTimeFormat;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class UserSettingGroupTransformer implements DataTransformerInterface
{

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (is_array($value)) {
            return $value;
        }
        return $value;
//        if (!\is_string($value)) {
//            throw new TransformationFailedException(
//                sprintf('Received %s instead of %s', \gettype($value), 'string')
//            );
//        }

//        $value = $this->serializer->deserialize($value);

        $transformed = [
            'identifier' => $value->getIdentifier()
        ];

        foreach ($value->getValues() as $key => $setting) {
            $transformed[$key][] = ['value' => $setting];
        }
        return $transformed;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value): ?UserSettingGroupUpdateData
    {
        if (empty($value)) {
            return null;
        }

        if (!\is_array($value)) {
            throw new TransformationFailedException(
                sprintf('Received %s instead of an array', \gettype($value))
            );
        }

        $identifier = $value['identifier'];
        unset($value['identifier']);

        return new UserSettingGroupUpdateData(
            $identifier,
            $value
        );
    }
}
