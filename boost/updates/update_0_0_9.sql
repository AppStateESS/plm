ALTER TABLE plm_nominee ADD COLUMN email VARCHAR(255) NOT NULL;
ALTER TABLE plm_nominee CHANGE COLUMN first_name first_name VARCHAR(64) NOT NULL;
ALTER TABLE plm_nominee CHANGE COLUMN middle_name middle_name VARCHAR(64) ;
ALTER TABLE plm_nominee CHANGE COLUMN last_name last_name VARCHAR(64) NOT NULL;

ALTER TABLE plm_nominator CHANGE COLUMN email email VARCHAR(255) NOT NULL;
ALTER TABLE plm_nominator CHANGE COLUMN first_name first_name VARCHAR(64) NOT NULL;
ALTER TABLE plm_nominator CHANGE COLUMN middle_name middle_name VARCHAR(64) ;
ALTER TABLE plm_nominator CHANGE COLUMN last_name last_name VARCHAR(64) NOT NULL;
ALTER TABLE plm_nominator CHANGE COLUMN address address VARCHAR(255);

ALTER TABLE plm_reference CHANGE COLUMN email email VARCHAR(255);
ALTER TABLE plm_reference CHANGE COLUMN first_name first_name VARCHAR(64) NOT NULL;
ALTER TABLE plm_reference CHANGE COLUMN middle_name middle_name VARCHAR(64) ;
ALTER TABLE plm_reference CHANGE COLUMN last_name last_name VARCHAR(64) NOT NULL;
