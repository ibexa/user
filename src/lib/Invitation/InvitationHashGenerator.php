<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Invitation;

use Ibexa\Contracts\Core\HashGenerator;

final class InvitationHashGenerator implements HashGenerator
{
    private int $length;

    public function __construct(int $length = 16)
    {
        $this->length = $length;
    }

    public function generate()
    {
        return bin2hex(random_bytes($this->length));
    }
}
