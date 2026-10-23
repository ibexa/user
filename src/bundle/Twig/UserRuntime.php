<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\User\Twig;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\User\Password\PasswordRequirement;
use Ibexa\User\Password\PasswordRequirementsResolver;
use Twig\Extension\RuntimeExtensionInterface;

final readonly class UserRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private PermissionResolver $permissionResolver,
        private UserService $userService,
        private PasswordRequirementsResolver $passwordRequirementsResolver
    ) {}

    public function getCurrentUser(): User
    {
        return $this->userService->loadUser(
            $this->permissionResolver->getCurrentUserReference()->getUserId()
        );
    }

    /**
     * @param ContentType|null $contentType required
     * on anonymous pages (e.g. password reset); defaults to the current user's content type
     *
     * @return PasswordRequirement[]
     */
    public function getPasswordRequirements(?ContentType $contentType = null): array
    {
        return $this->passwordRequirementsResolver->getRequirements(
            $contentType ?? $this->getCurrentUser()->getContentType()
        );
    }
}
