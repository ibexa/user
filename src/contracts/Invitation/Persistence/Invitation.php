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
    private string $email;

    private string $hash;

    private string $siteAccessIdentifier;

    private string $createdAtTimestamp;

    private bool $isUsed;

    private ?int $roleId;

    private ?int $groupId;

    private ?string $limitation;

    private ?array $limitationValue;

    public function __construct(
        string $email,
        string $hash,
        string $siteAccessIdentifier,
        string $createdAtTimestamp,
        bool $isUsed,
        ?int $roleId = null,
        ?int $groupId = null,
        ?string $limitation = null,
        ?array $limitationValue = null
    ) {
        parent::__construct();

        $this->email = $email;
        $this->hash = $hash;
        $this->siteAccessIdentifier = $siteAccessIdentifier;
        $this->createdAtTimestamp = $createdAtTimestamp;
        $this->isUsed = $isUsed;
        $this->roleId = $roleId;
        $this->groupId = $groupId;
        $this->limitation = $limitation;
        $this->limitationValue = $limitationValue;
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

    public function getCreatedAtTimestamp(): string
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

    public function getLimitationValue(): ?array
    {
        return $this->limitationValue;
    }
}
