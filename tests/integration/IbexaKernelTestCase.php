<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\User;

use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Test\IbexaKernelTestCase as BaseIbexaKernelTestCase;
use Ibexa\Contracts\User\Invitation\InvitationService;

abstract class IbexaKernelTestCase extends BaseIbexaKernelTestCase
{
    protected static function getUserService(): UserService
    {
        return self::getServiceByClassName(UserService::class);
    }

    protected static function getInvitationService(): InvitationService
    {
        return self::getServiceByClassName(InvitationService::class);
    }
}
