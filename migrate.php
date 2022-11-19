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
$migration = new MysqlMigrate();
if (isset($options['c'])) {
  $name = $options['c'];
  $d = date("Ymd_His");
  $filename = "{$location}/{$d}_{$name}.sql";
  $f = fopen($filename, "w") or die("Unable to open file!");
  fwrite($f, "-- TODO");
  fclose($f);
  echo "New migration file: $filename\n\n";
} else {
  $migration->process($location, $options['m'], $options['d'], $options['u'], $options['p']);
}


class MysqlMigrate {
  var $dbname;
  var $db;

  function __construct() {
    date_default_timezone_set('UTC');
  }

  function process($location, $host, $db, $user, $pass) {
    $this->log("mysql migrate (location: '$location', host: '$host', db: '$db', user: '$user', pass: '$pass')");
    $this->dbname = $db;
    $this->create_connection($host, $db, $user, $pass);
    if (is_dir($location)) {
      $this->process_folder($location);
    } else {
      $this->process_file($location);
    }
    $this->db->close();
  }

  private function log($message) {
    echo "$message\n";
  }

  private function create_connection($host, $db, $user, $pass) {
    $this->log("connecting to db: mysqli($host, $user, $pass, $db)");
    $this->db = new mysqli($host, $user, $pass, $db);
    if ($this->db->connect_errno) {
        $n = $mysqli->connect_errno;
        $e = $mysqli->connect_error;
        $this->log("Failed to connect to MySQL: ({$n}) {$e}\n");
        die;
    }
    $this->db->options(MYSQLI_READ_DEFAULT_GROUP,"max_allowed_packet=128M");
  }

  function process_folder($folder) {
    $this->create_migrations_table_if_needed();
    $this->log("processing folder: $folder");
    chdir($folder);
    $files = glob("*.sql");
    $outstanding = array_filter($files, fn($f) => $this->should_apply($f));
    foreach ($outstanding as $file) {
      try {
        $this->process_file($file);
      }
      catch (Exception $e) {
        $msg = $e->getMessage();
        echo "\nFile {$file} exception:\n{$msg}\n";
        echo "Quitting.\n\n";
        die;
      }
      $this->add_migration_to_database($file);
    }
  }

  function create_migrations_table_if_needed() {
    $check_sql = "SELECT TABLE_NAME FROM information_schema.TABLES
      WHERE TABLE_SCHEMA = '{$this->dbname}' AND TABLE_NAME = '_migrations'";
    $res = $this->db->query($check_sql);
    if ($res->num_rows != 0) {
      return;
    }
    $this->log("Creating _migrations table in database");
    if (!$this->db->query("CREATE TABLE _migrations (filename varchar(255), PRIMARY KEY (filename))")) {
      $this->log("Table creation failed: (" . $this->db->errno . ") " . $this->db->error);
      die;
    }
  }

  function should_apply($filename) {
    if (is_dir($filename)) {
      return false;
    }
    $sql = "select filename from _migrations where filename = '{$filename}'";
    $res = $this->db->query($sql);
    return ($res->num_rows == 0);
  }

  function add_migration_to_database($file) {
    if (!$this->db->query("INSERT INTO _migrations values ('$file')")) {
      $this->log("Table insert failed: (" . $this->db->errno . ") " . $this->db->error);
      die;
    }
  }

  function process_file($file) {
    $this->log("  running $file");
    $commands = file_get_contents($file);

    /* execute multi query */
    $this->db->multi_query($commands);
    do {
      $this->db->store_result();
      if ($this->db->info) {
        $this->log($this->db->info);
      }
    } while ($this->db->next_result());

    if ($this->db->error) {
      $this->log("error:");
      $this->log($this->db->error);
      die;
    }
  }

}

?>