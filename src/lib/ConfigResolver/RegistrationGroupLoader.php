<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\ConfigResolver;

/**
 * Used to load a user group during registration.
 */
interface RegistrationGroupLoader
{
    /**
     * Loads a parent group.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\UserGroup
     */
    public function loadGroup();
}
