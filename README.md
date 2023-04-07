# Database migrations

A simple db migration tool using a PDO connection.  The schema is
managed following the ideas outlined at
https://github.com/jzohrab/DbMigrator/blob/master/docs/managing_database_changes.md:

* Baseline schema and reference data are in `baseline`.
* All one-time migrations are stored in the `migrations` folder, and are applied once only, in filename-sorted order.
* All repeatable migrations are stored in the `migrations_repeatable` folder, and are applied every single migration run.

Code only tested manually, but it's very simple.

The database being migrated must contain a table called '_migrations' with a single field, 'filename'.

Usage in this project:

```
# Copy the sqlite baseline to db/
php run_baseline.php

# Migrate that db in db/
php run_migrations.php

# Migrate it again, so see that only the repeatable migs are run:
php run_migrations.php
```