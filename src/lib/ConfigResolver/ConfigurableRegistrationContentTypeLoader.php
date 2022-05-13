<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\ConfigResolver;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\Repository\Repository;

/**
 * Loads the registration content type from a configured, injected content type identifier.
 */
class ConfigurableRegistrationContentTypeLoader implements RegistrationContentTypeLoader
{
    private ConfigResolverInterface $configResolver;

    private Repository $repository;

    private ContentTypeService $contentTypeService;

    public function __construct(
        ConfigResolverInterface $configResolver,
        Repository $repository,
        ContentTypeService $contentTypeService
    ) {
        $this->configResolver = $configResolver;
        $this->repository = $repository;
        $this->contentTypeService = $contentTypeService;
    }

    public function loadContentType(?SiteAccess $siteAccess = null)
    {
        return $this->repository->sudo(
            fn () => $this->contentTypeService->loadContentTypeByIdentifier(
                $this->configResolver->getParameter(
                    'user_registration.user_type_identifier',
                    null,
                    $siteAccess->name ?? null
                )
            )
        );
    }
}

class_alias(ConfigurableRegistrationContentTypeLoader::class, 'EzSystems\EzPlatformUser\ConfigResolver\ConfigurableRegistrationContentTypeLoader');
