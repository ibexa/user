<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\User\Invitation\Persistence;

interface Handler
{
    public function createInvitation(
        string $email,
        string $siteAccessName,
        string $hash,
        ?int $roleId = null,
        ?int $groupId = null,
        ?string $limitation = null,
        ?string $limitationValue = null
    ): Invitation;

    public function getInvitation(string $hash): Invitation;

    public function getInvitationForEmail(string $email): Invitation;

    public function invitationExistsForEmail(string $email): bool;

    public function markAsUsed(string $hash): void;
}
