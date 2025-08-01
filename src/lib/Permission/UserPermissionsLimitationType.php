<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Permission;

use Ibexa\Contracts\Core\Exception\InvalidArgumentType;
use Ibexa\Contracts\Core\Limitation\Type as SPILimitationTypeInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation as APILimitationValue;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference as APIUserReference;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Core\Limitation\AbstractPersistenceLimitationType;

class UserPermissionsLimitationType extends AbstractPersistenceLimitationType implements SPILimitationTypeInterface
{
    /**
     * Accepts a Limitation value and checks for structural validity.
     *
     * Makes sure LimitationValue object and ->limitationValues is of correct type.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If the value does not match the expected type/structure
     */
    public function acceptValue(APILimitationValue $limitationValue): void
    {
        if (!$limitationValue instanceof UserPermissionsLimitation) {
            throw new InvalidArgumentType(
                '$limitationValue',
                'TaxonomyLimitation',
                $limitationValue
            );
        }

        if (!array_key_exists('roles', $limitationValue->limitationValues)
            || !array_key_exists('user_groups', $limitationValue->limitationValues)
        ) {
            throw new InvalidArgumentType(
                '$limitationValue->limitationValues',
                'array',
                $limitationValue->limitationValues
            );
        }

        if (!is_array($limitationValue->limitationValues['roles'])
            && $limitationValue->limitationValues['roles'] !== null
        ) {
            throw new InvalidArgumentType(
                "\$limitationValue->limitationValues['roles']",
                'array|null',
                $limitationValue->limitationValues
            );
        }

        if (!is_array($limitationValue->limitationValues['user_groups'])
            && $limitationValue->limitationValues['user_groups'] !== null
        ) {
            throw new InvalidArgumentType(
                "\$limitationValue->limitationValues['user_groups']",
                'array|null',
                $limitationValue->limitationValues
            );
        }
    }

    /**
     * Makes sure LimitationValue->limitationValues is valid according to valueSchema().
     *
     * Make sure {@link acceptValue()} is checked first!
     *
     * @return \Ibexa\Contracts\Core\FieldType\ValidationError[]
     */
    public function validate(APILimitationValue $limitationValue): array
    {
        $validationErrors = [];

        if ($limitationValue->limitationValues['roles'] !== null) {
            foreach ($limitationValue->limitationValues['roles'] as $key => $id) {
                try {
                    $this->persistence->userHandler()->loadRole($id);
                } catch (NotFoundException) {
                    $validationErrors[] = new ValidationError(
                        "limitationValues[%key%] => '%value%' does not exist in the backend",
                        null,
                        [
                            'value' => $id,
                            'key' => $key,
                        ]
                    );
                }
            }
        }

        if ($limitationValue->limitationValues['user_groups'] !== null) {
            foreach ($limitationValue->limitationValues['user_groups'] as $key => $id) {
                try {
                    $this->persistence->contentHandler()->loadContentInfo($id);
                } catch (NotFoundException) {
                    $validationErrors[] = new ValidationError(
                        "limitationValues[%key%] => '%value%' does not exist in the backend",
                        null,
                        [
                            'value' => $id,
                            'key' => $key,
                        ]
                    );
                }
            }
        }

        return $validationErrors;
    }

    /**
     * Create the Limitation Value.
     *
     * @param array<mixed> $limitationValues
     */
    public function buildValue(array $limitationValues): APILimitationValue
    {
        return new UserPermissionsLimitation(['limitationValues' => $limitationValues]);
    }

    /**
     * Evaluate permission against content & target(placement/parent/assignment).
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If any of the arguments are invalid
     *         Example: If LimitationValue is instance of ContentTypeLimitationValue, and Type is SectionLimitationType.
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException If value of the LimitationValue is unsupported
     *         Example if OwnerLimitationValue->limitationValues[0] is not one of: [ 1 ]
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject $object
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject[]|null $targets The context of the $object, like Location of Content, if null none where provided by caller
     */
    public function evaluate(APILimitationValue $value, APIUserReference $currentUser, object $object, array $targets = null): ?bool
    {
        if (!$value instanceof UserPermissionsLimitation) {
            throw new InvalidArgumentException('$value', 'Must be of type: APISiteAccessLimitation');
        }

        if (!$object instanceof Role && !$object instanceof Content) {
            return self::ACCESS_ABSTAIN;
        }

        if (($object instanceof Role && is_array($value->limitationValues['roles']))
            && (empty($value->limitationValues['roles']) || in_array($object->id, $value->limitationValues['roles']))
        ) {
            return self::ACCESS_GRANTED;
        }

        if (($object instanceof Content && is_array($value->limitationValues['user_groups']))
            && (empty($value->limitationValues['user_groups']) || in_array($object->id, $value->limitationValues['user_groups']))
        ) {
            return self::ACCESS_GRANTED;
        }

        return self::ACCESS_DENIED;
    }

    /**
     * Returns Criterion for use in find() query.
     */
    public function getCriterion(APILimitationValue $value, APIUserReference $currentUser): CriterionInterface
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * Returns info on valid $limitationValues.
     *
     * @return mixed[]|int In case of array, a hash with key as valid limitations value and value as human readable name
     *                     of that option, in case of int on of VALUE_SCHEMA_ constants.
     */
    public function valueSchema(): int|array
    {
        throw new NotImplementedException(__METHOD__);
    }
}
