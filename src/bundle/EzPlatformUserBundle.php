<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformUserBundle;

use EzSystems\EzPlatformUserBundle\DependencyInjection\Compiler\UserSetting;
use EzSystems\EzPlatformUserBundle\DependencyInjection\Configuration\Parser\ChangePassword;
use EzSystems\EzPlatformUserBundle\DependencyInjection\Configuration\Parser\UserRegistration;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use EzSystems\EzPlatformUserBundle\DependencyInjection\Configuration\Parser\Security;

class EzPlatformUserBundle extends Bundle
{
    public const ADMIN_GROUP_NAME = 'admin_group';

    public function build(ContainerBuilder $container)
    {
        /** @var \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension $core */
        $core = $container->getExtension('ezpublish');
        $core->addConfigParser(new Security());
        $core->addConfigParser(new ChangePassword());
        $core->addConfigParser(new UserRegistration());
        $container->addCompilerPass(new UserSetting\ValueDefinitionPass());
        $container->addCompilerPass(new UserSetting\FormMapperPass());
        $core->addDefaultSettings(__DIR__ . '/Resources/config', ['ezplatform_default_settings.yml']);
    }
}