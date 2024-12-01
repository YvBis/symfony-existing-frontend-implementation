start:
	docker-compose up -d

stop:
	docker-compose stop

restart:
	make stop && make start

rebuild:
	make stop
	docker-compose build --no-cache
	make start

php:
	docker-compose exec php-smf bash

cache:
	docker-compose exec php-smf bash -c "bin/console c:c"

init:
	docker-compose up -d
	docker-compose exec php-smf bash -c "bin/console doctrine:migration:migrate --no-interaction --allow-no-migration"

reset:
		docker-compose exec php-smf bash -c "bin/console doctrine:schema:drop --force"
		docker-compose exec php-smf bash -c "bin/console doctrine:migration:migrate --no-interaction --allow-no-migration"

style-check:
	docker-compose exec php-smf bash -c "vendor/bin/php-cs-fixer fix --using-cache=no --rules=@PSR12 --diff --dry-run src/ \
	&& vendor/bin/php-cs-fixer fix --using-cache=no --rules=@PSR12 --diff --dry-run tests/"

static-check:
	docker-compose exec php-smf bash -c "vendor/bin/phpstan analyse -c phpstan.neon src/"

pre-commit:
	make style-check && make static-check
