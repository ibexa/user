<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\User;

use Ibexa\Contracts\Test\Core\Bootstrapper\DefaultSchemaFilesProvider;
use Ibexa\Contracts\Test\Core\Bootstrapper\SchemaFilesProviderInterface;
use Symfony\Component\HttpKernel\KernelInterface;

final class UserSchemaFilesProvider implements SchemaFilesProviderInterface
{
    private DefaultSchemaFilesProvider $defaultProvider;

    private KernelInterface $kernel;

    public function __construct(DefaultSchemaFilesProvider $defaultProvider, KernelInterface $kernel)
    {
        $this->defaultProvider = $defaultProvider;
        $this->kernel = $kernel;
    }

    public function getSchemaFiles(): iterable
    {
        yield from $this->defaultProvider->getSchemaFiles();

        yield $this->kernel->locateResource('@IbexaUserBundle/Resources/config/storage/schema.yaml');
    }
}
