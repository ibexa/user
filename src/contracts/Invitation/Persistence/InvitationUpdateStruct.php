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
    private ?int $createdAt;

    private ?bool $isUsed;

    public function __construct(
        ?int $createdAt = null,
        ?bool $isUsed = null
    ) {
        parent::__construct();

        $this->createdAt = $createdAt;
        $this->isUsed = $isUsed;
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
}
