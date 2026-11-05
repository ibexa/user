<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\User\Invitation\Persistence;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
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
        $gatewayEmail = 'gateway-existing@ibexa.co';
        $this->gateway->addInvitation('gateway-other@ibexa.co', 'admin', 'gateway-other-hash');
        $this->gateway->addInvitation($gatewayEmail, 'admin', 'gateway-existing-hash');

        $invitationRow = $this->gateway->getInvitationByEmail($gatewayEmail);

        self::assertSame($gatewayEmail, $invitationRow['email']);
        self::assertSame('gateway-existing-hash', $invitationRow['hash']);
    }

    public function testGetInvitationByEmailThrowExceptionWhenInvitationDoesNotExist(): void
    {
        $this->expectException(NotFoundException::class);

        $this->gateway->getInvitationByEmail('gateway-missing@ibexa.co');
    }

    public function testGetInvitationReturnsRowForExistingHash(): void
    {
        $gatewayHash = 'gateway-existing-by-hash-hash';
        $this->gateway->addInvitation('gateway-other-by-hash@ibexa.co', 'admin', 'gateway-other-by-hash-hash');
        $this->gateway->addInvitation('gateway-existing-by-hash@ibexa.co', 'admin', $gatewayHash);

        $invitationRow = $this->gateway->getInvitation($gatewayHash);

        self::assertSame('gateway-existing-by-hash@ibexa.co', $invitationRow['email']);
        self::assertSame($gatewayHash, $invitationRow['hash']);
    }

    public function testGetInvitationThrowExceptionWhenHashDoesNotExist(): void
    {
        $this->expectException(NotFoundException::class);

        $this->gateway->getInvitation('gateway-missing-hash');
    }
}
