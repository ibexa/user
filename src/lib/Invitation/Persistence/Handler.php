<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Invitation\Persistence;

use Ibexa\Contracts\User\Invitation\Invitation;
use Ibexa\Core\Base\Exceptions\NotFoundException;

class Handler
{
    private DoctrineGateway $gateway;

    /** @var \Ibexa\User\Invitation\Persistence\Mapper */
    private Mapper $mapper;

    public function __construct(
        DoctrineGateway $gateway,
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
        $invitationRow = $this->gateway->getInvitationForEmail($email);

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

    public function markAsUsed(Invitation $invitation): void
    {
        $this->gateway->markAsUsed($invitation->getHash());
    }
}
