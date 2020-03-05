# Setup

0) start the docker containers: `docker-compose up -d`
1) run migrations: `docker exec php php bin/console doctrine:migrations:migrate --no-interaction`
2) load fixtures: `docker exec php php bin/console hautelook:fixtures:load --no-interaction`
