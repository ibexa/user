<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\User\DependencyInjection\Compiler\UserSetting;

use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\User\UserSetting\FormMapperRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FormMapperPass implements CompilerPassInterface
{
    public const TAG_NAME = 'ibexa.user.setting.mapper.form';

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException;
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(FormMapperRegistry::class)) {
            return;
        }

        $registryDefinition = $container->getDefinition(FormMapperRegistry::class);
        $taggedServiceIds = $container->findTaggedServiceIds(self::TAG_NAME);

        foreach ($taggedServiceIds as $taggedServiceId => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['identifier'])) {
                    throw new InvalidArgumentException(
                        $taggedServiceId,
                        sprintf("Tag '%s' must contain an 'identifier' argument.", self::TAG_NAME)
                    );
                }

                $registryDefinition->addMethodCall(
                    'addFormMapper',
                    [$tag['identifier'], new Reference($taggedServiceId)]
                );
            }
        }
    }
}
