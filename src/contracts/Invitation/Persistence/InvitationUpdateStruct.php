<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\User\Invitation\Persistence;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class InvitationUpdateStruct extends ValueObject
{
    public function __construct(
        private ?int $createdAt = null,
        private ?bool $isUsed = null,
        private ?string $hash = null
    ) {
        parent::__construct();
    }

    public function getCreatedAt(): ?int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?int $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getIsUsed(): ?bool
    {
        return $this->isUsed;
    }

    public function setIsUsed(?bool $isUsed): void
    {
        $this->isUsed = $isUsed;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(?string $hash): void
    {
        $this->hash = $hash;
    }
}
