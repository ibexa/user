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
    /** @var array<string, ValueDefinitionInterface> */
    protected array $values;

    public function __construct(array $values = [])
    {
        $this->values = $values;
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
