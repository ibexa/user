<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\ExceptionHandler;

/**
 * @internal
 */
interface ActionResultHandler
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function error(string $message, array $parameters = [], ?string $domain = null, ?string $locale = null): void;

    /**
     * @param array<string, mixed> $parameters
     */
    public function success(string $message, array $parameters = [], ?string $domain = null, ?string $locale = null): void;
}
