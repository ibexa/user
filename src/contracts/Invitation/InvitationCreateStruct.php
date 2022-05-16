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
use Ibexa\Core\MVC\Symfony\string;

class InvitationCreateStruct extends ValueObject
{
    protected string $email;

    protected string $siteAccessIdentifier;

    protected ?UserGroup $userGroup;

    protected ?Role $role;

    protected ?RoleLimitation $roleLimitation;

    public function __construct(
        string $email,
        string $siteAccessIdentifier,
        ?UserGroup $userGroup = null,
        ?Role $role = null,
        ?RoleLimitation $roleLimitation = null
    ) {
        parent::__construct([
            'email' => $email,
            'siteAccessIdentifier' => $siteAccessIdentifier,
            'userGroup' => $userGroup,
            'role' => $role,
            'roleLimitation' => $roleLimitation,
        ]);
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
