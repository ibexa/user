<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\User;

use Ibexa\Bundle\User\DependencyInjection\Compiler\SecurityPass;
use Ibexa\Bundle\User\DependencyInjection\Compiler\UserSetting;
use Ibexa\Bundle\User\DependencyInjection\Configuration\Parser\ChangePassword;
use Ibexa\Bundle\User\DependencyInjection\Configuration\Parser\ForgotPassword;
use Ibexa\Bundle\User\DependencyInjection\Configuration\Parser\Pagination;
use Ibexa\Bundle\User\DependencyInjection\Configuration\Parser\ResetPassword;
use Ibexa\Bundle\User\DependencyInjection\Configuration\Parser\Security;
use Ibexa\Bundle\User\DependencyInjection\Configuration\Parser\UserInvitation;
use Ibexa\Bundle\User\DependencyInjection\Configuration\Parser\UserPreferences;
use Ibexa\Bundle\User\DependencyInjection\Configuration\Parser\UserRegistration;
use Ibexa\Bundle\User\DependencyInjection\Configuration\Parser\UserSettingsUpdateView;
use Ibexa\User\Permission\InvitationPolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IbexaUserBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $core */
        $core = $container->getExtension('ibexa');
        $core->addConfigParser(new Security());
        $core->addConfigParser(new ChangePassword());
        $core->addConfigParser(new Pagination());
        $core->addConfigParser(new UserRegistration());
        $core->addConfigParser(new UserPreferences());
        $core->addConfigParser(new UserSettingsUpdateView());
        $core->addConfigParser(new ForgotPassword());
        $core->addConfigParser(new ResetPassword());
        $core->addConfigParser(new UserInvitation());

        $core->addPolicyProvider(new InvitationPolicyProvider());

        $container->addCompilerPass(new UserSetting\ValueDefinitionPass());
        $container->addCompilerPass(new UserSetting\FormMapperPass());
        $container->addCompilerPass(new SecurityPass());

        $core->addDefaultSettings(__DIR__ . '/Resources/config', ['ibexa_core_default_settings.yaml']);
    }
}
