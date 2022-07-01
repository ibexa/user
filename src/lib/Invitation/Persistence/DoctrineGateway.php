<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Invitation\Persistence;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\User\Invitation\Persistence\Gateway;
use Ibexa\Contracts\User\Invitation\Persistence\InvitationUpdateStruct;
use Ibexa\Contracts\User\Invitation\Query\InvitationFilter;

/**
 * @internal
 */
final class DoctrineGateway implements Gateway
{
    private const TABLE_USER_INVITATIONS = 'ibexa_user_invitations';
    private const TABLE_USER_INVITATIONS_ASSIGNMENTS = 'ibexa_user_invitations_assignments';

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function addInvitation(
        string $email,
        string $siteAccessName,
        string $hash,
        ?int $roleId = null,
        ?int $userGroupId = null,
        ?string $limitation = null,
        ?string $limitationValue = null
    ): array {
        $query = $this->connection->createQueryBuilder();
        $query
            ->insert(self::TABLE_USER_INVITATIONS)
            ->values(
                [
                    'email' => $query->createPositionalParameter($email),
                    'site_access_name' => $query->createPositionalParameter($siteAccessName),
                    'hash' => $query->createPositionalParameter($hash),
                    'creation_date' => time(),
                    'used' => $query->createPositionalParameter(false, ParameterType::BOOLEAN),
                ]
            );

        $query->execute();
        $invitationId = $this->connection->lastInsertId(self::TABLE_USER_INVITATIONS);

        $assigmentQuery = $this->connection->createQueryBuilder();
        $assigmentQuery
            ->insert(self::TABLE_USER_INVITATIONS_ASSIGNMENTS)
            ->values(
                [
                    'invitation_id' => $assigmentQuery->createPositionalParameter($invitationId, ParameterType::INTEGER),
                    'user_group_id' => $assigmentQuery->createPositionalParameter($userGroupId, ParameterType::INTEGER),
                    'role_id' => $assigmentQuery->createPositionalParameter($roleId, ParameterType::INTEGER),
                    'limitation_type' => $assigmentQuery->createPositionalParameter($limitation),
                    'limitation_value' => $assigmentQuery->createPositionalParameter($limitationValue),
                ]
            );
        $assigmentQuery->execute();

        return $this->getInvitationByEmail($email);
    }

    public function getInvitation(
        string $hash
    ) {
        $query = $this->getSelectQuery();
        $query->where(
            $query->expr()->eq(
                't1.hash',
                $query->createPositionalParameter($hash)
            )
        );

        $statement = $query->execute();

        return $statement->fetchAssociative();
    }

    public function invitationExistsForEmail(
        string $email
    ): bool {
        $query = $this->connection->createQueryBuilder();
        $query
            ->select(1)
            ->from(self::TABLE_USER_INVITATIONS)
            ->where(
                $query->expr()->eq(
                    'email',
                    $query->createPositionalParameter($email)
                )
            );

        $statement = $query->execute();

        return (bool) $statement->fetchOne();
    }

    public function getInvitationByEmail(string $email)
    {
        $query = $this->getSelectQuery();
        $query->where(
            $query->expr()->eq(
                't1.email',
                $query->createPositionalParameter($email)
            )
        );

        $statement = $query->execute();

        return $statement->fetchAssociative();
    }

    private function getSelectQuery(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder();

        return $query
            ->select(
                't1.email',
                't1.hash',
                't1.site_access_name',
                't1.creation_date',
                't1.used',
                't2.role_id',
                't2.user_group_id',
                't2.limitation_type',
                't2.limitation_value'
            )
            ->from(self::TABLE_USER_INVITATIONS, 't1')
            ->leftJoin(
                't1',
                self::TABLE_USER_INVITATIONS_ASSIGNMENTS,
                't2',
                't1.id = t2.invitation_id'
            );
    }

    public function findInvitations(?InvitationFilter $filter = null): array
    {
        $query = $this->getSelectQuery();

        if ($filter === null) {
            $statement = $query->execute();

            return $statement->fetchAllAssociative();
        }

        if ($filter->getRole() !== null) {
            $query
                ->andWhere(
                    $query->expr()->eq(
                        't2.role_id',
                        $query->createPositionalParameter($filter->getRole()->id)
                    )
                );
        }

        if ($filter->getUserGroup() !== null) {
            $query
                ->andWhere(
                    $query->expr()->eq(
                        't2.user_group_id',
                        $query->createPositionalParameter($filter->getUserGroup()->id)
                    )
                );
        }

        if ($filter->getIsUsed() !== null) {
            $query
                ->andWhere(
                    $query->expr()->eq(
                        't1.used',
                        $query->createPositionalParameter($filter->getIsUsed())
                    )
                );
        }

        $statement = $query->execute();

        return $statement->fetchAllAssociative();
    }

    public function updateInvitation(string $hash, InvitationUpdateStruct $updateStruct): void
    {
        $query = $this->connection->createQueryBuilder();
        $query->update(self::TABLE_USER_INVITATIONS);

        $fieldsForUpdateMap = [
            'creation_date' => [
                'value' => $updateStruct->getCreatedAt(),
                'type' => ParameterType::INTEGER,
            ],
            'used' => [
                'value' => $updateStruct->getIsUsed(),
                'type' => ParameterType::BOOLEAN,
            ],
        ];

        foreach ($fieldsForUpdateMap as $fieldName => $field) {
            if (null === $field['value']) {
                continue;
            }
            $query->set(
                $fieldName,
                $query->createNamedParameter($field['value'], $field['type'], ":{$fieldName}")
            );
        }

        $query->where(
            $query->expr()->eq(
                'hash',
                $query->createNamedParameter($hash, ParameterType::STRING, ':hash')
            )
        );

        $query->execute();
    }
}
