<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Form\Data;

use Ibexa\ContentForms\Data\User\UserCreateData;

class UserRegisterData extends UserCreateData
{
}

class_alias(UserRegisterData::class, 'EzSystems\EzPlatformUser\Form\Data\UserRegisterData');
