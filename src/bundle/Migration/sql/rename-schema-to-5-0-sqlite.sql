ALTER TABLE ibexa_user_invitations RENAME TO ibexa_user_invitation
-- ibexa:sql-statement-separator
DROP INDEX ibexa_user_invitations_email_idx
-- ibexa:sql-statement-separator
CREATE INDEX ibexa_user_invitation_email_idx ON ibexa_user_invitation (email)
-- ibexa:sql-statement-separator
DROP INDEX ibexa_user_invitations_hash_idx
-- ibexa:sql-statement-separator
CREATE INDEX ibexa_user_invitation_hash_idx ON ibexa_user_invitation (hash)
-- ibexa:sql-statement-separator
DROP INDEX ibexa_user_invitations_email_uindex
-- ibexa:sql-statement-separator
CREATE UNIQUE INDEX ibexa_user_invitation_email_uindex ON ibexa_user_invitation (email)
-- ibexa:sql-statement-separator
DROP INDEX ibexa_user_invitations_hash_uindex
-- ibexa:sql-statement-separator
CREATE UNIQUE INDEX ibexa_user_invitation_hash_uindex ON ibexa_user_invitation (hash)
-- ibexa:sql-statement-separator
ALTER TABLE ibexa_user_invitations_assignments RENAME TO ibexa_user_invitation_assignment
-- ibexa:sql-statement-separator
DROP INDEX IDX_DA5A7872A35D7AF0
-- ibexa:sql-statement-separator
CREATE INDEX IDX_9E1E6F70A35D7AF0 ON ibexa_user_invitation_assignment (invitation_id)
