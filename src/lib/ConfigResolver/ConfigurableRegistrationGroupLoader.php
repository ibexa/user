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
class ConfigurableRegistrationGroupLoader implements RegistrationGroupLoader
{
    private ConfigResolverInterface $configResolver;

    private Repository $repository;

    public function __construct(ConfigResolverInterface $configResolver, Repository $repository)
    {
        $this->configResolver = $configResolver;
        $this->repository = $repository;
    }

    public function loadGroup()
    {
        return $this->repository->sudo(function (Repository $repository): UserGroup {
            return $repository
                ->getUserService()
                ->loadUserGroupByRemoteId(
                    $this->configResolver->getParameter('user_registration.group_remote_id')
                );
        });
    }
}
