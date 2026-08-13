<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\User;

use Ibexa\Contracts\Core\Test\IbexaKernelTestCase as BaseIbexaKernelTestCase;
use Ibexa\Contracts\User\Invitation\InvitationService;
use Ibexa\User\Invitation\Persistence\DoctrineGateway;

abstract class IbexaKernelTestCase extends BaseIbexaKernelTestCase
{
    protected static function getInvitationService(): InvitationService
    {
        return self::getServiceByClassName(InvitationService::class);
    }

    protected static function getInvitationGateway(): DoctrineGateway
    {
        return self::getServiceByClassName(DoctrineGateway::class);
    }
}
