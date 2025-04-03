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

class UserRegistration extends AbstractParser
{
    /**
     * Adds semantic configuration definition.
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeBuilder $nodeBuilder Node just under ezpublish.system.<siteaccess>
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('user_registration')
                ->info('User registration configuration')
                ->children()
                    ->scalarNode('user_type_identifier')
                        ->info('Content type identifier used for registration.')
                        ->defaultValue('user')
                    ->end()
                    ->scalarNode('group_id')
                        ->info('Content id of the user group where users who register are created.')
                        ->defaultValue(11)
                        ->setDeprecated('ibexa/user', '4.6', 'The child node "%node%" at path "%path%" is deprecated, use group_remote_id instead.')
                    ->end()
                    ->scalarNode('group_remote_id')
                        ->info('Content remote id of the user group where users who register are created - takes precedence over group_id if set.')
                        ->defaultNull()
                    ->end()
                    ->arrayNode('templates')
                        ->info('User registration templates.')
                        ->children()
                            ->scalarNode('form')
                                ->info('Template to use for registration form rendering.')
                            ->end()
                            ->scalarNode('confirmation')
                                ->info('Template to use for registration confirmation rendering.')
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('form')
                        ->info('User registration form configuration.')
                        ->children()
                            ->arrayNode('allowed_field_definitions_identifiers')
                            ->requiresAtLeastOneElement()
                            ->defaultValue(['user_account'])
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer)
    {
        if (empty($scopeSettings['user_registration'])) {
            return;
        }

        $settings = $scopeSettings['user_registration'];

        if (!empty($settings['user_type_identifier'])) {
            $contextualizer->setContextualParameter(
                'user_registration.user_type_identifier',
                $currentScope,
                $settings['user_type_identifier']
            );
        }

        if (!empty($settings['group_id'])) {
            $contextualizer->setContextualParameter(
                'user_registration.group_id',
                $currentScope,
                $settings['group_id']
            );
        }

        if (!empty($settings['group_remote_id'])) {
            $contextualizer->setContextualParameter(
                'user_registration.group_remote_id',
                $currentScope,
                $settings['group_remote_id']
            );
        }

        if (!empty($settings['templates']['form'])) {
            $contextualizer->setContextualParameter(
                'user_registration.templates.form',
                $currentScope,
                $settings['templates']['form']
            );
        }

        if (!empty($settings['templates']['confirmation'])) {
            $contextualizer->setContextualParameter(
                'user_registration.templates.confirmation',
                $currentScope,
                $settings['templates']['confirmation']
            );
        }

        if (!empty($settings['form']['allowed_field_definitions_identifiers'])) {
            $contextualizer->setContextualParameter(
                'user_registration.form.allowed_field_definitions_identifiers',
                $currentScope,
                $settings['form']['allowed_field_definitions_identifiers']
            );
        }
    }
}
