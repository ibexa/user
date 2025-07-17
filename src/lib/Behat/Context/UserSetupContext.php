<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Behat\Context;

use Behat\Behat\Context\Context;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Ibexa\Core\Persistence\Legacy\User\Gateway;

class UserSetupContext implements Context
{
    private const int UNSUPPORTED_USER_HASH = 5;

    public function __construct(
        private readonly Connection $connection
    ) {
    }

    /**
     * @Given a user :login has password in unsupported format
     */
    public function aUserHasPasswordInUnsupportedFormat(string $login): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $update = $queryBuilder
            ->update(Gateway::USER_TABLE, 'u')
            ->set('password_hash_type', self::UNSUPPORTED_USER_HASH)
            ->andWhere(
                $queryBuilder->expr()->eq('u.login', ':login')
            )
            ->setParameter('login', $login, ParameterType::STRING);

        $update->executeStatement();
    }
}
