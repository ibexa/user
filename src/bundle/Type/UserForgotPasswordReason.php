<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\User\Type;

class UserForgotPasswordReason
{
    public const MIGRATION = 'migration';
}

class_alias(UserForgotPasswordReason::class, 'EzSystems\EzPlatformUserBundle\Type\UserForgotPasswordReason');
