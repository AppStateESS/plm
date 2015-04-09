alter table plm_nominator add column doc_id int references plm_doc(id);
alter table plm_reference add column doc_id int references plm_doc(id);

create table plm_doc (
    id int not null,
    name varchar(255) not null,
    primary key(id)
);
