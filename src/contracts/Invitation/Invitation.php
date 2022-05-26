<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\User\Invitation;

use DateTimeInterface;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class Invitation extends ValueObject
{
    private string $email;

    private string $hash;

    private DateTimeInterface $createdAt;

    private string $siteAccessIdentifier;

    private bool $used;

    private ?Role $role;

    private ?UserGroup $userGroup;

    private ?RoleLimitation $limitation;

    public function __construct(
        string $email,
        string $hash,
        DateTime $createdAt,
        string $siteAccessIdentifier,
        bool $used,
        ?Role $role = null,
        ?UserGroup $userGroup = null,
        ?RoleLimitation $limitation = null
    ) {
        parent::__construct();

        $this->email = $email;
        $this->hash = $hash;
        $this->createdAt = $createdAt;
        $this->siteAccessIdentifier = $siteAccessIdentifier;
        $this->used = $used;
        $this->role = $role;
        $this->userGroup = $userGroup;
        $this->limitation = $limitation;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getSiteAccessIdentifier(): string
    {
        return $this->siteAccessIdentifier;
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
