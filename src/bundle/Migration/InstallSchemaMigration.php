<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\User\Migration;

use DateTimeImmutable;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ibexa\Contracts\DoctrineMigrations\Migrations\IbexaMigrationInterface;

final class InstallSchemaMigration extends AbstractMigration implements IbexaMigrationInterface
{
    public function getDescription(): string
    {
        return 'Creates the ibexa/user database schema';
    }

    public static function getTargetVersion(): string
    {
        return '4.6.0';
    }

    public static function getCreationDate(): DateTimeImmutable
    {
        return new DateTimeImmutable('2026-07-20 00:00:00');
    }

    public function up(Schema $schema): void
    {
        if ($this->platform instanceof AbstractMySQLPlatform) {
            $this->addSql('CREATE TABLE ibexa_user_invitations (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, site_access_name VARCHAR(255) NOT NULL, hash VARCHAR(255) NOT NULL, creation_date INT NOT NULL, used TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX ibexa_user_invitations_email_idx (email), INDEX ibexa_user_invitations_hash_idx (hash), UNIQUE INDEX ibexa_user_invitations_email_uindex (email(191)), UNIQUE INDEX ibexa_user_invitations_hash_uindex (hash(191)), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE TABLE ibexa_user_invitations_assignments (id INT AUTO_INCREMENT NOT NULL, invitation_id INT NOT NULL, user_group_id INT DEFAULT NULL, role_id INT DEFAULT NULL, limitation_type VARCHAR(255) DEFAULT NULL, limitation_value VARCHAR(255) DEFAULT NULL, INDEX IDX_DA5A7872A35D7AF0 (invitation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
            $this->addSql('ALTER TABLE ibexa_user_invitations_assignments ADD CONSTRAINT ibexa_user_invitations_assignments_ibexa_user_invitations_id_fk FOREIGN KEY (invitation_id) REFERENCES ibexa_user_invitations (id) ON UPDATE CASCADE ON DELETE CASCADE');

        } elseif ($this->platform instanceof PostgreSQLPlatform) {
            $this->addSql('CREATE TABLE ibexa_user_invitations (id SERIAL NOT NULL, email VARCHAR(255) NOT NULL, site_access_name VARCHAR(255) NOT NULL, hash VARCHAR(255) NOT NULL, creation_date INT NOT NULL, used BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE INDEX ibexa_user_invitations_email_idx ON ibexa_user_invitations (email)');
            $this->addSql('CREATE INDEX ibexa_user_invitations_hash_idx ON ibexa_user_invitations (hash)');
            $this->addSql('CREATE UNIQUE INDEX ibexa_user_invitations_email_uindex ON ibexa_user_invitations (email)');
            $this->addSql('CREATE UNIQUE INDEX ibexa_user_invitations_hash_uindex ON ibexa_user_invitations (hash)');
            $this->addSql('CREATE TABLE ibexa_user_invitations_assignments (id SERIAL NOT NULL, invitation_id INT NOT NULL, user_group_id INT DEFAULT NULL, role_id INT DEFAULT NULL, limitation_type VARCHAR(255) DEFAULT NULL, limitation_value VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE INDEX IDX_DA5A7872A35D7AF0 ON ibexa_user_invitations_assignments (invitation_id)');
            $this->addSql('ALTER TABLE ibexa_user_invitations_assignments ADD CONSTRAINT ibexa_user_invitations_assignments_ibexa_user_invitations_id_fk FOREIGN KEY (invitation_id) REFERENCES ibexa_user_invitations (id) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        } elseif ($this->platform instanceof SqlitePlatform) {
            $this->addSql('CREATE TABLE ibexa_user_invitations (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(255) NOT NULL, site_access_name VARCHAR(255) NOT NULL, hash VARCHAR(255) NOT NULL, creation_date INTEGER NOT NULL, used BOOLEAN DEFAULT \'0\' NOT NULL)');
            $this->addSql('CREATE INDEX ibexa_user_invitations_email_idx ON ibexa_user_invitations (email)');
            $this->addSql('CREATE INDEX ibexa_user_invitations_hash_idx ON ibexa_user_invitations (hash)');
            $this->addSql('CREATE UNIQUE INDEX ibexa_user_invitations_email_uindex ON ibexa_user_invitations (email)');
            $this->addSql('CREATE UNIQUE INDEX ibexa_user_invitations_hash_uindex ON ibexa_user_invitations (hash)');
            $this->addSql('CREATE TABLE ibexa_user_invitations_assignments (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, invitation_id INTEGER NOT NULL, user_group_id INTEGER DEFAULT NULL, role_id INTEGER DEFAULT NULL, limitation_type VARCHAR(255) DEFAULT NULL, limitation_value VARCHAR(255) DEFAULT NULL, CONSTRAINT ibexa_user_invitations_assignments_ibexa_user_invitations_id_fk FOREIGN KEY (invitation_id) REFERENCES ibexa_user_invitations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
            $this->addSql('CREATE INDEX IDX_DA5A7872A35D7AF0 ON ibexa_user_invitations_assignments (invitation_id)');

        }
    }
}
