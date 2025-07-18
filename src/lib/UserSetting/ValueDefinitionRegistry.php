<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting;

use Ibexa\Contracts\User\UserSetting\ValueDefinitionGroupInterface;
use Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

/**
 * @internal
 */
class ValueDefinitionRegistry
{
    /** @var array<string, \Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface> */
    protected array $valueDefinitions;

    /** @var array<string, \Ibexa\Contracts\User\UserSetting\ValueDefinitionGroupInterface> */
    protected array $groupedDefinitions;

    /**
     * @param array<string, \Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface> $valueDefinitions
     */
    public function __construct(
        array $valueDefinitions = []
    ) {
        $this->valueDefinitions = [];
        foreach ($valueDefinitions as $identifier => $valueDefinition) {
            $this->valueDefinitions[$identifier] = $valueDefinition;
        }
    }

    public function addValueDefinition(
        string $identifier,
        ValueDefinitionInterface $valueDefinition,
        ?string $groupIdentifier = null
    ): void {
        $this->valueDefinitions[$identifier] = $valueDefinition;

        if ($groupIdentifier !== null) {
            $this->groupedDefinitions[$groupIdentifier]->addValueDefinition(
                $identifier,
                $valueDefinition
            );
        }
    }

    public function addValueDefinitionGroup(
        string $groupIdentifier,
        ValueDefinitionGroupInterface $valueDefinition
    ): void {
        $this->groupedDefinitions[$groupIdentifier] = $valueDefinition;
    }

    /**
     * @return array<string, \Ibexa\Contracts\User\UserSetting\ValueDefinitionGroupInterface>
     */
    public function getValueDefinitionGroups(): array
    {
        return $this->groupedDefinitions;
    }

    /**
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

        return $this->valueDefinitions[$identifier];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function getValueDefinitionGroup(string $identifier): ValueDefinitionGroupInterface
    {
        if (!isset($this->groupedDefinitions[$identifier])) {
            throw new InvalidArgumentException(
                '$identifier',
                sprintf('There is no ValueDefinition service registered for \'%s\' identifier', $identifier)
            );
        }

        return $this->groupedDefinitions[$identifier];
    }

    public function hasValueDefinition(string $identifier): bool
    {
        return isset($this->valueDefinitions[$identifier]);
    }

    /**
     * @return \Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface[]
     */
    public function getValueDefinitions(): array
    {
        return $this->valueDefinitions;
    }

    public function countValueDefinitions(): int
    {
        return count($this->valueDefinitions);
    }

    public function countValueDefinitionGroups(): int
    {
        return count($this->groupedDefinitions);
    }
}
