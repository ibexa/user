<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ibexa\Contracts\Test\Core\Bootstrapper\DefaultSchemaFilesProvider;
use Ibexa\Contracts\Test\Core\Bootstrapper\SchemaFilesProviderInterface;
use Ibexa\Tests\Integration\User\UserSchemaFilesProvider;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()
        ->set(UserSchemaFilesProvider::class)
        ->args([
            service(DefaultSchemaFilesProvider::class),
            service('kernel'),
        ])
        ->tag(SchemaFilesProviderInterface::TAG, ['priority' => 200]);
};
