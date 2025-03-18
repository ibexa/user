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

/*
 * Example configuration:
 * ```yaml
 * ibexa:
 *   system:
 *      default: # configuration per siteaccess or siteaccess group
 *          user_invitation:
 *              hash_expiration_time: P1D
 *              templates:
 *                  mail: "@@App/invitation/mail.html.twig"
 * ```
 */
class UserInvitation extends AbstractParser
{
    /**
     * Adds semantic configuration definition.
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeBuilder $nodeBuilder Node just under ezpublish.system.<siteaccess>
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('user_invitation')
                ->info('User invitation configuration')
                ->children()
                    ->scalarNode('hash_expiration_time')
                        ->defaultValue('P2D')
                    ->end()
                    ->arrayNode('templates')
                        ->info('User invitation templates.')
                        ->children()
                            ->scalarNode('form')
                                ->info('Template to use for registration form rendering.')
                            ->end()
                            ->scalarNode('mail')
                                ->info('Template to use for registration confirmation rendering.')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (empty($scopeSettings['user_invitation'])) {
            return;
        }

        $settings = $scopeSettings['user_invitation'];

        if (!empty($settings['hash_expiration_time'])) {
            $contextualizer->setContextualParameter(
                'user_invitation.hash_expiration_time',
                $currentScope,
                $settings['hash_expiration_time']
            );
        }

        if (!empty($settings['templates']['form'])) {
            $contextualizer->setContextualParameter(
                'user_invitation.templates.form',
                $currentScope,
                $settings['templates']['form']
            );
        }

        if (!empty($settings['templates']['mail'])) {
            $contextualizer->setContextualParameter(
                'user_invitation.templates.mail',
                $currentScope,
                $settings['templates']['mail']
            );
        }
    }
}
