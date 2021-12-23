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
use Ibexa\User\UserSetting\Group\CustomGroup;

/**
 * @internal
 */
class ValueDefinitionRegistry
{
    /** @var \Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface[] */
    protected $valueDefinitions;

    /** @var \Ibexa\Contracts\User\UserSetting\ValueDefinitionGroupInterface[] */
    protected $groupedDefinitions;

    public function __construct(array $valueDefinitions = [])
    {
        $this->valueDefinitions = [];
        foreach ($valueDefinitions as $identifier => $valueDefinition) {
            $this->valueDefinitions[$identifier] = $valueDefinition;
        }
        $this->groupedDefinitions[CustomGroup::CUSTOM_GROUP_IDENTIFIER] = $valueDefinitions;
    }

    /**
     * @param string $identifier
     * @param \Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface $valueDefinition
     * @param int $priority
     */
    public function addValueDefinition(
        string $identifier,
        ValueDefinitionInterface $valueDefinition,
        ?string $groupIdentifier = null
    ): void {
        $this->valueDefinitions[$identifier] = $valueDefinition;

        if ($groupIdentifier) {
            $this->groupedDefinitions[$groupIdentifier]->addToGroup(
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

    public function getValueDefinitionGroups(): array
    {
        return $this->groupedDefinitions;
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

        return $this->valueDefinitions[$identifier];
    }


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
        return $this->valueDefinitions;
    }

    /**
     * @return int
     */
    public function countValueDefinitions(): int
    {
        return \count($this->valueDefinitions);
    }

    public function countValueDefinitionGroups(): int
    {
        return \count($this->groupedDefinitions);
    }

}

class_alias(ValueDefinitionRegistry::class, 'EzSystems\EzPlatformUser\UserSetting\ValueDefinitionRegistry');
