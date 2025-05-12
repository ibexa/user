<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Form\DataTransformer;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation\SectionLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\SubtreeLimitation;
use Ibexa\User\Form\Data\UserInvitationData;
use Ibexa\User\Form\Type\Invitation\UserInvitationType;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @phpstan-type TUserInvitationArray array{email: string, siteaccess: string, role: string|null, user_group: string|null, limitation_type: string|null, sections: array<int>, location_path: string|null}
 *
 * @phpstan-implements \Symfony\Component\Form\DataTransformerInterface<array, \Ibexa\User\Form\Data\UserInvitationData>
 */
final class LimitationValueTransformer implements DataTransformerInterface
{
    /**
     * @param TUserInvitationArray|null $value
     *
     * @return TUserInvitationArray|null
     */
    public function transform(mixed $value): ?array
    {
        return $value;
    }

    public function reverseTransform(mixed $value): ?UserInvitationData
    {
        if (null === $value) {
            return null;
        }

        $roleLimitation = null;

        if ($value->getLimitationType() === UserInvitationType::LIMITATION_TYPE_SECTION) {
            $limitationValues = array_map(
                static fn ($section): int => $section->id,
                $value->getSections()
            );
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
