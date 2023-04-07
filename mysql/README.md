# php-migration

Rails like migrations in PHP.

Create migration .sql scripts in a migration folder, and this migration script applies any un-applied migration scripts to the database.

* The migration scripts can be named anything, but you should name them something like "yyyymmdd_hhmmss_name.sql" to ensure they're run in a good date-sorted order.
* Executed migrations are tracked in a table _migrations that the migrator class creates.

## Usage

### Create migration file example

```
php migrate.php -c create_cats_table ./migrations/
```

### Run all pending migrations

```
php migrate.php -m 127.0.0.1 -d my_database -u username -psecret ./migrations/
```

### Run one migration

```
php migrate.php -m 127.0.0.1 -d my_database -u username -psecret ./migrations/20150118140555_MyFirstMigration.sql
```

## Usage in another project

Any way you want, really.  Some options:

* use the `migrate.php` and `mysql_migrator.php` files in your project as-is
* just use the `mysql_migrator.php` class and add your own execution script wrapper on top of it.

## Demo

See the `demo` folder and its README for some examples.