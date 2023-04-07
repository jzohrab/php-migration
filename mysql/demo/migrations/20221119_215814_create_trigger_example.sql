alter table a add column(a_plus int);

create trigger a_trig before insert on a for each row set NEW.a_plus = NEW.id + 1000;
