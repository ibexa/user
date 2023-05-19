<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Invitation\Persistence;

use Ibexa\Contracts\User\Invitation\Persistence\Gateway;
use Ibexa\Contracts\User\Invitation\Persistence\Handler as HandlerInterface;
use Ibexa\Contracts\User\Invitation\Persistence\Invitation;
use Ibexa\Contracts\User\Invitation\Persistence\InvitationUpdateStruct;
use Ibexa\Contracts\User\Invitation\Persistence\Mapper;
use Ibexa\Contracts\User\Invitation\Query\InvitationFilter;
use Ibexa\Core\Base\Exceptions\NotFoundException;

class Handler implements HandlerInterface
{
    private Gateway $gateway;

    private Mapper $mapper;

    public function __construct(
        Gateway $gateway,
        Mapper $mapper
    ) {
        $this->gateway = $gateway;
        $this->mapper = $mapper;
    }

    public function createInvitation(
        string $email,
        string $siteAccessName,
        string $hash,
        ?int $roleId = null,
        ?int $groupId = null,
        ?string $limitation = null,
        ?string $limitationValue = null
    ): Invitation {
        $invitationRow = $this->gateway->addInvitation(
            $email,
            $siteAccessName,
            $hash,
            $roleId,
            $groupId,
            $limitation,
            $limitationValue
        );

        return $this->mapper->extractInvitationFromRow($invitationRow);
    }

    public function getInvitation(
        string $hash
    ): Invitation {
        $invitationRow = $this->gateway->getInvitation($hash);

        if (empty($invitationRow)) {
            throw new NotFoundException('invitation', $hash);
        }

        return $this->mapper->extractInvitationFromRow($invitationRow);
    }

    public function getInvitationForEmail(
        string $email
    ): Invitation {
        $invitationRow = $this->gateway->getInvitationByEmail($email);

        if (empty($invitationRow)) {
            throw new NotFoundException('invitation', $email);
        }

        return $this->mapper->extractInvitationFromRow($invitationRow);
    }

    public function invitationExistsForEmail(
        string $email
    ): bool {
        return $this->gateway->invitationExistsForEmail($email);
    }

    public function markAsUsed(string $hash): void
    {
        $updateStruct = new InvitationUpdateStruct();
        $updateStruct->setIsUsed(true);

        $this->gateway->updateInvitation($hash, $updateStruct);
    }

    public function findInvitations(?InvitationFilter $invitationsFilter = null): array
    {
        $invitations = [];

        foreach ($this->gateway->findInvitations($invitationsFilter) as $invitation) {
            $invitations[] = $this->mapper->extractInvitationFromRow($invitation);
        }

        return $invitations;
    }

    public function refreshInvitation(string $hash, string $newHash): Invitation
    {
        $updateStruct = new InvitationUpdateStruct();
        $updateStruct->setCreatedAt(time());
        $updateStruct->setHash($newHash);

        $this->gateway->updateInvitation($hash, $updateStruct);

        return $this->mapper->extractInvitationFromRow(
            $this->gateway->getInvitation($newHash)
        );
    }
}
