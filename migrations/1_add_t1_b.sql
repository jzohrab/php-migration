alter table t1 add column b integer null;
alter table t1 add column c integer default 42;

update t1 set b = a + 100;
