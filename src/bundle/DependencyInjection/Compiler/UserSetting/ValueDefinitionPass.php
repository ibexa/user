<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\User\DependencyInjection\Compiler\UserSetting;

use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\User\UserSetting\Setting\GenericGroup;
use Ibexa\User\UserSetting\ValueDefinitionRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ValueDefinitionPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public const TAG_NAME = 'ezplatform.admin_ui.user_setting.value';

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(ValueDefinitionRegistry::class)) {
            return;
        }

        $registryDefinition = $container->getDefinition(ValueDefinitionRegistry::class);
        $taggedServiceIds = $this->findAndSortTaggedServices(self::TAG_NAME, $container);
        $groupServices = $this->findAndSortTaggedServices('ibexa.user.user_setting.group', $container);

        foreach ($groupServices as $groupService) {
            $groupServiceId = (string)$groupService;
            $tags = $container->getDefinition($groupServiceId)->getTag('ibexa.user.user_setting.group');
            foreach ($tags as $tag) {
                if (!isset($tag['identifier'])) {
                    throw new InvalidArgumentException(
                        $groupServiceId,
                        sprintf("Tag '%s' must contain an 'identifier' argument.", 'ibexa.user.user_setting.group')
                    );
                }

                $registryDefinition->addMethodCall('addValueDefinitionGroup', [
                    $tag['identifier'],
                    $groupService
                ]);
            }
        }


        foreach ($taggedServiceIds as $taggedService) {
            $settingServiceId = (string)$taggedService;
            $tags = $container->getDefinition($settingServiceId)->getTag(self::TAG_NAME);
            foreach ($tags as $tag) {
                if (!isset($tag['identifier'])) {
                    throw new InvalidArgumentException(
                        $settingServiceId,
                        sprintf("Tag '%s' must contain an 'identifier' argument.", self::TAG_NAME)
                    );
                }

                $registryDefinition->addMethodCall('addValueDefinition', [
                    $tag['identifier'],
                    $taggedService,
                    $tag['group'] ?? 'generic',
                ]);
            }
        }
    }
}

class_alias(ValueDefinitionPass::class, 'EzSystems\EzPlatformUserBundle\DependencyInjection\Compiler\UserSetting\ValueDefinitionPass');
