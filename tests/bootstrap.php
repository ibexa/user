<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

use Ibexa\Contracts\Test\Core\Bootstrapper\BaseFixtureHook;
use Ibexa\Contracts\Test\Core\Bootstrapper\Bootstrapper;
use Ibexa\Contracts\Test\Core\Bootstrapper\SchemaInstallPath;

require dirname(__DIR__) . '/vendor/autoload.php';

chdir(__DIR__ . '/..');

// Selects which of the two coexisting schema install paths this run exercises: the
// SchemaBuilderEvent one by default, or the Doctrine Migrations one with
// IBEXA_TEST_SCHEMA_BUILDER_EVENT_ENABLED=0.
$doctrineMigrationsEnabled = getenv('IBEXA_TEST_SCHEMA_BUILDER_EVENT_ENABLED') === '0';

(new Bootstrapper())->bootstrap(null, SchemaInstallPath::getBootstrapperOptions($doctrineMigrationsEnabled) + [
    // On the Doctrine Migrations path, ibexa/core's ImportDataMigration has already inserted the
    // baseline repository content.
    BaseFixtureHook::class => [BaseFixtureHook::OPTION_LOAD_BASE_FIXTURE => !$doctrineMigrationsEnabled],
]);
