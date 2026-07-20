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

final class RenameSchemaTo5_0Migration extends AbstractMigration implements IbexaMigrationInterface
{
    public function getDescription(): string
    {
        return 'Renames the user-invitation database schema to singular table names (introduced in 5.0)';
    }

    public static function getTargetVersion(): string
    {
        return '5.0.0';
    }

    public static function getCreationDate(): DateTimeImmutable
    {
        return new DateTimeImmutable('2026-07-20 00:00:00');
    }

    public function up(Schema $schema): void
    {
        if ($this->platform instanceof AbstractMySQLPlatform) {
            $this->addSql('ALTER TABLE ibexa_user_invitations RENAME TO ibexa_user_invitation');
            $this->addSql('ALTER TABLE ibexa_user_invitation RENAME INDEX ibexa_user_invitations_email_idx TO ibexa_user_invitation_email_idx');
            $this->addSql('ALTER TABLE ibexa_user_invitation RENAME INDEX ibexa_user_invitations_hash_idx TO ibexa_user_invitation_hash_idx');
            $this->addSql('ALTER TABLE ibexa_user_invitation RENAME INDEX ibexa_user_invitations_email_uindex TO ibexa_user_invitation_email_uindex');
            $this->addSql('ALTER TABLE ibexa_user_invitation RENAME INDEX ibexa_user_invitations_hash_uindex TO ibexa_user_invitation_hash_uindex');
            $this->addSql('ALTER TABLE ibexa_user_invitations_assignments RENAME TO ibexa_user_invitation_assignment');
            $this->addSql('ALTER TABLE ibexa_user_invitation_assignment DROP FOREIGN KEY ibexa_user_invitations_assignments_ibexa_user_invitations_id_fk');
            $this->addSql('ALTER TABLE ibexa_user_invitation_assignment ADD CONSTRAINT ibexa_user_invitation_assignment_ibexa_user_invitation_id_fk FOREIGN KEY (invitation_id) REFERENCES ibexa_user_invitation(id) ON DELETE CASCADE ON UPDATE CASCADE');
            $this->addSql('ALTER TABLE ibexa_user_invitation_assignment RENAME INDEX IDX_DA5A7872A35D7AF0 TO IDX_9E1E6F70A35D7AF0');
        } elseif ($this->platform instanceof PostgreSQLPlatform) {
            $this->addSql('ALTER TABLE ibexa_user_invitations RENAME TO ibexa_user_invitation');
            $this->addSql('ALTER SEQUENCE ibexa_user_invitations_id_seq RENAME TO ibexa_user_invitation_id_seq');
            $this->addSql('ALTER INDEX ibexa_user_invitations_email_idx RENAME TO ibexa_user_invitation_email_idx');
            $this->addSql('ALTER INDEX ibexa_user_invitations_hash_idx RENAME TO ibexa_user_invitation_hash_idx');
            $this->addSql('ALTER INDEX ibexa_user_invitations_email_uindex RENAME TO ibexa_user_invitation_email_uindex');
            $this->addSql('ALTER INDEX ibexa_user_invitations_hash_uindex RENAME TO ibexa_user_invitation_hash_uindex');
            $this->addSql('ALTER TABLE ibexa_user_invitations_assignments RENAME TO ibexa_user_invitation_assignment');
            $this->addSql('ALTER SEQUENCE ibexa_user_invitations_assignments_id_seq RENAME TO ibexa_user_invitation_assignment_id_seq');
            $this->addSql('ALTER INDEX IDX_DA5A7872A35D7AF0 RENAME TO IDX_9E1E6F70A35D7AF0');
            $this->addSql('ALTER TABLE ibexa_user_invitation_assignment DROP CONSTRAINT ibexa_user_invitations_assignments_ibexa_user_invitations_id_fk');
            $this->addSql('ALTER TABLE ibexa_user_invitation_assignment ADD CONSTRAINT ibexa_user_invitation_assignment_ibexa_user_invitation_id_fk FOREIGN KEY (invitation_id) REFERENCES ibexa_user_invitation(id) ON DELETE CASCADE ON UPDATE CASCADE');
        } elseif ($this->platform instanceof SqlitePlatform) {
            $this->addSql('ALTER TABLE ibexa_user_invitations RENAME TO ibexa_user_invitation');
            $this->addSql('DROP INDEX ibexa_user_invitations_email_idx');
            $this->addSql('CREATE INDEX ibexa_user_invitation_email_idx ON ibexa_user_invitation (email)');
            $this->addSql('DROP INDEX ibexa_user_invitations_hash_idx');
            $this->addSql('CREATE INDEX ibexa_user_invitation_hash_idx ON ibexa_user_invitation (hash)');
            $this->addSql('DROP INDEX ibexa_user_invitations_email_uindex');
            $this->addSql('CREATE UNIQUE INDEX ibexa_user_invitation_email_uindex ON ibexa_user_invitation (email)');
            $this->addSql('DROP INDEX ibexa_user_invitations_hash_uindex');
            $this->addSql('CREATE UNIQUE INDEX ibexa_user_invitation_hash_uindex ON ibexa_user_invitation (hash)');
            $this->addSql('ALTER TABLE ibexa_user_invitations_assignments RENAME TO ibexa_user_invitation_assignment');
            $this->addSql('DROP INDEX IDX_DA5A7872A35D7AF0');
            $this->addSql('CREATE INDEX IDX_9E1E6F70A35D7AF0 ON ibexa_user_invitation_assignment (invitation_id)');
        }
    }
}
