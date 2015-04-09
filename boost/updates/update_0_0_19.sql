ALTER TABLE plm_reference ADD COLUMN relationship VARCHAR(255);
ALTER TABLE plm_nominator ADD COLUMN relationship VARCHAR(255);

-- Copy relationships from nomination table to correct reference/nominator
UPDATE plm_reference INNER JOIN plm_nomination ON plm_reference.id = plm_nomination.reference_id_1 SET plm_reference.relationship = plm_nomination.reference_relationship_1;

UPDATE plm_reference INNER JOIN plm_nomination ON plm_reference.id = plm_nomination.reference_id_2 SET plm_reference.relationship = plm_nomination.reference_relationship_2;

UPDATE plm_reference INNER JOIN plm_nomination ON plm_reference.id = plm_nomination.reference_id_3 SET plm_reference.relationship = plm_nomination.reference_relationship_3;

UPDATE plm_nominator INNER JOIN plm_nomination ON plm_nominator.id = plm_nomination.nominator_id SET plm_nominator.relationship = plm_nomination.nominator_relationship;

ALTER TABLE plm_nomination DROP COLUMN nominator_relationship;
ALTER TABLE plm_nomination DROP COLUMN reference_relationship_1;
ALTER TABLE plm_nomination DROP COLUMN reference_relationship_2;
ALTER TABLE plm_nomination DROP COLUMN reference_relationship_3;
