ALTER TABLE plm_nominator DROP COLUMN doc_id;
ALTER TABLE plm_reference DROP COLUMN doc_id;

ALTER TABLE plm_nominator ADD COLUMN doc_id INT NULL REFERENCES plm_doc(id);
ALTER TABLE plm_reference ADD COLUMN doc_id INT NULL REFERENCES plm_doc(id);
