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
    public function generate()
    {
        return bin2hex(random_bytes(16));
    }
}
