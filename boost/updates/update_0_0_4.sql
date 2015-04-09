ALTER TABLE plm_nominator DROP COLUMN department;
ALTER TABLE plm_nominator DROP COLUMN major;

ALTER TABLE plm_reference CHANGE first_name name varchar(64);
ALTER TABLE plm_reference DROP COLUMN middle_name;
ALTER TABLE plm_reference DROP COLUMN last_name;
ALTER TABLE plm_reference DROP COLUMN major;

ALTER TABLE plm_nomination ADD COLUMN added_on INT NOT NULL;
ALTER TABLE plm_nomination ADD COLUMN updated_on INT;
