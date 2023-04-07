# Demo

* Create a new empty database in your mysql server.
* Copy `settings.sh.example` to new file `settings.sh` and edit `settings.sh` with your db settings.

Then run the demo from the `demo` folder:

```
$ cd demo/
$ ./run_migrations.sh
```

The folder `migrations` contains migrations for this demo as a baseline.

The folder `migrations_extra` is space to try out new migrations in the demo, all of which are ignored by git.  See that folder's README for notes.  Run the extra migrations from the `demo` folder:

```
$ cd demo/
$ ./run_migrations_extra.sh
```
