<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\User\Invitation;

use DateTime;
use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\User\Invitation\Exception\InvitationAlreadyExistsException;
use Ibexa\Contracts\User\Invitation\Exception\UserAlreadyExistsException;
use Ibexa\Contracts\User\Invitation\InvitationCreateStruct;
use Ibexa\Contracts\User\Invitation\InvitationService;
use Ibexa\Contracts\User\Invitation\Query\InvitationFilter;
use Ibexa\Tests\Integration\User\IbexaKernelTestCase;
use Ibexa\User\Invitation\Persistence\Handler;
use Symfony\Bridge\PhpUnit\ClockMock;

final class InvitationServiceTest extends IbexaKernelTestCase
{
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
        $invitation = $this->invitationService->createInvitation(
            new InvitationCreateStruct(
                'thisisjustatest@ibexa.co',
                'admin',
            )
        );

        self::assertEquals('thisisjustatest@ibexa.co', $invitation->getEmail());
        self::assertEquals('admin', $invitation->getSiteAccessIdentifier());
        self::assertNotNull($invitation->getHash());
        self::assertFalse($invitation->isUsed());
        self::assertEqualsWithDelta($now, $invitation->createdAt()->getTimestamp(), 10);
    }

    public function testCreateInvitationForExistingUser(): void
    {
        $this->expectExceptionObject(new UserAlreadyExistsException());
        $this->invitationService->createInvitation(
            new InvitationCreateStruct(
                'admin@link.invalid',
                'admin',
            )
        );
    }

    public function testCreateExistingInvitation(): void
    {
        $this->expectExceptionObject(new InvitationAlreadyExistsException());
        $this->invitationService->createInvitation(
            new InvitationCreateStruct(
                'thisisjustatest@ibexa.co',
                'admin',
            )
        );
    }

    public function testFindInvitations(): void
    {
        $invitations = $this->invitationService->findInvitations();
        self::assertCount(1, $invitations);
    }

    public function testFindInvitationsWithUserGroupFilter(): void
    {
        $editorsGroup = $this->userService->loadUserGroupByRemoteId('3c160cca19fb135f83bd02d911f04db2');

        $this->invitationService->createInvitation(
            new InvitationCreateStruct(
                'editor@ibexa.co',
                'admin',
                $editorsGroup
            )
        );

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

        $this->invitationService->createInvitation(
            new InvitationCreateStruct(
                'anonymous_editor@ibexa.co',
                'admin',
                $editorsGroup,
                $role
            )
        );

        $invitations = $this->invitationService->findInvitations();
        self::assertCount(3, $invitations);

        $editorInvitations = $this->invitationService->findInvitations(
            new InvitationFilter($role, $editorsGroup)
        );

        self::assertCount(1, $editorInvitations);
    }

    public function testMarkInvitationAsUsed(): void
    {
        $invitation = $this->invitationService->createInvitation(
            new InvitationCreateStruct(
                'used@ibexa.co',
                'admin',
            )
        );

        self::assertFalse($invitation->isUsed());

        $this->invitationService->markAsUsed($invitation);

        self::assertTrue(
            $this->invitationService->getInvitation($invitation->getHash())->isUsed()
        );
    }

    public function testRefreshInvitation(): void
    {
        ClockMock::register(Handler::class);
        ClockMock::register(__CLASS__);

        ClockMock::withClockMock(true);

        $invitation = $this->invitationService->createInvitation(
            new InvitationCreateStruct(
                'refresh@ibexa.co',
                'admin',
            )
        );
        sleep(5);

        $refreshed = $this->invitationService->refreshInvitation($invitation);

        self::assertGreaterThan($invitation->createdAt()->getTimestamp(), $refreshed->createdAt()->getTimestamp());
        self::assertNotEquals($invitation->getHash(), $refreshed->getHash());

        ClockMock::withClockMock(false);
    }
}
