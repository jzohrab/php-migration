alter table x add column(a_plus int);

create trigger x_trig before insert on x for each row set NEW.a_plus = NEW.a + 1000;
