<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\User\Invitation;

use Ibexa\Contracts\Core\Repository\Exceptions\BadStateException;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\User\Invitation\Query\InvitationFilter;

interface InvitationService
{
    /**
     * @throws BadStateException
     * @throws InvalidArgumentException
     * @throws UnauthorizedException
     * @throws \JsonException
     */
    public function createInvitation(
        InvitationCreateStruct $createStruct
    ): Invitation;

    public function isValid(Invitation $invitation): bool;

    public function isExpired(Invitation $invitation): bool;

    public function getInvitation(string $hash): Invitation;

    public function getInvitationByEmail(string $email): Invitation;

    public function markAsUsed(Invitation $invitation): void;

    /**
     * @return Invitation[]
     *
     * @throws BadStateException
     * @throws InvalidArgumentException
     * @throws UnauthorizedException
     */
    public function findInvitations(?InvitationFilter $invitationsFilter = null): array;

    public function refreshInvitation(Invitation $invitation): Invitation;
}
