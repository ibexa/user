<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\User\Invitation;

interface InvitationService
{
    public function createInvitation(
        InvitationCreateStruct $createStruct
    ): Invitation;

    public function isValid(Invitation $invitation): bool;

    public function getInvitation(string $hash): Invitation;

    public function getInvitationByEmail(string $email): Invitation;

    public function markAsUsed(Invitation $invitation): void;
}
