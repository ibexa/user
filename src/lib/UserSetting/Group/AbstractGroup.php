<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting\Group;

use Ibexa\Contracts\User\UserSetting\ValueDefinitionGroupInterface;
use Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface;

abstract class AbstractGroup implements ValueDefinitionGroupInterface
{
    /**
     * @param array<string, \Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface> $values
     */
    public function __construct(
        protected array $values = []
    ) {
    }

    public function addValueDefinition(string $identifier, ValueDefinitionInterface $valueDefinition): void
    {
        $this->values[$identifier] = $valueDefinition;
    }

    public function getValueDefinitions(): array
    {
        return $this->values;
    }
}
