<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\ConfigResolver;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;

/**
 * Loads the registration user group from a configured, injected group ID.
 */
readonly class ConfigurableRegistrationGroupLoader implements RegistrationGroupLoader
{
    public function __construct(
        private ConfigResolverInterface $configResolver,
        private Repository $repository
    ) {
    }

    public function loadGroup(): UserGroup
    {
        return $this->repository->sudo(fn (Repository $repository): UserGroup => $repository
            ->getUserService()
            ->loadUserGroupByRemoteId(
                $this->configResolver->getParameter('user_registration.group_remote_id')
            ));
    }
}
