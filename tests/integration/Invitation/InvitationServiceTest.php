<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\User\Invitation;

use DateTime;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\User\Invitation\Exception\InvitationAlreadyExistsException;
use Ibexa\Contracts\User\Invitation\Exception\UserAlreadyExistsException;
use Ibexa\Contracts\User\Invitation\Invitation;
use Ibexa\Contracts\User\Invitation\InvitationCreateStruct;
use Ibexa\Contracts\User\Invitation\InvitationService;
use Ibexa\Contracts\User\Invitation\Query\InvitationFilter;
use Ibexa\Tests\Integration\User\IbexaKernelTestCase;
use Ibexa\User\Invitation\Persistence\Handler;
use Symfony\Bridge\PhpUnit\ClockMock;

final class InvitationServiceTest extends IbexaKernelTestCase
{
    private const string INVITEE_EMAIL = 'thisisjustatest@ibexa.co';

    private InvitationService $invitationService;

    private UserService $userService;

    private RoleService $roleService;

    protected function setUp(): void
    {
        self::setAdministratorUser();

        $this->invitationService = self::getInvitationService();
        $this->roleService = self::getRoleService();
        $this->userService = self::getUserService();
    }

    public function testCreateInvitation(): void
    {
        $now = (new DateTime())->getTimestamp();
        $invitation = $this->createInvitation(self::INVITEE_EMAIL);

        self::assertEquals(self::INVITEE_EMAIL, $invitation->getEmail());
        self::assertEquals('admin', $invitation->getSiteAccessIdentifier());
        self::assertNotNull($invitation->getHash());
        self::assertFalse($invitation->isUsed());
        self::assertEqualsWithDelta($now, $invitation->createdAt()->getTimestamp(), 10);
    }

    public function testCreateInvitationForExistingUser(): void
    {
        $this->expectExceptionObject(new UserAlreadyExistsException());
        $this->createInvitation('admin@link.invalid');
    }

    public function testCreateExistingInvitation(): void
    {
        $this->createInvitation(self::INVITEE_EMAIL);

        $this->expectExceptionObject(new InvitationAlreadyExistsException());
        $this->createInvitation(self::INVITEE_EMAIL);
    }

    public function testFindInvitations(): void
    {
        $this->createInvitation(self::INVITEE_EMAIL);

        $invitations = $this->invitationService->findInvitations();
        self::assertCount(1, $invitations);
    }

    public function testFindInvitationsWithUserGroupFilter(): void
    {
        $editorsGroup = $this->userService->loadUserGroupByRemoteId('3c160cca19fb135f83bd02d911f04db2');

        $this->createInvitation(self::INVITEE_EMAIL);

        $this->createInvitation('editor@ibexa.co', $editorsGroup);

        $invitations = $this->invitationService->findInvitations();
        self::assertCount(2, $invitations);

        $editorInvitations = $this->invitationService->findInvitations(
            new InvitationFilter(null, $editorsGroup)
        );

        self::assertCount(1, $editorInvitations);
    }

    public function testFindInvitationsWithUserGroupAndRoleFilter(): void
    {
        $editorsGroup = $this->userService->loadUserGroupByRemoteId('3c160cca19fb135f83bd02d911f04db2');
        $role = $this->roleService->loadRoleByIdentifier('Anonymous');

        $this->createInvitation(self::INVITEE_EMAIL);

        $this->createInvitation('editor@ibexa.co', $editorsGroup);

        $this->createInvitation('anonymous_editor@ibexa.co', $editorsGroup, $role);

        $invitations = $this->invitationService->findInvitations();
        self::assertCount(3, $invitations);

        $editorInvitations = $this->invitationService->findInvitations(
            new InvitationFilter($role, $editorsGroup)
        );

        self::assertCount(1, $editorInvitations);
    }

    public function testMarkInvitationAsUsed(): void
    {
        $invitation = $this->createInvitation('used@ibexa.co');

        self::assertFalse($invitation->isUsed());

        $this->invitationService->markAsUsed($invitation);

        self::assertTrue(
            $this->invitationService->getInvitation($invitation->getHash())->isUsed()
        );
    }

    public function testRefreshInvitation(): void
    {
        ClockMock::register(Handler::class);
        ClockMock::register(self::class);

        ClockMock::withClockMock(true);

        $invitation = $this->createInvitation('refresh@ibexa.co');
        sleep(5);

        $refreshed = $this->invitationService->refreshInvitation($invitation);

        self::assertGreaterThan($invitation->createdAt()->getTimestamp(), $refreshed->createdAt()->getTimestamp());
        self::assertNotEquals($invitation->getHash(), $refreshed->getHash());

        ClockMock::withClockMock(false);
    }

    public function testGetInvitationByEmailReturnsRowForExistingInvitation(): void
    {
        $invitationEmail = 'invitation-service@ibexa.co';

        $this->createInvitation('invitation-service-other@ibexa.co');

        $invitation = $this->createInvitation($invitationEmail);

        $invitationByEmail = $this->invitationService->getInvitationByEmail($invitationEmail);

        self::assertSame($invitationEmail, $invitationByEmail->getEmail());
        self::assertSame($invitation->getHash(), $invitationByEmail->getHash());
    }

    public function testGetInvitationByEmailThrowErrorWhenInvitationDoesNotExist(): void
    {
        $this->expectException(NotFoundException::class);

        $this->invitationService->getInvitationByEmail('gateway-missing@ibexa.co');
    }

    public function testGetInvitationReturnsRowForExistingHash(): void
    {
        $this->createInvitation('invitation-service-by-hash-other@ibexa.co');

        $invitation = $this->createInvitation('invitation-service-by-hash@ibexa.co');

        $invitationByHash = $this->invitationService->getInvitation($invitation->getHash());

        self::assertSame('invitation-service-by-hash@ibexa.co', $invitationByHash->getEmail());
        self::assertSame($invitation->getHash(), $invitationByHash->getHash());
    }

    public function testGetInvitationThrowExceptionWhenHashDoesNotExist(): void
    {
        $this->expectException(NotFoundException::class);

        $this->invitationService->getInvitation('invitation-service-missing-hash');
    }

    private function createInvitation(
        string $email,
        ?UserGroup $userGroup = null,
        ?Role $role = null
    ): Invitation {
        return $this->invitationService->createInvitation(
            new InvitationCreateStruct(
                $email,
                'admin',
                $userGroup,
                $role
            )
        );
    }
}
