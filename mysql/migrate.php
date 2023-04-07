<?php
/**
 * Rails like migration in PHP
 *
 * Can create empty migration SQL script and run this script on specific database if
 * the script never run on it. Migration sql file name must contains timestamp and a
 * description like '20150118140555_DatabaseStructure.sql'
 * The script create a database table called 'migrations' and store already processed
 * migration file timestamp into this tabel. On the next run it will not run already
 * processed scripts based on 'migrations' table.
 *
 * If only one file specified instead of a folder migration will only work on that script
 * and timestamp check will not run.
 *
 * Create migration file example:
 *  php migrate.php -c MyFirstMigration ./migrations/
 *
 * Run pending migrations:
 *  php migrate.php -m 127.0.0.1 -d my_database -u username -p secret ./migrations/
 *
 * Run one migration:
 *  php migrate.php -m 127.0.0.1 -d my_database -u username -p secret ./migrations/20150118140555_MyFirstMigration.sql
 *
 * @author Péter Képes - https://github.com/kepes
 * with small mods afterwards.
 */

require __DIR__ . '/mysql_migrator.php';

ini_set('memory_limit', '512M');

function show_help() {
  echo "Usage: php migrate.php [options] [folder|script_file]\n";
  echo "Options:\n";
  echo " -h   This help\n";
  echo " -m HOSTNAME  Mysql host\n";
  echo " -d DATABASE  Database name\n";
  echo " -u USERNAME  Username\n";
  echo " -p PASSWORD  Password\n";
  echo " -c NAME      Create migration file with given name\n";
  echo "\n";
  exit;
}

$options = getopt("hm:d:u:p:c:");
if ($options == false || isset($options["h"])) {
    show_help();
}

$location = array_pop($argv);
if (isset($options['c'])) {
  $name = $options['c'];
  $d = date("Ymd_His");
  $filename = "{$location}/{$d}_{$name}.sql";
  $f = fopen($filename, "w") or die("Unable to open file!");
  fwrite($f, "-- TODO");
  fclose($f);
  echo "New migration file: $filename\n\n";
} else {
  $migration = new MysqlMigrator();
  $migration->process($location, $options['m'], $options['d'], $options['u'], $options['p']);
}

?>