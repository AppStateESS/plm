CREATE TABLE plm_cancel_queue (
       nomination INTEGER NOT NULL REFERENCES plm_nomination(id),
       PRIMARY KEY(nomination)
);
