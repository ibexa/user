<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Form\DataTransformer;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation\SectionLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\SubtreeLimitation;
use Ibexa\User\Form\Type\Invitation\UserInvitationType;
use Symfony\Component\Form\DataTransformerInterface;

class LimitationValueTransformer implements DataTransformerInterface
{
    public function transform($value): ?array
    {
        return $value;
    }

    public function reverseTransform($value)
    {
        /** @var \Ibexa\User\Form\Data\UserInvitationData $value */
        if (null === $value) {
            return null;
        }

        $roleLimitation = null;

        if ($value->getLimitationType() === UserInvitationType::LIMITATION_TYPE_SECTION) {
            $limitationValues = array_map(static fn ($section) => $section->id, $value->getSections());
            $roleLimitation = new SectionLimitation(['limitationValues' => $limitationValues]);
        }

        if ($value->getLimitationType() === UserInvitationType::LIMITATION_TYPE_LOCATION) {
            $limitationValues = [$value->getLocationPath()];
            $roleLimitation = new SubtreeLimitation(['limitationValues' => $limitationValues]);
        }

        $value->setRoleLimitation($roleLimitation);

        return $value;
    }
}
