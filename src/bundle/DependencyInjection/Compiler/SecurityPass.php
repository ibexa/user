<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\User\DependencyInjection\Compiler;

use Ibexa\Bundle\User\Security\Authentication\DefaultAuthenticationFailureHandler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SecurityPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition('security.authentication.failure_handler')) {
            $failureHandlerDef = $container->getDefinition('security.authentication.failure_handler');
            $failureHandlerDef->setClass(DefaultAuthenticationFailureHandler::class);
        }
    }
}

class_alias(SecurityPass::class, 'EzSystems\EzPlatformUserBundle\DependencyInjection\Compiler\SecurityPass');
