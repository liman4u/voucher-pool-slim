#!/bin/bash

composer install

cp .env.example .env

>&2 echo "Waiting for MySql to run. Please wait....."
sleep 2
>&2 echo "MySql started :)"
>&2 echo "Running all phpunit tests now...."

./vendor/bin/phinx migrate
>&2 echo "Database migrations done..."

php -S localhost:4000 -t public
