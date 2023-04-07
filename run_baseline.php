<?php

echo "copy new.\n";

// Copy the baseline to the db folder
$src = __DIR__ . '/baseline/baseline.sqlite';
$dest = __DIR__ . '/db/db.sqlite';
copy($src, $dest);

?>