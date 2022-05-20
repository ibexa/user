<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Invitation;

use DateTime;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\User\Invitation\DomainMapper as DomainMapperInterface;
use Ibexa\Contracts\User\Invitation\Invitation;
use Ibexa\Contracts\User\Invitation\Persistence\Invitation as PersistenceInvitation;
use Ibexa\Core\Repository\Permission\LimitationService;

final class DomainMapper implements DomainMapperInterface
{
    private UserService $userService;

    private RoleService $roleService;

    private Repository $repository;

    private LimitationService $limitationService;

    public function __construct(
        Repository $repository,
        UserService $userService,
        RoleService $roleService,
        LimitationService $limitationService
    ) {
        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->repository = $repository;
        $this->limitationService = $limitationService;
    }

    public function buildDomainObject(PersistenceInvitation $invitation): Invitation
    {
        $userGroup = $invitation->getGroupId() !== null ? $this->loadUserGroup($invitation->getGroupId()) : null;
        $role = $invitation->getRoleId() !== null ? $this->loadRole($invitation->getRoleId()) : null;

        $roleLimitation = null;
        if ($invitation->getLimitation()) {
            $roleLimitation = $this->mapRoleLimitation(
                $invitation->getLimitation(),
                $invitation->getLimitationValue()
            );
        }

        return new Invitation(
            $invitation->getEmail(),
            $invitation->getHash(),
            new DateTime('@' . $invitation->getCreatedAtTimestamp()),
            $invitation->getSiteAccessIdentifier(),
            $invitation->isUsed(),
            $role,
            $userGroup,
            $roleLimitation
        );
    }

    private function loadUserGroup(int $userGroupId): UserGroup
    {
        return $this->repository->sudo(fn () => $this->userService->loadUserGroup($userGroupId));
    }

    private function loadRole(int $roleId): Role
    {
        return $this->repository->sudo(fn () => $this->roleService->loadRole($roleId));
    }

    private function mapRoleLimitation(string $type, array $values): RoleLimitation
    {
        $limitation = $this->limitationService->getLimitationType($type)->buildValue($values);

        if (!$limitation instanceof RoleLimitation) {
            throw new InvalidArgumentException(
                $type,
                sprintf(
                    'Given limitation must be of: %s class, %s given',
                    RoleLimitation::class,
                    get_class($limitation)
                )
            );
        }

        return $limitation;
    }
}
