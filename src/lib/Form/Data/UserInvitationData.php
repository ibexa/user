<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Form\Data;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Core\MVC\Symfony\string;

class UserInvitationData
{
    private ?string $email;

    private ?Role $role;

    private ?string $siteaccess;

    private ?UserGroup $userGroup;

    /** @var \Ibexa\Contracts\Core\Persistence\Content\Section[]|null */
    private ?array $sections;

    private ?string $locationPath;

    private ?RoleLimitation $roleLimitation;

    private ?string $limitationType;

    private ?array $limitationValue;

    public function __construct(
        ?string $email = null,
        ?Role $role = null,
        ?string $siteaccess = null,
        ?UserGroup $userGroup = null,
        ?array $sections = null,
        ?string $locationId = null,
        ?RoleLimitation $roleLimitation = null,
        ?string $limitationType = null,
        ?array $limitationValue = null
    ) {
        $this->email = $email;
        $this->role = $role;
        $this->siteaccess = $siteaccess;
        $this->userGroup = $userGroup;
        $this->sections = $sections;
        $this->locationPath = $locationId;
        $this->limitationType = $limitationType;
        $this->limitationValue = $limitationValue;
        $this->roleLimitation = $roleLimitation;
    }

    public function getLimitationType(): ?string
    {
        return $this->limitationType;
    }

    public function setLimitationType(?string $limitationType): void
    {
        $this->limitationType = $limitationType;
    }

    public function getLimitationValue(): ?array
    {
        return $this->limitationValue;
    }

    public function setLimitationValue(?array $limitationValue): void
    {
        $this->limitationValue = $limitationValue;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): void
    {
        $this->role = $role;
    }

    public function getUserGroup(): ?UserGroup
    {
        return $this->userGroup;
    }

    public function setUserGroup(?UserGroup $userGroup): void
    {
        $this->userGroup = $userGroup;
    }

    public function getSiteaccess(): ?string
    {
        return $this->siteaccess;
    }

    public function setSiteaccess(?string $siteaccess): void
    {
        $this->siteaccess = $siteaccess;
    }

    public function getSections(): ?array
    {
        return $this->sections;
    }

    public function setSections(?array $sections): void
    {
        $this->sections = $sections;
    }

    public function getLocationPath(): ?string
    {
        return $this->locationPath;
    }

    public function setLocationPath(?string $locationPath): void
    {
        $this->locationPath = $locationPath;
    }

    public function setRoleLimitation(?RoleLimitation $roleLimitation): void
    {
        $this->roleLimitation = $roleLimitation;
    }

    public function getRoleLimitation(): ?RoleLimitation
    {
        return $this->roleLimitation;
    }
}
