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

/**
 * Configuration parser for system security configuration.
 */
class Security extends AbstractParser
{
    /**
     * Adds semantic configuration definition.
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeBuilder $nodeBuilder Node just under ezpublish.system.<siteaccess>
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('security')
                ->info('System security configuration.')
                ->children()
                    ->scalarNode('token_interval_spec')
                        ->info('Token ttl as DateInterval. See http://php.net/manual/dateinterval.construct.php')
                        ->isRequired()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param array<string, mixed> $scopeSettings
     * @param string $currentScope
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (empty($scopeSettings['security'])) {
            return;
        }

        $settings = $scopeSettings['security'];
        $keys = ['token_interval_spec'];

        foreach ($keys as $key) {
            if (!isset($settings[$key]) || empty($settings[$key])) {
                continue;
            }

            $contextualizer->setContextualParameter(
                sprintf('security.%s', $key),
                $currentScope,
                $settings[$key]
            );
        }
    }
}
