<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Form;

final class UserFormEvents
{
    /**
     * Triggered when registering an user.
     */
    const USER_REGISTER = 'user.edit.register';
}

class_alias(UserFormEvents::class, 'EzSystems\EzPlatformUser\Form\UserFormEvents');
