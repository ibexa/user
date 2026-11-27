<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\User\Controller;

use Ibexa\Bundle\User\Controller\UserInvitationController;
use Ibexa\Contracts\User\Invitation\Invitation;
use Ibexa\Contracts\User\Invitation\InvitationSender;
use Ibexa\Tests\Integration\User\IbexaKernelTestCase;
use Ibexa\User\ExceptionHandler\NullActionResultHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @covers \Ibexa\Bundle\User\Controller\UserInvitationController
 */
final class UserInvitationControllerTest extends IbexaKernelTestCase
{
    private const INVITEE_EMAIL = 'invitee@example.com';
    private const SITEACCESS = 'eng';
    private const EDITORS_GROUP_REMOTE_ID = '3c160cca19fb135f83bd02d911f04db2';

    protected function setUp(): void
    {
        self::bootKernel();
        self::setAdministratorUser();
    }

    public function testInviteUserCreatesAndSendsInvitation(): void
    {
        // the real mail template extends admin-ui's base mail layout, which this kernel doesn't load
        $invitationSender = $this->createMock(InvitationSender::class);
        $invitationSender
            ->expects(self::once())
            ->method('sendInvitation')
            ->with(self::callback(static function (Invitation $invitation): bool {
                return $invitation->getEmail() === self::INVITEE_EMAIL;
            }));

        $role = self::getRoleService()->loadRoleByIdentifier('Editor');
        $userGroup = self::getUserService()->loadUserGroupByRemoteId(self::EDITORS_GROUP_REMOTE_ID);

        $request = Request::create('/user/invite', 'POST');
        $request->setSession(new Session(new MockArraySessionStorage()));
        self::getServiceByClassName(RequestStack::class)->push($request);
        $request->request->set('user_invitation', [
            'email' => self::INVITEE_EMAIL,
            'siteaccess' => self::SITEACCESS,
            'role' => (string)$role->id,
            'user_group' => (string)$userGroup->id,
            'limitation_type' => '0',
            '_token' => self::getServiceByClassName(CsrfTokenManagerInterface::class)
                ->getToken('user_invitation')
                ->getValue(),
        ]);

        $controller = new UserInvitationController(
            self::getInvitationService(),
            $invitationSender,
            self::getServiceByClassName(FormFactoryInterface::class),
            new NullActionResultHandler()
        );
        $controller->inviteUser($request);

        $invitation = self::getInvitationService()->getInvitationByEmail(self::INVITEE_EMAIL);
        self::assertSame(self::SITEACCESS, $invitation->getSiteAccessIdentifier());
        $invitedRole = $invitation->getRole();
        self::assertNotNull($invitedRole);
        self::assertSame($role->id, $invitedRole->id);
    }
}
