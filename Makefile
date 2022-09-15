install: composer.json
	composer install
lint:
	symfony console lint:twig templates
test:
	./bin/phpunit
fixtures:
	symfony console doctrine:database:drop --force
	symfony console doctrine:database:create
	symfony console doctrine:migrations:migrate --no-interaction
	symfony console doctrine:fixtures:load --no-interaction
format:
	./vendor/bin/pint
all: install lint test format

.PHONY: install lint test format
