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
	docker-compose exec backend bash

cache:
	docker-compose exec backend bash -c "bin/console c:c"
	docker-compose exec backend bash -c "bin/console c:c --env=test"

init:
	docker-compose up -d
	docker-compose exec backend bash -c "bin/console doctrine:migration:migrate --no-interaction --allow-no-migration"

reset:
		docker-compose exec backend bash -c "bin/console doctrine:schema:drop --force"
		docker-compose exec backend bash -c "bin/console doctrine:migration:migrate --no-interaction --allow-no-migration"

style-check:
	docker-compose exec backend bash -c "vendor/bin/php-cs-fixer fix --using-cache=no --rules=@Symfony --diff --dry-run src/ \
	&& vendor/bin/php-cs-fixer fix --using-cache=no --rules=@Symfony --diff --dry-run tests/"

style-fix:
	docker-compose exec backend bash -c "vendor/bin/php-cs-fixer fix --using-cache=no --rules=@Symfony src/ \
	&& vendor/bin/php-cs-fixer fix --using-cache=no --rules=@Symfony tests/"

static-check:
	docker-compose exec backend bash -c "vendor/bin/phpstan analyse -c phpstan.neon src/"

pre-commit:
	make style-check && make static-check
