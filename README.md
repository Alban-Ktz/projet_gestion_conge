#Demande de congés

## Build & Run the containers
*$ make build
*$ make run

## Database
$ make console doctrine:database:create
$ make console make:migration
$ make console doctrine:migrations:migrate

## Create an admin user
$ make console app:create-user

## Link
http://localhost:8000/
