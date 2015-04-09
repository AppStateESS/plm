ALTER TABLE plm_nomination ADD COLUMN period INT NOT NULL;

CREATE TABLE plm_period (
    id      INT NOT NULL ,
    year    INT NOT NULL ,
    PRIMARY KEY (id)
);
