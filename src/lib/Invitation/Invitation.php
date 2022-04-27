<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Invitation;

use DateTime;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\MVC\Symfony\SiteAccess;

class Invitation extends ValueObject implements \Ibexa\Contracts\User\Invitation\Invitation
{
    protected string $email;

    protected string $hash;

    protected DateTime $createdAt;

    protected SiteAccess $siteAccess;

    protected bool $used;

    protected ?Role $role;

    protected ?UserGroup $userGroup;

    protected ?RoleLimitation $limitation;

    public function __construct(
        string $email,
        string $hash,
        DateTime $createdAt,
        SiteAccess $siteAccess,
        bool $used,
        ?Role $role = null,
        ?UserGroup $userGroup = null,
        ?RoleLimitation $limitation = null
    ) {
        parent::__construct([
            'email' => $email,
            'hash' => $hash,
            'createdAt' => $createdAt,
            'siteAccess' => $siteAccess,
            'used' => $used,
            'role' => $role,
            'userGroup' => $userGroup,
            'limitation' => $limitation,
        ]);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getSiteAccess(): SiteAccess
    {
        return $this->siteAccess;
    }

    public function createdAt(): DateTime
    {
        return $this->createdAt;
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function getUserGroup(): ?UserGroup
    {
        return $this->userGroup;
    }

    public function getLimitation(): ?RoleLimitation
    {
        return $this->limitation;
    }
}
