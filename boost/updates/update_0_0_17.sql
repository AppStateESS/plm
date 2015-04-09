CREATE TABLE plm_email_log (
       id            INT NOT NULL DEFAULT 1,
       nominee_id    INT NOT NULL,
       message       TEXT,
       message_type  CHAR(6) NOT NULL,
       receiver_id   INT NOT NULL,
       receiver_type CHAR(3) NOT NULL,
       sent_on       INT NOT NULL,
       PRIMARY KEY(id)
);
