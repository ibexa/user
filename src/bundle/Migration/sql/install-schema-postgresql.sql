CREATE TABLE ibexa_user_invitations (id SERIAL NOT NULL, email VARCHAR(255) NOT NULL, site_access_name VARCHAR(255) NOT NULL, hash VARCHAR(255) NOT NULL, creation_date INT NOT NULL, used BOOLEAN DEFAULT 'false' NOT NULL, PRIMARY KEY(id));
-- ibexa:sql-statement-separator
CREATE INDEX ibexa_user_invitations_email_idx ON ibexa_user_invitations (email);
-- ibexa:sql-statement-separator
CREATE INDEX ibexa_user_invitations_hash_idx ON ibexa_user_invitations (hash);
-- ibexa:sql-statement-separator
CREATE UNIQUE INDEX ibexa_user_invitations_email_uindex ON ibexa_user_invitations (email);
-- ibexa:sql-statement-separator
CREATE UNIQUE INDEX ibexa_user_invitations_hash_uindex ON ibexa_user_invitations (hash);
-- ibexa:sql-statement-separator
CREATE TABLE ibexa_user_invitations_assignments (id SERIAL NOT NULL, invitation_id INT NOT NULL, user_group_id INT DEFAULT NULL, role_id INT DEFAULT NULL, limitation_type VARCHAR(255) DEFAULT NULL, limitation_value VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id));
-- ibexa:sql-statement-separator
CREATE INDEX IDX_DA5A7872A35D7AF0 ON ibexa_user_invitations_assignments (invitation_id);
-- ibexa:sql-statement-separator
ALTER TABLE ibexa_user_invitations_assignments ADD CONSTRAINT ibexa_user_invitations_assignments_ibexa_user_invitations_id_fk FOREIGN KEY (invitation_id) REFERENCES ibexa_user_invitations (id) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
