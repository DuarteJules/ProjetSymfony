text in ".." are command line

Required installation : 
"composer install".

for create the database

.env.local : DATABASERUL MYSQL => your username:password + Change the port

bin/console doctrine:database:create (bin/console d:d:c)

for add a table "make:entity"

for push the change "php bin/console make:migration"

then "php bin/console doctrine:migrations:migrate"

