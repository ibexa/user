ALTER TABLE ibexa_user_invitations RENAME TO ibexa_user_invitation
-- ibexa:sql-statement-separator
ALTER SEQUENCE ibexa_user_invitations_id_seq RENAME TO ibexa_user_invitation_id_seq
-- ibexa:sql-statement-separator
ALTER INDEX ibexa_user_invitations_email_idx RENAME TO ibexa_user_invitation_email_idx
-- ibexa:sql-statement-separator
ALTER INDEX ibexa_user_invitations_hash_idx RENAME TO ibexa_user_invitation_hash_idx
-- ibexa:sql-statement-separator
ALTER INDEX ibexa_user_invitations_email_uindex RENAME TO ibexa_user_invitation_email_uindex
-- ibexa:sql-statement-separator
ALTER INDEX ibexa_user_invitations_hash_uindex RENAME TO ibexa_user_invitation_hash_uindex
-- ibexa:sql-statement-separator
ALTER TABLE ibexa_user_invitations_assignments RENAME TO ibexa_user_invitation_assignment
-- ibexa:sql-statement-separator
ALTER SEQUENCE ibexa_user_invitations_assignments_id_seq RENAME TO ibexa_user_invitation_assignment_id_seq
-- ibexa:sql-statement-separator
ALTER INDEX IDX_DA5A7872A35D7AF0 RENAME TO IDX_9E1E6F70A35D7AF0
-- ibexa:sql-statement-separator
ALTER TABLE ibexa_user_invitation_assignment DROP CONSTRAINT ibexa_user_invitations_assignments_ibexa_user_invitations_id_fk
-- ibexa:sql-statement-separator
ALTER TABLE ibexa_user_invitation_assignment ADD CONSTRAINT ibexa_user_invitation_assignment_ibexa_user_invitation_id_fk FOREIGN KEY (invitation_id) REFERENCES ibexa_user_invitation(id) ON DELETE CASCADE ON UPDATE CASCADE
