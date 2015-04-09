ALTER TABLE plm_reference CHANGE name first_name VARCHAR(32);
ALTER TABLE plm_reference ADD COLUMN middle_name VARCHAR(32);
ALTER TABLE plm_reference ADD COLUMN last_name VARCHAR(32);
ALTER TABLE plm_reference ADD COLUMN unique_id VARCHAR(32);
ALTER TABLE plm_reference DROP COLUMN letter;

ALTER TABLE plm_nominator ADD COLUMN unique_id VARCHAR(32);
ALTER TABLE plm_nominator DROP COLUMN statement;

ALTER TABLE plm_nomination DROP COLUMN statement;
