<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Invitation\Persistence;

use eZ\Publish\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\SectionLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\SubtreeLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\User\Invitation\Invitation;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;

final class Mapper
{
    private SiteAccessServiceInterface $siteAccessService;

    private UserService $userService;

    private RoleService $roleService;

    private Repository $repository;

    public function __construct(
        Repository $repository,
        SiteAccessServiceInterface $siteAccessService,
        UserService $userService,
        RoleService $roleService
    ) {
        $this->siteAccessService = $siteAccessService;
        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->repository = $repository;
    }

    public function extractInvitationFromRow(array $row): Invitation
    {
        $userGroup = $row['user_group_id'] !== null ? $this->loadUserGroup((int) $row['user_group_id']) : null;
        $role = $row['role_id'] !== null ? $this->loadRole((int) $row['role_id']) : null;

        $roleLimitation = null;
        if ($row['limitation_type']) {
            $roleLimitation = $this->mapRoleLimitation(
                $row['limitation_type'],
                json_decode($row['limitation_value'], true)
            );
        }
        return new \Ibexa\User\Invitation\Invitation(
            $row['email'],
            $row['hash'],
            new \DateTime('@' . $row['creation_date']),
            $this->siteAccessService->get($row['site_access_name']),
            (bool)$row['used'],
            $role,
            $userGroup,
            $roleLimitation
        );
    }

    private function loadUserGroup(int $userGroupId): UserGroup
    {
        return $this->repository->sudo(fn() => $this->userService->loadUserGroup($userGroupId));
    }

    private function loadRole(int $roleId): Role
    {
        return $this->repository->sudo(fn() => $this->roleService->loadRole($roleId));
    }

    private function mapRoleLimitation(string $type, array $values): RoleLimitation
    {
        if ($type === 'Section') {
            return new SectionLimitation(['limitationValues' => $values]);
        }

        if ($type === 'Subtree') {
            return new SubtreeLimitation(['limitationValues' => $values]);
        }
    }
}
