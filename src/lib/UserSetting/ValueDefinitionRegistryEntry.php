<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting;

use Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface;

/**
 * @internal
 */
final class ValueDefinitionRegistryEntry
{
    /** @var \EzSystems\EzPlatformUser\UserSetting\ValueDefinitionInterface */
    private $definition;

    /** @var int */
    private $priority;

    /**
     * @param \EzSystems\EzPlatformUser\UserSetting\ValueDefinitionInterface $definition
     * @param int $priority
     */
    public function __construct(ValueDefinitionInterface $definition, int $priority = 0)
    {
        $this->definition = $definition;
        $this->priority = $priority;
    }

    /**
     * @return \EzSystems\EzPlatformUser\UserSetting\ValueDefinitionInterface
     */
    public function getDefinition(): ValueDefinitionInterface
    {
        return $this->definition;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }
}

class_alias(ValueDefinitionRegistryEntry::class, 'EzSystems\EzPlatformUser\UserSetting\ValueDefinitionRegistryEntry');
