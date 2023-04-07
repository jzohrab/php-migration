<?php

require __DIR__ . '/lib/pdo_migrator.php';

$dbpath = __DIR__ . '/db/db.sqlite';

$pdo = new \PDO("sqlite:" . $dbpath);
$mig = new PdoMigrator(
  $pdo,
  __DIR__ . '/migrations',
  __DIR__ . '/migrations_repeatable',
  true
);

// Initial load to check it out.
echo "\n\nInitial state, t1:\n";
$res = $pdo->query('select * from t1');
while ($rec = $res->fetch(\PDO::FETCH_NUM)) {
    echo implode(', ', $rec) . "\n";
}


// check stuff out
echo "\n\nGet pending\n";
echo implode("\n", $mig->get_pending()) . "\n";

echo "\n\nProcess\n";
$mig->process();

// Initial load to check it out.
echo "\n\nFinal state, t1:\n";
$res = $pdo->query('select * from t1');
while ($rec = $res->fetch(\PDO::FETCH_NUM)) {
    echo implode(', ', $rec) . "\n";
}
echo "t2:\n";
$res = $pdo->query('select * from t2');
while ($rec = $res->fetch(\PDO::FETCH_NUM)) {
    echo implode(', ', $rec) . "\n";
}

?>