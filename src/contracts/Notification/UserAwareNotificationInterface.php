<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\User\Notification;

use Ibexa\Contracts\Core\Repository\Values\User\User;

interface UserAwareNotificationInterface
{
    public function getUser(): User;
}
