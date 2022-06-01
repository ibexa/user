<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Invitation\Persistence;

use Ibexa\Contracts\User\Invitation\Persistence\Invitation;

final class Mapper implements \Ibexa\Contracts\User\Invitation\Persistence\Mapper
{
    public function extractInvitationFromRow(array $row): Invitation
    {
        return new Invitation(
            $row['email'],
            $row['hash'],
            $row['site_access_name'],
            (int)$row['creation_date'],
            (bool)$row['used'],
            $row['role_id'] !== null ? (int) $row['role_id'] : null,
            $row['user_group_id'] !== null ? (int) $row['user_group_id'] : null,
            $row['limitation_type'],
            $row['limitation_value'] !== null
                ? json_decode($row['limitation_value'], true, 512, JSON_THROW_ON_ERROR)
                : null
        );
    }
}
