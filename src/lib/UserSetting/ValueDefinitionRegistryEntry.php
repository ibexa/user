<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
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
    private ValueDefinitionInterface $definition;

    private int $priority;

    /**
     * @param \Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface $definition
     * @param int $priority
     */
    public function __construct(ValueDefinitionInterface $definition, int $priority = 0)
    {
        $this->definition = $definition;
        $this->priority = $priority;
    }

    /**
     * @return \Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface
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
