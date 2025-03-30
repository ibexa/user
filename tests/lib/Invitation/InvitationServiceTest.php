<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\User\Invitation;

use DateInterval;
use DateTime;
use Ibexa\Contracts\Core\HashGenerator;
use Ibexa\Contracts\Core\Persistence\TransactionHandler;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\User\Invitation\DomainMapper;
use Ibexa\Contracts\User\Invitation\Invitation;
use Ibexa\Contracts\User\Invitation\Invitation as InvitationContract;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\User\Invitation\InvitationService;
use Ibexa\User\Invitation\Persistence\Handler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class InvitationServiceTest extends TestCase
{
    private InvitationService $invitationService;

    private SiteAccessServiceInterface&MockObject $siteAccessService;

    private ConfigResolverInterface&MockObject $configResolver;

    protected function setUp(): void
    {
        $this->siteAccessService = $this->createMock(SiteAccessServiceInterface::class);
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);

        $this->invitationService = new InvitationService(
            $this->createMock(PermissionResolver::class),
            $this->createMock(Handler::class),
            $this->createMock(HashGenerator::class),
            $this->createMock(UserService::class),
            $this->siteAccessService,
            $this->createMock(TransactionHandler::class),
            $this->configResolver,
            $this->createMock(DomainMapper::class)
        );
    }

    /**
     * @dataProvider invitationProvider
     */
    public function testIsValid(
        InvitationContract $invitation,
        string $usedInSiteAccessName,
        bool $isValid
    ): void {
        $this->siteAccessService
            ->method('getCurrent')
            ->willReturn(new SiteAccess($usedInSiteAccessName));

        $this->configResolver
            ->method('getParameter')
            ->with(
                'user_invitation.hash_expiration_time',
                null,
                $invitation->getSiteAccessIdentifier()
            )->willReturn('P2D');

        self::assertSame(
            $isValid,
            $this->invitationService->isValid($invitation)
        );
    }

    public function invitationProvider(): array
    {
        return [
            'valid' => [
                new Invitation(
                    'test@ibexa.co',
                    'random_hash',
                    (new DateTime())->sub(new DateInterval('PT2H')),
                    'admin',
                    false,
                ),
                'admin',
                true,
            ],
            'date_expired' => [
                new Invitation(
                    'test@ibexa.co',
                    'random_hash',
                    (new DateTime())->sub(new DateInterval('P3D')),
                    'admin',
                    false,
                ),
                'admin',
                false,
            ],
            'wrong_sa' => [
                new Invitation(
                    'test@ibexa.co',
                    'random_hash',
                    (new DateTime())->sub(new DateInterval('PT2H')),
                    'admin',
                    false,
                ),
                'site',
                false,
            ],
            'already_used' => [
                new Invitation(
                    'test@ibexa.co',
                    'random_hash',
                    (new DateTime())->sub(new DateInterval('PT2H')),
                    'admin',
                    true,
                ),
                'admin',
                false,
            ],
        ];
    }
}
