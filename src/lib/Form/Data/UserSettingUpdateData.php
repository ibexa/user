<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Form\Data;

use Symfony\Component\Validator\Constraints as Assert;

class UserSettingUpdateData
{
    /**
     * @Assert\NotBlank()
     */
    private string $identifier;

    /**
     * @var array<string, mixed>
     */
    private array $values;

    public function __construct(string $identifier, array $values)
    {
        $this->identifier = $identifier;
        $this->values = $values;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function setValues(array $values): void
    {
        $this->values = $values;
    }
}

class_alias(UserSettingUpdateData::class, 'EzSystems\EzPlatformUser\Form\Data\UserSettingUpdateData');
