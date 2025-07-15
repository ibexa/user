<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\ConfigResolver;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;

/**
 * Loads the registration content type from a configured, injected content type identifier.
 */
readonly class ConfigurableRegistrationContentTypeLoader implements RegistrationContentTypeLoader
{
    public function __construct(
        private ConfigResolverInterface $configResolver,
        private Repository $repository,
        private ContentTypeService $contentTypeService
    ) {
    }

    public function loadContentType(?string $siteAccessIdentifier = null): ContentType
    {
        return $this->repository->sudo(
            fn (): ContentType => $this->contentTypeService->loadContentTypeByIdentifier(
                $this->configResolver->getParameter(
                    'user_registration.user_type_identifier',
                    null,
                    $siteAccessIdentifier
                )
            )
        );
    }
}
