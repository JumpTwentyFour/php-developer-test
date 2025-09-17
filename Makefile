ENV_FILE_PATH := ./.env

ifeq ($(wildcard $(ENV_FILE_PATH)),)
$(error .env file is missing. Please ensure the file has been copied from : ./.env.dist)
endif

# Now include the .env file
include $(ENV_FILE_PATH)

# Export all variables from the .env files
export $(shell sed 's/=.*//' $(ENV_FILE_PATH))

# Executables (local)
DOCKER_APP_EXEC = docker compose exec -it php


## —— Bashes ————————————————————————————————————————————————————————————

bash:
	@$(DOCKER_APP_EXEC) bash

build:
	@docker compose build

up:
	@docker compose up

down:
	@docker compose down

## -- Build ----------------------------------------------------------------

composer-install:
	@$(DOCKER_APP_EXEC) composer install

composer-update:
	@$(DOCKER_APP_EXEC) composer update

composer-dump-autoload:
	@$(DOCKER_APP_EXEC) composer dump-autoload


## -- Fixers and validators ----------------------------------------------------------------

fixers:
	@$(DOCKER_APP_EXEC) ./vendor/bin/pint app --config laravel.pint.json || true

validate:
	@echo "Running PHP Mess Detector"
	@$(DOCKER_APP_EXEC) ./vendor/bin/phpmd app text ./phpmd.rules.xml --color

	@echo "Running PHP Code Sniffer"
	@${DOCKER_APP_EXEC} ./vendor/bin/phpcs app -p --colors --warning-severity=0 --report=code --standard=./phpcs.dist.xml -s

	@echo "Clearing PHP Stan cache"
	@${DOCKER_APP_EXEC} ./vendor/bin/phpstan clear-result-cache --memory-limit=1G --configuration ./phpstan.neon

	@echo "Running PHP Stan analysis"
	@${DOCKER_APP_EXEC} ./vendor/bin/phpstan analyse --memory-limit=1G --configuration ./phpstan.neon --error-format=table

validate-md:
	@echo "Running PHP Mess Detector"
	@$(DOCKER_APP_EXEC) ./vendor/bin/phpmd app text ./phpmd.rules.xml --color --baseline-file=./phpmd.baseline.xml

validate-cs:
	@echo "Running PHP Code Sniffer"
	@${DOCKER_APP_EXEC} ./vendor/bin/phpcs app -p --colors --warning-severity=0 --report=code --standard=./phpcs.dist.xml -s

validate-stan:
	@echo "Clearing PHP Stan cache"
	@${DOCKER_APP_EXEC} ./vendor/bin/phpstan clear-result-cache --memory-limit=1G --configuration ./phpstan.neon
	@echo "Running PHP Stan analysis"
	@${DOCKER_APP_EXEC} ./vendor/bin/phpstan analyse --memory-limit=1G --configuration ./phpstan.neon --error-format=table

## -- Unit Testing ----------------------------------------------------------------

.PHONY: tests
tests:
	@make --no-print-directory tests-delete-reports
	@$(DOCKER_APP_EXEC) bash -c 'export XDEBUG_MODE=coverage && export APP_ENV=testing && ./vendor/bin/paratest \
	--configuration ./tests/phpunit.xml \
	--coverage-clover ./tests/reports/phpunit.coverage.xml \
	--coverage-html tests/reports/unit/html/ \
	--coverage-text=tests/reports/unit/phpunit.coverage.txt \
	--log-junit tests/reports/unit/phpunit.junit.xml \
	--processes=$(nproc)'

