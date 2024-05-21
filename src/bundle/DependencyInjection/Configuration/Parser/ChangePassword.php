<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\User\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\AbstractParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class ChangePassword extends AbstractParser
{
    /**
     * Adds semantic configuration definition.
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeBuilder $nodeBuilder Node just under ezpublish.system.<siteaccess>
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('user_change_password')
                ->info('User change password configuration')
                ->children()
                    ->arrayNode('templates')
                        ->info('User change password templates.')
                        ->children()
                            ->scalarNode('form')
                                ->info('Template to use for change password form rendering.')
                            ->end()
                            ->scalarNode('success')
                                ->info('Template to use for change password success view.')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer)
    {
        if (empty($scopeSettings['user_change_password'])) {
            return;
        }

        $settings = $scopeSettings['user_change_password'];

        if (!empty($settings['templates']['form'])) {
            $contextualizer->setContextualParameter(
                'user_change_password.templates.form',
                $currentScope,
                $settings['templates']['form']
            );
        }

        if (!empty($settings['templates']['success'])) {
            $contextualizer->setContextualParameter(
                'user_change_password.templates.success',
                $currentScope,
                $settings['templates']['success']
            );
        }
    }
}
