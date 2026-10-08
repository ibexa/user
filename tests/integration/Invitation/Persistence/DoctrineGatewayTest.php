<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\User\Invitation\Persistence;

use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Tests\Integration\User\IbexaKernelTestCase;
use Ibexa\User\Invitation\Persistence\DoctrineGateway;

final class DoctrineGatewayTest extends IbexaKernelTestCase
{
    private DoctrineGateway $gateway;

    protected function setUp(): void
    {
        $this->gateway = self::getInvitationGateway();
    }

    public function testGetInvitationByEmailReturnsRowForExistingInvitation(): void
    {
        $invitation = $this->gateway->addInvitation(
            'gateway-existing@ibexa.co',
            'admin',
            'gateway-existing-hash'
        );

        $invitationRow = $this->gateway->getInvitationByEmail($invitation['email']);

        self::assertSame($invitation['email'], $invitationRow['email']);
        self::assertSame($invitation['hash'], $invitationRow['hash']);
    }

    public function testGetInvitationByEmailThrowExceptionWhenInvitationDoesNotExist(): void
    {
        $this->expectException(NotFoundException::class);

        $this->gateway->getInvitationByEmail('gateway-missing@ibexa.co');
    }

    public function testGetInvitationReturnsRowForExistingHash(): void
    {
        $invitation = $this->gateway->addInvitation(
            'gateway-existing-by-hash@ibexa.co',
            'admin',
            'gateway-existing-by-hash-hash'
        );

        $invitationRow = $this->gateway->getInvitation($invitation['hash']);

        self::assertSame($invitation['email'], $invitationRow['email']);
        self::assertSame($invitation['hash'], $invitationRow['hash']);
    }

    public function testGetInvitationThrowExceptionWhenHashDoesNotExist(): void
    {
        $this->expectException(NotFoundException::class);

        $this->gateway->getInvitation('gateway-missing-hash');
    }
}
