<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\User\Invitation\Persistence;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class Invitation extends ValueObject
{
    protected string $email;

    protected string $hash;

    protected string $siteAccessIdentifier;

    protected string $createdAtTimestamp;

    protected bool $isUsed;

    protected ?int $roleId;

    protected ?int $groupId;

    protected ?string $limitation;

    protected ?array $limitationValue;

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
        parent::__construct([
            'email' => $email,
            'hash' => $hash,
            'siteAccessIdentifier' => $siteAccessIdentifier,
            'createdAtTimestamp' => $createdAtTimestamp,
            'isUsed' => $isUsed,
            'roleId' => $roleId,
            'groupId' => $groupId,
            'limitation' => $limitation,
            'limitationValue' => $limitationValue,
        ]);
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
