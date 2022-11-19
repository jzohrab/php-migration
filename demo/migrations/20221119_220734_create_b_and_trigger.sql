create table b (BText varchar(200));

/* New column. */
alter table b add column (BTextLC varchar(200));

/* Update any existing data. */
update b set BTextLC = LOWER(BText);

/* Add trigger for new ones. */
create trigger b_trig_lcase before insert on b for each row set NEW.BTextLC = LOWER(NEW.BText);
