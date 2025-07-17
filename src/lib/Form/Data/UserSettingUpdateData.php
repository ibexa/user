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
     * @param array<string, array<string, mixed>> $values
     */
    public function __construct(
        #[Assert\NotBlank]
        private string $identifier,
        private array $values
    ) {
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param array<string, array<string, mixed>> $values
     */
    public function setValues(array $values): void
    {
        $this->values = $values;
    }
}
