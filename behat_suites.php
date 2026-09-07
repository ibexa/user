<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

use Behat\Config\Config;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Behat\MinkExtension\Context\MinkContext;
use Ibexa\AdminUi\Behat\BrowserContext\NavigationContext;
use Ibexa\Behat\API\Context\TestContext;
use Ibexa\Behat\API\Context\UserContext;
use Ibexa\Behat\Browser\Context\AuthenticationContext;
use Ibexa\Behat\Browser\Context\BrowserContext;
use Ibexa\User\Behat\Context\UserSetupContext;

return (new Config())
    ->withProfile((new Profile('browser'))
        ->withSuite((new Suite('password'))
            ->withContexts(
                TestContext::class,
                UserContext::class,
                UserSetupContext::class,
                BrowserContext::class,
                AuthenticationContext::class,
                MinkContext::class
            )
            ->withPaths('%paths.base%/vendor/ibexa/user/features/browser/formats.feature'))
        ->withSuite((new Suite('providers'))
            ->withContexts(
                TestContext::class,
                UserContext::class,
                UserSetupContext::class,
                BrowserContext::class,
                AuthenticationContext::class,
                MinkContext::class,
                NavigationContext::class
            )
            ->withPaths('%paths.base%/vendor/ibexa/user/features/browser/loginMethods.feature')));
