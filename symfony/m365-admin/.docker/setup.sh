#!/bin/bash

# Set up the database
php bin/console cache:clear
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction

yarn build

chmod a+rwx -R /var/www/html/var/cache

echo "Startup jobs complete"
