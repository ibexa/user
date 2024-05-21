<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\ConfigResolver;

/**
 * Loads the content type used by user registration.
 */
interface RegistrationContentTypeLoader
{
    /**
     * Gets the content type used by user registration.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType
     */
    public function loadContentType();
}
