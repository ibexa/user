<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\User\Invitation\Persistence;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class Invitation extends ValueObject
{
    /**
     * @param array<mixed>|null $limitationValue
     */
    public function __construct(
        private readonly string $email,
        private readonly string $hash,
        private readonly string $siteAccessIdentifier,
        private readonly int $createdAtTimestamp,
        private readonly bool $isUsed,
        private readonly ?int $roleId = null,
        private readonly ?int $groupId = null,
        private readonly ?string $limitation = null,
        private readonly ?array $limitationValue = null
    ) {
        parent::__construct();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getSiteAccessIdentifier(): string
    {
        return $this->siteAccessIdentifier;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getCreatedAtTimestamp(): int
    {
        return $this->createdAtTimestamp;
    }

    public function isUsed(): bool
    {
        return $this->isUsed;
    }

    public function getRoleId(): ?int
    {
        return $this->roleId;
    }

    public function getGroupId(): ?int
    {
        return $this->groupId;
    }

    public function getLimitation(): ?string
    {
        return $this->limitation;
    }

    /**
     * @return array<mixed>|null
     */
    public function getLimitationValue(): ?array
    {
        return $this->limitationValue;
    }
}
