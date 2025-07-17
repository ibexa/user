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
    public function __construct(
        private readonly string $email,
        private readonly string $hash,
        private readonly DateTimeInterface $createdAt,
        private readonly string $siteAccessIdentifier,
        private readonly bool $used,
        private readonly ?Role $role = null,
        private readonly ?UserGroup $userGroup = null,
        private readonly ?RoleLimitation $limitation = null
    ) {
        parent::__construct();
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

    public function createdAt(): DateTimeInterface
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
