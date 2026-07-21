ALTER TABLE ibexa_user_invitations RENAME TO ibexa_user_invitation
-- ibexa:sql-statement-separator
ALTER TABLE ibexa_user_invitation RENAME INDEX ibexa_user_invitations_email_idx TO ibexa_user_invitation_email_idx
-- ibexa:sql-statement-separator
ALTER TABLE ibexa_user_invitation RENAME INDEX ibexa_user_invitations_hash_idx TO ibexa_user_invitation_hash_idx
-- ibexa:sql-statement-separator
ALTER TABLE ibexa_user_invitation RENAME INDEX ibexa_user_invitations_email_uindex TO ibexa_user_invitation_email_uindex
-- ibexa:sql-statement-separator
ALTER TABLE ibexa_user_invitation RENAME INDEX ibexa_user_invitations_hash_uindex TO ibexa_user_invitation_hash_uindex
-- ibexa:sql-statement-separator
ALTER TABLE ibexa_user_invitations_assignments RENAME TO ibexa_user_invitation_assignment
-- ibexa:sql-statement-separator
ALTER TABLE ibexa_user_invitation_assignment DROP FOREIGN KEY ibexa_user_invitations_assignments_ibexa_user_invitations_id_fk
-- ibexa:sql-statement-separator
ALTER TABLE ibexa_user_invitation_assignment ADD CONSTRAINT ibexa_user_invitation_assignment_ibexa_user_invitation_id_fk FOREIGN KEY (invitation_id) REFERENCES ibexa_user_invitation(id) ON DELETE CASCADE ON UPDATE CASCADE
-- ibexa:sql-statement-separator
ALTER TABLE ibexa_user_invitation_assignment RENAME INDEX IDX_DA5A7872A35D7AF0 TO IDX_9E1E6F70A35D7AF0
