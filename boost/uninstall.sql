DROP TABLE IF EXISTS plm_nominee;
DROP TABLE IF EXISTS plm_nominator;
DROP TABLE IF EXISTS plm_nomination;
DROP TABLE IF EXISTS plm_reference;
DROP TABLE IF EXISTS plm_permissions;
DROP TABLE IF EXISTS plm_period;
DROP TABLE IF EXISTS plm_doc;
DROP TABLE IF EXISTS plm_email_log;
DROP TABLE IF EXISTS plm_cancel_queue;

DROP TABLE IF EXISTS plm_nominee_seq;
DROP TABLE IF EXISTS plm_nominator_seq;
DROP TABLE IF EXISTS plm_nomination_seq;
DROP TABLE IF EXISTS plm_reference_seq;
DROP TABLE IF EXISTS plm_period_seq;
DROP TABLE IF EXISTS plm_doc_seq;
DROP TABLE IF EXISTS plm_email_log_seq;
DROP TABLE IF EXISTS plm_cancel_queue_seq;

DELETE FROM pulse_schedule where module = 'plm';
DELETE FROM users_groups where name = 'plm_committee';
DELETE FROM mod_settings where module = 'plm';