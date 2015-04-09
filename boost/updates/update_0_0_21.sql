ALTER TABLE nomination_nomination MODIFY years_at_asu SMALLINT;
ALTER TABLE nomination_nomination MODIFY category INTEGER;
ALTER TABLE nomination_nomination MODIFY nominator_first_name VARCHAR(255);
ALTER TABLE nomination_nomination MODIFY nominator_last_name VARCHAR(255);
ALTER TABLE nomination_nomination MODIFY nominator_email VARCHAR(255);
ALTER TABLE nomination_nomination MODIFY nominator_phone VARCHAR(32);
ALTER TABLE nomination_nomination MODIFY nominator_relation VARCHAR(255);
