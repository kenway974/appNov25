#!/bin/sh
until php bin/console doctrine:query:sql "SELECT 1" >/dev/null 2>&1
do
  sleep 1
done
exec php-fpm
