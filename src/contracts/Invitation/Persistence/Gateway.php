<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\User\Invitation\Persistence;

use Ibexa\Contracts\User\Invitation\Query\InvitationFilter;

interface Gateway
{
    /**
     * @phpstan-return array{
     *     email: string,
     *     hash: string,
     *     site_access_name: string,
     *     creation_date: int,
     *     used: bool,
     *     role_id: ?int,
     *     user_group_id: ?int,
     *     limitation_type: ?string,
     *     limitation_value: ?string
     *  }
     */
    public function addInvitation(
        string $email,
        string $siteAccessName,
        string $hash,
        ?int $roleId = null,
        ?int $userGroupId = null,
        ?string $limitation = null,
        ?string $limitationValue = null
    ): array;

    public function getInvitation(string $hash);

    public function invitationExistsForEmail(string $email): bool;

    public function getInvitationByEmail(string $email);

    /**
     * @phpstan-return array<array{
     *     email: string,
     *     hash: string,
     *     site_access_name: string,
     *     creation_date: int,
     *     used: bool,
     *     role_id: ?int,
     *     user_group_id: ?int,
     *     limitation_type: ?string,
     *     limitation_value: ?string,
     * }>
     */
    public function findInvitations(?InvitationFilter $filter = null): array;

    public function updateInvitation(string $hash, InvitationUpdateStruct $updateStruct): void;
}
