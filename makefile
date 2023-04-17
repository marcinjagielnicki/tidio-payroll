# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php-fpm

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP_CONT) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs sh composer vendor sf cc

help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Connect to the PHP FPM container
	$(PHP_CONT) sh -l

phpunit: ## Connect to the PHP FPM container
	$(SYMFONY) d:d:d --force --if-exists -e test
	$(SYMFONY) d:d:c --if-not-exists -e test
	$(SYMFONY) d:m:m -n -e test
	$(PHP_CONT) bin/phpunit

## —— Composer  ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

deptrac: ## Run deptrac
	$(PHP_CONT) vendor/bin/deptrac

fixtures: ## Run fixtures
	$(SYMFONY) d:f:l -n

cs-fix: ## Run cs fixer
	$(PHP_CONT) vendor/bin/ecs check src tests --fix


phpstan: ## Run phpstan
	$(PHP_CONT) vendor/bin/phpstan analyse src tests -l 8

migrations: ## Run migrations
	$(SYMFONY) d:m:m -n

reset-db: ## Reset database
	$(SYMFONY) d:d:drop --force
	$(SYMFONY) d:d:c
	$(SYMFONY) d:m:m -n
	$(SYMFONY) d:f:l -n

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

## —— Symfony  ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf
