<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

use Ibexa\Contracts\Test\Core\Bootstrapper\Bootstrapper;

require dirname(__DIR__) . '/vendor/autoload.php';

chdir(__DIR__ . '/..');

(new Bootstrapper())->bootstrap();
