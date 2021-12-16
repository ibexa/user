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
    public function error(string $message, array $parameters = [], ?string $domain = null, ?string $locale = null): void;

    public function success(string $message, array $parameters = [], ?string $domain = null, ?string $locale = null): void;
}

class_alias(ActionResultHandler::class, 'EzSystems\EzPlatformUser\ExceptionHandler\ActionResultHandler');
