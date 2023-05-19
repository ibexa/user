<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\User\Twig;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Twig\Extension\RuntimeExtensionInterface;

final class UserRuntime implements RuntimeExtensionInterface
{
    private PermissionResolver $permissionResolver;

    private UserService $userService;

    public function __construct(
        PermissionResolver $permissionResolver,
        UserService $userService
    ) {
        $this->permissionResolver = $permissionResolver;
        $this->userService = $userService;
    }

    public function getCurrentUser(): User
    {
        return $this->userService->loadUser(
            $this->permissionResolver->getCurrentUserReference()->getUserId()
        );
    }
}
