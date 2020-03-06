# Setup

1) build the docker containers: `docker-compose build` 
2) start the docker containers: `docker-compose up -d`
3) run migrations: `docker exec php php bin/console doctrine:migrations:migrate --no-interaction`
4) load fixtures: `docker exec php php bin/console hautelook:fixtures:load --no-interaction`
5) navigate to `localhost:8080` to see the home page
6) run tests `docker exec php php bin/phpunit`
