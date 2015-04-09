DROP TABLE nomination_doc;

CREATE TABLE nomination_document (
       id               INT NOT NULL,
       nomination_id    INT NOT NULL REFERENCES nomination_nomination(id),
       uploaded_by      VARCHAR(255),
       description      VARCHAR(255),
       file_path        VARCHAR(1024),
       file_name        VARCHAR(1024),
       orig_file_name   VARCHAR(1024),
       mime_type        VARCHAR(1024),
       PRIMARY KEY(id)
);