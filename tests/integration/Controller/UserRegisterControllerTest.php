<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\User\Controller;

use Ibexa\Bundle\User\Controller\UserRegisterController;
use Ibexa\ContentForms\Form\ActionDispatcher\UserDispatcher;
use Ibexa\Contracts\User\Invitation\InvitationCreateStruct;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Tests\Integration\User\IbexaKernelTestCase;
use Ibexa\User\View\Register\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @covers \Ibexa\Bundle\User\Controller\UserRegisterController::registerFromInvitationAction
 */
final class UserRegisterControllerTest extends IbexaKernelTestCase
{
    private const string INVITEE_EMAIL = 'invitee@ibexa.co';

    protected function setUp(): void
    {
        self::setAdministratorUser();

        // the test kernel declares the dispatcher synthetic; the form is never submitted here
        self::getContainer()->set(UserDispatcher::class, new UserDispatcher());
    }

    public function testRegisterFromInvitationBuildsForm(): void
    {
        $invitation = self::getInvitationService()->createInvitation(
            new InvitationCreateStruct(self::INVITEE_EMAIL, $this->getCurrentSiteAccessName())
        );

        $controller = self::getServiceByClassName(UserRegisterController::class);

        $request = new Request(attributes: ['inviteHash' => $invitation->getHash()]);
        // the form has CSRF protection, which reads the token from the session
        $request->setSession(new Session(new MockArraySessionStorage()));
        self::getServiceByClassName(RequestStack::class)->push($request);

        $view = $controller->registerFromInvitationAction($request);

        self::assertInstanceOf(FormView::class, $view);
    }

    protected function tearDown(): void
    {
        // Fixtures are imported once per run, so without this the invitation survives into
        // InvitationServiceTest, which asserts absolute findInvitations() counts and fails
        $connection = self::getDoctrineConnection();
        $connection->executeStatement(
            'DELETE FROM ibexa_user_invitation_assignment WHERE invitation_id IN '
            . '(SELECT id FROM ibexa_user_invitation WHERE email = :email)',
            ['email' => self::INVITEE_EMAIL]
        );
        $connection->delete('ibexa_user_invitation', ['email' => self::INVITEE_EMAIL]);
    }

    private function getCurrentSiteAccessName(): string
    {
        $siteAccess = self::getServiceByClassName(SiteAccessServiceInterface::class)->getCurrent();
        self::assertNotNull($siteAccess);

        return $siteAccess->name;
    }
}
