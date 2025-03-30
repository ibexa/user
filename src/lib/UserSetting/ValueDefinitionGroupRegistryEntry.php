<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting;

use ArrayIterator;
use Ibexa\Contracts\User\UserSetting\ValueDefinitionGroupInterface;
use Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface;
use IteratorAggregate;
use Traversable;

/**
 * @internal
 */
final class ValueDefinitionGroupRegistryEntry implements IteratorAggregate
{
    private ValueDefinitionGroupInterface $definition;

    private array $valueDefinitions;

    public function __construct(ValueDefinitionGroupInterface $definition, array $valueDefinitions = [])
    {
        $this->definition = $definition;
        $this->valueDefinitions = $valueDefinitions;
    }

    public function addToValueDefinitions(string $identifier, ValueDefinitionInterface $valueDefinition): void
    {
        $this->valueDefinitions[$identifier] = $valueDefinition;
    }

    public function getValueDefinitions(): array
    {
        return $this->valueDefinitions;
    }

    public function getDefinition(): ValueDefinitionGroupInterface
    {
        return $this->definition;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->valueDefinitions);
    }
}
