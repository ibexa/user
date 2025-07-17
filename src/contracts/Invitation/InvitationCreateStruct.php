<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\User\Invitation;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class InvitationCreateStruct extends ValueObject
{
    public function __construct(
        private readonly string $email,
        private readonly string $siteAccessIdentifier,
        private readonly ?UserGroup $userGroup = null,
        private readonly ?Role $role = null,
        private readonly ?RoleLimitation $roleLimitation = null
    ) {
        parent::__construct();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getSiteAccessIdentifier(): string
    {
        return $this->siteAccessIdentifier;
    }

    public function getUserGroup(): ?UserGroup
    {
        return $this->userGroup;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function getRoleLimitation(): ?RoleLimitation
    {
        return $this->roleLimitation;
    }
}
