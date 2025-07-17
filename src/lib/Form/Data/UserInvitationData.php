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
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Symfony\Component\Validator\Constraints as Assert;

final class UserInvitationData
{
    /**
     * @param array<int, \Ibexa\Contracts\Core\Repository\Values\Content\Section>|null $sections
     * @param array<string, mixed>|null $limitationValue
     */
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        private string $email,
        #[Assert\NotBlank]
        private ?SiteAccess $siteaccess = null,
        private ?Role $role = null,
        private ?UserGroup $userGroup = null,
        private ?array $sections = null,
        private ?string $locationPath = null,
        private ?RoleLimitation $roleLimitation = null,
        private ?string $limitationType = null,
        private ?array $limitationValue = null
    ) {
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
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

    public function getSiteaccess(): SiteAccess
    {
        return $this->siteaccess;
    }

    public function setSiteaccess(SiteAccess $siteaccess): void
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
