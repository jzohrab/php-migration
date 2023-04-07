#!/bin/bash

source ./settings.sh

echo "Running demo with settings: $server $dbname $userid $passwd"

php ../migrate.php -m $server -d $dbname -u $userid -p${passwd} ./migrations/
