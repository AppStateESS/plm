CREATE TABLE plm_nominee (
       id           INT NOT NULL DEFAULT 1,
       first_name   VARCHAR(64) NOT NULL ,
       middle_name  VARCHAR(64) ,
       last_name    VARCHAR(64) NOT NULL ,
       email        VARCHAR(255) NOT NULL,
       position     VARCHAR(255) ,
       major        VARCHAR(64) , 
       years        SMALLINT default 0,
       PRIMARY KEY (id)
);

CREATE TABLE plm_nominator (
       id           INT NOT NULL DEFAULT 1 ,
       first_name   VARCHAR(64) NOT NULL ,
       middle_name  VARCHAR(64) ,
       last_name    VARCHAR(64) NOT NULL ,
       email        VARCHAR(255) NOT NULL ,
       phone        VARCHAR(32) NOT NULL ,
       address      VARCHAR(255) ,
       unique_id    VARCHAR(32) ,
       doc_id       INT NULL REFERENCES plm_doc(id),
       PRIMARY KEY (id)
);

CREATE TABLE plm_nomination (
       id               INT NOT NULL DEFAULT 1,
       nominee_id       INT NOT NULL ,
       nominator_id     INT NOT NULL ,
       reference_id_1   INT NULL ,
       reference_id_2   INT NULL ,
       reference_id_3   INT NULL ,
       category         INT NOT NULL ,
       nominator_relationship   VARCHAR(255) NOT NULL ,
       reference_relationship_1 VARCHAR(255) NULL ,
       reference_relationship_2 VARCHAR(255) NULL ,
       reference_relationship_3 VARCHAR(255) NULL ,
       completed                SMALLINT DEFAULT 0,
       period                   SMALLINT NOT NULL ,
       added_on                 INT NOT NULL ,
       updated_on               INT NOT NULL ,
       winner                   SMALLINT DEFAULT NULL,
       PRIMARY KEY (id)
);

CREATE TABLE plm_reference (
       id           INT NOT NULL DEFAULT 1,
       first_name   VARCHAR(64) NOT NULL ,
       middle_name  VARCHAR(64) ,
       last_name    VARCHAR(64) NOT NULL ,
       department   VARCHAR(64) ,
       email        VARCHAR(255),
       phone        VARCHAR(32) ,
       unique_id    VARCHAR(32) ,
       doc_id       INT NULL REFERENCES plm_doc(id),
       PRIMARY KEY (id)
);
 
CREATE TABLE plm_period (
       id         INT NOT NULL DEFAULT 1,
       year       SMALLINT NOT NULL, 
       start_date INT,
       end_date   INT,
       PRIMARY KEY (id)
);

CREATE TABLE plm_doc (
       id         INT NOT NULL,
       name       VARCHAR(255) NOT NULL,
       PRIMARY KEY(id)
);

CREATE TABLE plm_email_log (
       id            INT NOT NULL DEFAULT 1,
       nominee_id    INT NOT NULL,
       message       TEXT,
       message_type  CHAR(6) NOT NULL,
       subject       TEXT,
       receiver_id   INT NOT NULL,
       receiver_type CHAR(3) NOT NULL,
       sent_on       INT NOT NULL,
       PRIMARY KEY(id)
);

CREATE TABLE plm_cancel_queue (
       nomination INTEGER NOT NULL REFERENCES plm_nomination(id),
       PRIMARY KEY(nomination)
);
