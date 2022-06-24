include .env
export

WWW_DIR                 = /var/www/application
WWW_SYMFONY_DIR         = $(WWW_DIR)/symfony
WWW_USER                = www-data
DOCKER                  = docker
DOCKER_COMPOSE          = docker-compose
DOCKER_PHP_CONTAINER    = php-fpm
DOCKER_NGINX_CONTAINER  = webserver
EXEC_IN_PHP             = $(DOCKER_COMPOSE) exec --user $(WWW_USER) -w $(WWW_SYMFONY_DIR) $(DOCKER_PHP_CONTAINER)
EXEC_STD_VERSION        = $(DOCKER_COMPOSE) exec --user $(WWW_USER) -w $(WWW_DIR) $(DOCKER_PHP_CONTAINER)
SYMFONY                 = $(EXEC_IN_PHP) php bin/console
COMPOSER                = $(EXEC_IN_PHP) composer
SF_ENV                  ?= dev

##
## —— 🐋 Docker ——
setup: symfony/.env build run symfony/vendor db-create assets-install assets-build-dev ## Install and starts the project
	$(DOCKER_COMPOSE) up -d

build: ## Builds docker images
	$(DOCKER_COMPOSE) build

run: ## Run docker images (apache, database, php)
	$(DOCKER_COMPOSE) up -d

stop: ## Stops docker images
	$(DOCKER_COMPOSE) down

status: ## Display state of services (a docker-compose.yaml MUST be in the current directory)
	$(DOCKER) ps

nginx-shell: ## Starts a bash shell in the nginx container (if you want to access nginx logs for example)
	$(DOCKER_COMPOSE) exec $(DOCKER_NGINX_CONTAINER) /bin/bash

php-shell: ## Starts a bash shell in the PHP container (if you want to access symfony console for example)
	$(DOCKER_COMPOSE) exec $(DOCKER_PHP_CONTAINER) /bin/bash

.PHONY: build run stop status nginx-shell php-shell


##
## —— 🐘 PHP / Composer ——
composer: ## Runs a composer command in the php container, ex: make composer install
	$(COMPOSER) $(filter-out $@,$(MAKECMDGOALS))

console: ## Runs a Symfony bin/console command in the php container, ex: make console debug:router
	$(SYMFONY) $(filter-out $@,$(MAKECMDGOALS))

debug-router:
	$(SYMFONY) debug:router --show-controllers

.PHONY: composer console debug-router

##
## —— 🌍 Release and deployment ——
release-dry: ## Use sandard-version to make a release and bump project version (DRY-RUN)
	$(EXEC_STD_VERSION) standard-version --dry-run

release-run: ## Use sandard-version to make a release and bump project version
	$(EXEC_STD_VERSION) standard-version
	@echo "Version done, push changes with:"
	@echo "$ git push --follow-tags origin master"

pre-release: ## Use standard-version to create a pre-release (ie: 1.9.3-pre.0)
	$(EXEC_STD_VERSION) standard-version --prerelease pre

.PHONY: release-dry release-run pre-release

##
## —— 📐 Utils ——
db-create: ## Creates the database
	$(SYMFONY) doctrine:database:create --no-interaction --if-not-exists

fixtures: ## Load fixtures in database to have a set of fake data
	$(SYMFONY) doctrine:fixtures:load --no-interaction

migrations: ## Execute migrations
	@make console "doctrine:migrations:migrate --no-interaction --env=$(SF_ENV)"

db-reset: ## Execute all commands to purge and reset db => make db-reset SF_ENV=test
	@make console "doctrine:database:create --if-not-exists --env=$(SF_ENV)"
	@make console "doctrine:schema:drop --force --full-database --env=$(SF_ENV)"
	@make console "doctrine:migrations:version --delete --all --no-interaction --env=$(SF_ENV)"
	@make console "doctrine:migrations:migrate --no-interaction --env=$(SF_ENV)"
	@make console "managician:data:init --env=$(SF_ENV)"
	@make console "doctrine:fixtures:load --append --no-interaction --env=$(SF_ENV)"

.PHONY: db-create fixtures db-reset migrations

##
## —— 📄 Frontend ——
# UNCOMMENT FRONTEND ONLY IF YOU HAVE A FRONT END INSTALL WITH WEBPACK ENCORE
# BY DEFAULT THE BOILERPLATE DOES NOT COME WITH FRONTEND PACKAGES, ONLY API PACKAGES.
#assets-install: ## Install NPM dependencies
#	$(EXEC_IN_PHP) yarn install
#	$(SYMFONY) assets:install --symlink
#
#assets-watch: ## Starts Webpack watcher to compile assets
#	$(EXEC_IN_PHP) yarn encore dev --watch
#
#assets-build-dev: ## Build assets for 'dev' environment
#	$(EXEC_IN_PHP) yarn encore dev
#
#assets-build-prod: ## Build assets for 'prod' environment
#	$(EXEC_IN_PHP) yarn encore production
#
#.PHONY: assets-install assets-watch assets-build-dev assets-build-prod

##
## —— 🍿 Tests ——
test: unit-test functional-test ## Run unit and functional tests

unit-test: symfony/vendor ## Run unit tests
	$(EXEC_IN_PHP) php bin/phpunit --exclude-group functional --coverage-text

functional-test: symfony/vendor ## Run functional tests
	$(EXEC_IN_PHP) php bin/phpunit --group functional

.PHONY: test unit-test functional-test

##
## —— ✍ Code Quality ——
lint: symfony/vendor/bin/php-cs-fixer ## Run php-cs-fixer and prints a diff (http://cs.sensiolabs.org)
	$(EXEC_IN_PHP) $(EXEC_PHP) vendor/bin/php-cs-fixer fix --using-cache=no --verbose

phpstan: symfony/vendor/bin/phpstan
	$(EXEC_IN_PHP) $(EXEC_PHP) vendor/bin/phpstan analyse src tests

phpmd: symfony/vendor/bin/phpmd
	$(EXEC_IN_PHP) $(EXEC_PHP) vendor/bin/phpmd src ansi phpmd.xml

.PHONY: lint phpstan phpmd


# Rules based on files
symfony/vendor:
	$(COMPOSER) install

symfony/.env: symfony/.env.dist
	@if [ -f symfony/.env ]; \
	then\
		echo '\033[1;41m/!\ The .env.dist file has changed. Please check your .env file (this message will not be displayed again).\033[0m';\
		touch symfony/.env;\
		exit 1;\
	else\
		@echo cp symfony/.env.dist symfony/.env;\
		cp symfony/.env.dist symfony/.env;\
	fi

.DEFAULT_GOAL := help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' Makefile | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help

# Manage args for make commands
%:
	@:
