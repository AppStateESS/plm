ALTER TABLE plm_period CHANGE COLUMN end_date end_date INT(11);
ALTER TABLE plm_period CHANGE COLUMN start_date start_date INT(11);

ALTER TABLE plm_nomination CHANGE COLUMN period period INT(4) NOT NULL;
ALTER TABLE plm_nomination ADD COLUMN winner SMALLINT DEFAULT NULL;

ALTER TABLE plm_nominee    CHANGE COLUMN years years SMALLINT DEFAULT 0;
ALTER TABLE plm_nomination CHANGE COLUMN period period SMALLINT NOT NULL;
ALTER TABLE plm_nomination CHANGE COLUMN added_on added_on INT NOT NULL;
ALTER TABLE plm_nomination CHANGE COLUMN updated_on updated_on INT NOT NULL;
ALTER TABLE plm_period     CHANGE COLUMN year year SMALLINT NOT NULL;
ALTER TABLE plm_period     CHANGE COLUMN start_date start_date INT;
ALTER TABLE plm_period     CHANGE COLUMN end_date end_date INT;
