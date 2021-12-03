<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting;

use Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

/**
 * @internal
 */
class ValueDefinitionRegistry
{
    /** @var \Ibexa\User\UserSetting\ValueDefinitionRegistryEntry[] */
    protected $valueDefinitions;

    /**
     * @param \Ibexa\User\UserSetting\ValueDefinitionRegistryEntry[] $valueDefinitions
     */
    public function __construct(array $valueDefinitions = [])
    {
        $this->valueDefinitions = [];
        foreach ($valueDefinitions as $identifier => $valueDefinition) {
            $this->valueDefinitions[$identifier] = new ValueDefinitionRegistryEntry($valueDefinition);
        }
    }

    /**
     * @param string $identifier
     * @param \Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface $valueDefinition
     * @param int $priority
     */
    public function addValueDefinition(
        string $identifier,
        ValueDefinitionInterface $valueDefinition,
        int $priority = 0
    ): void {
        $this->valueDefinitions[$identifier] = new ValueDefinitionRegistryEntry($valueDefinition, $priority);
    }

    /**
     * @param string $identifier
     *
     * @return \Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function getValueDefinition(string $identifier): ValueDefinitionInterface
    {
        if (!isset($this->valueDefinitions[$identifier])) {
            throw new InvalidArgumentException(
                '$identifier',
                sprintf('There is no ValueDefinition service registered for \'%s\' identifier', $identifier)
            );
        }

        return $this->valueDefinitions[$identifier]->getDefinition();
    }

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function hasValueDefinition(string $identifier): bool
    {
        return isset($this->valueDefinitions[$identifier]);
    }

    /**
     * @return \Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface[]
     */
    public function getValueDefinitions(): array
    {
        uasort($this->valueDefinitions, static function (ValueDefinitionRegistryEntry $a, ValueDefinitionRegistryEntry $b) {
            return $b->getPriority() <=> $a->getPriority();
        });

        return array_map(static function (ValueDefinitionRegistryEntry $entry) {
            return $entry->getDefinition();
        }, $this->valueDefinitions);
    }

    /**
     * @return int
     */
    public function countValueDefinitions(): int
    {
        return \count($this->valueDefinitions);
    }
}

class_alias(ValueDefinitionRegistry::class, 'EzSystems\EzPlatformUser\UserSetting\ValueDefinitionRegistry');
