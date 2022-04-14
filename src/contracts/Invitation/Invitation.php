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

interface Invitation
{
    public function getEmail(): string;

    public function getHash(): string;

    public function getSiteAccess(): SiteAccess;

    public function createdAt(): \DateTime;

    public function isUsed(): bool;

    public function getRole(): ?Role;

    public function getUserGroup(): ?UserGroup;

    public function getLimitation(): ?RoleLimitation;
}
