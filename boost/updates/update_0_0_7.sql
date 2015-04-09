CREATE TABLE plm_period (
       id         INT NOT NULL,
       year       INT(4) NOT NULL UNIQUE, 
       start_date INT(11) NOT NULL,
       end_date   INT(11) NOT NULL,
       PRIMARY KEY (id)
);

ALTER TABLE plm_nomination alter column id set default 1;
ALTER TABLE plm_nominee    alter column id set default 1;
ALTER TABLE plm_nominator  alter column id set default 1;
ALTER TABLE plm_reference  alter column id set default 1;
ALTER TABLE plm_period     alter column id set default 1;