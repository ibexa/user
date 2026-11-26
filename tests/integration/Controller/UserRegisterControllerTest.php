<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\User\Controller;

use Ibexa\ContentForms\Form\ActionDispatcher\UserDispatcher;
use Ibexa\Contracts\User\Invitation\InvitationCreateStruct;
use Ibexa\Tests\Integration\User\IbexaKernelTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \Ibexa\Bundle\User\Controller\UserRegisterController
 */
final class UserRegisterControllerTest extends IbexaKernelTestCase
{
    private const string INVITEE_EMAIL = 'invitee@ibexa.co';

    public function testRegisterFromInvitationRespondsWithOk(): void
    {
        self::setAdministratorUser();
        self::getContainer()->set(UserDispatcher::class, new UserDispatcher());

        // InvitationService::isValid() compares against the SiteAccess the request resolves to
        // which is the default one, not what SiteAccessServiceInterface::getCurrent() returns here
        $siteAccess = self::getContainer()->getParameter('ibexa.site_access.default');
        self::assertIsString($siteAccess);

        $invitation = self::getInvitationService()->createInvitation(
            new InvitationCreateStruct(self::INVITEE_EMAIL, $siteAccess)
        );

        $kernel = self::$kernel;
        self::assertNotNull($kernel);

        $client = new KernelBrowser($kernel);
        $client->request('GET', '/from-invite/register/' . $invitation->getHash());

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}
