<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\User\Invitation\Query;

use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;

final class InvitationFilter
{
    private ?Role $role;

    private ?UserGroup $userGroup;

    private ?bool $isUsed;

    public function __construct(
        ?Role $role = null,
        ?UserGroup $userGroup = null,
        ?bool $isUsed = null
    ) {
        $this->role = $role;
        $this->userGroup = $userGroup;
        $this->isUsed = $isUsed;
    }

    public function setRole(?Role $role): void
    {
        $this->role = $role;
    }

    public function setUserGroup(?UserGroup $userGroup): void
    {
        $this->userGroup = $userGroup;
    }

    public function setIsUsed(?bool $isUsed): void
    {
        $this->isUsed = $isUsed;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function getUserGroup(): ?UserGroup
    {
        return $this->userGroup;
    }

    public function getIsUsed(): ?bool
    {
        return $this->isUsed;
    }
}
