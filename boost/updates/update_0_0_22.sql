alter table nomination_reference drop column nominee_id;
alter table nomination_reference add column nomination_id INT NOT NULL REFERENCES nomination_nomination(id);
alter table nomination_reference drop column middle_name;