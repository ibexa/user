<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\User\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Ibexa\Bundle\Core\Command\BackwardCompatibleCommand;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Core\FieldType\User\Type;
use Ibexa\Core\FieldType\User\UserStorage\Gateway\DoctrineStorage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class AuditUserDatabaseCommand extends Command implements BackwardCompatibleCommand
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Doctrine\DBAL\Connection */
    private $connection;

    public function __construct(
        ContentTypeService $contentTypeService,
        UserService $userService,
        Connection $connection
    ) {
        parent::__construct('ibexa:user:audit-database');

        $this->contentTypeService = $contentTypeService;
        $this->userService = $userService;
        $this->connection = $connection;
    }

    protected function configure(): void
    {
        $this->setAliases($this->getDeprecatedAliases());
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $userFieldDefinitions = $this->getUserFieldDefinitions();

        if ($this->isUniqueEmailRequired($userFieldDefinitions)) {
            $output->writeln('<question>Checking email uniqueness...</question>');

            $query = $this->connection->createQueryBuilder();
            $query
                ->select('email')
                ->from(DoctrineStorage::USER_TABLE)
                ->groupBy('email')
                ->having('COUNT(email) > 1');

            $statement = $query->execute();
            $nonUniqueEmails = $statement->fetchAll(FetchMode::ASSOCIATIVE);

            if (!empty($nonUniqueEmails)) {
                $output->writeln('');
                $output->writeln(sprintf('<error>%d non-unique emails found.</error>', count($nonUniqueEmails)));
                $output->writeln('');

                foreach ($nonUniqueEmails as $record) {
                    $output->writeln(sprintf("<info>Users with '%s' email:</info>", $record['email']));

                    $users = $this->userService->loadUsersByEmail($record['email']);
                    foreach ($users as $user) {
                        $output->writeln(sprintf(' - %s [Login: %s]', $user->getName(), $user->login));
                    }
                }
            }
        }

        $output->writeln('');

        $query = $this->connection->createQueryBuilder();
        $query
            ->select('login')
            ->from(DoctrineStorage::USER_TABLE);

        $statement = $query->execute();
        $logins = $statement->fetchAll(FetchMode::ASSOCIATIVE);

        $output->writeln('<question>Checking login format...</question>');

        foreach ($userFieldDefinitions as $userFieldDefinition) {
            $pattern = $userFieldDefinition->fieldSettings[Type::USERNAME_PATTERN];

            $output->writeln('');
            $output->writeln(sprintf("<info>Pattern '%s':</info>", $pattern));

            foreach ($logins as $record) {
                $login = $record['login'];

                if (!preg_match(sprintf('/%s/', $pattern), $login)) {
                    $output->writeln(sprintf(' - Login %s does not match', $login));
                }
            }
        }

        $output->writeln('');
        $output->writeln('Done.');

        return 0;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition[]
     */
    private function getUserFieldDefinitions(): array
    {
        $userFieldDefinitions = [];

        $contentTypeGroups = $this->contentTypeService->loadContentTypeGroups();
        foreach ($contentTypeGroups as $contentTypeGroup) {
            $contentTypes = $this->contentTypeService->loadContentTypes($contentTypeGroup);

            foreach ($contentTypes as $contentType) {
                $fieldDefinitions = $contentType->getFieldDefinitionsOfType('ezuser');
                if (!$fieldDefinitions->isEmpty()) {
                    $userFieldDefinitions[] = $fieldDefinitions->first();
                }
            }
        }

        return $userFieldDefinitions;
    }

    private function isUniqueEmailRequired(array $userFieldDefinitions): bool
    {
        foreach ($userFieldDefinitions as $userFieldDefinition) {
            if ($userFieldDefinition->fieldSettings[Type::REQUIRE_UNIQUE_EMAIL]) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string[]
     */
    public function getDeprecatedAliases(): array
    {
        return ['ezplatform:user:audit_database'];
    }
}

class_alias(AuditUserDatabaseCommand::class, 'EzSystems\EzPlatformUserBundle\Command\AuditUserDatabaseCommand');
