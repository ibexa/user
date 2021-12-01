<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\ConfigResolver;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Core\MVC\ConfigResolverInterface;

/**
 * Loads the registration user group from a configured, injected group ID.
 */
class ConfigurableRegistrationGroupLoader implements RegistrationGroupLoader
{
    /** @var \Ibexa\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    private $repository;

    public function __construct(ConfigResolverInterface $configResolver, Repository $repository)
    {
        $this->configResolver = $configResolver;
        $this->repository = $repository;
    }

    public function loadGroup()
    {
        return $this->repository->sudo(function (Repository $repository) {
            return $repository
                 ->getUserService()
                 ->loadUserGroup(
                     $this->configResolver->getParameter('user_registration.group_id')
                 );
        });
    }
}

class_alias(ConfigurableRegistrationGroupLoader::class, 'EzSystems\EzPlatformUser\ConfigResolver\ConfigurableRegistrationGroupLoader');
