<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Invitation;

use Ibexa\Contracts\Core\HashGenerator;

final readonly class InvitationHashGenerator implements HashGenerator
{
    public function __construct(
        private int $length = 16
    ) {
    }

    public function generate(): string
    {
        return bin2hex(random_bytes($this->length));
    }
}
