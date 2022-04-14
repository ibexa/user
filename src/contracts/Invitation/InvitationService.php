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
use Ibexa\Core\MVC\Symfony\SiteAccess;

interface InvitationService
{
    public function createInvitation(
        string $email,
        SiteAccess $siteAccess,
        ?UserGroup $userGroup = null,
        ?Role $role = null,
        ?RoleLimitation $roleLimitation = null
    ): Invitation;

    public function isValid(Invitation $invitation): bool;

    public function getInvitation(string $hash): Invitation;

    public function getInviteForEmail(string $email): Invitation;

    public function markAsUsed(Invitation $invitation): void;
}
