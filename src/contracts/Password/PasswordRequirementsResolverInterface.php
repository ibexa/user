<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\User\Password;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;

interface PasswordRequirementsResolverInterface
{
    /**
     * @return \Ibexa\Contracts\User\Password\PasswordRequirement[]
     */
    public function getRequirements(ContentType $contentType): array;
}
