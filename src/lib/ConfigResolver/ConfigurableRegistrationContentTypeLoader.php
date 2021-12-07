<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\ConfigResolver;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Loads the registration content type from a configured, injected content type identifier.
 */
class ConfigurableRegistrationContentTypeLoader implements RegistrationContentTypeLoader
{
    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    public function __construct(ConfigResolverInterface $configResolver, Repository $repository)
    {
        $this->configResolver = $configResolver;
        $this->repository = $repository;
    }

    public function loadContentType()
    {
        return $this->repository->sudo(function (Repository $repository) {
            return $repository
                ->getContentTypeService()
                ->loadContentTypeByIdentifier(
                    $this->configResolver->getParameter('user_registration.user_type_identifier')
                );
        });
    }
}

class_alias(ConfigurableRegistrationContentTypeLoader::class, 'EzSystems\EzPlatformUser\ConfigResolver\ConfigurableRegistrationContentTypeLoader');
