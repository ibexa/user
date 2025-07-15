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
final readonly class ValueDefinitionRegistryEntry
{
    public function __construct(
        private ValueDefinitionInterface $definition,
        private int $priority = 0
    ) {
    }

    public function getDefinition(): ValueDefinitionInterface
    {
        return $this->definition;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }
}
