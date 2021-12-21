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

class UserSettingGroupViewTransformer implements DataTransformerInterface
{

    /**
     * {@inheritdoc}
     */
    public function transform($value): ?UserSettingGroupUpdateData
    {
        return $value;
        if (null === $value) {
            return null;
        }
        $identifier = $value['identifier'];
        unset($value['identifier']);

        return new UserSettingGroupUpdateData(
            $identifier,
            $value
        );
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value): ?array
    {
        if (empty($value)) {
            return null;
        }

        if (!\is_array($value)) {
            throw new TransformationFailedException(
                sprintf('Received %s instead of an array', \gettype($value))
            );
        }

        $transformed = [
            'identifier' => $value->getIdentifier()
        ];

        foreach ($value->getValues() as $setting) {
            $transformed[$setting->identifier] = $setting->value;
        }
        return $transformed;
    }
}
