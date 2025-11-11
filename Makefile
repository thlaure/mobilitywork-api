DOCKER = docker
DOCKER_COMPOSE = $(DOCKER) compose
EXEC_PHP = $(DOCKER_COMPOSE) exec php

help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

build: ## Build the Docker images
	@$(DOCKER_COMPOSE) build --no-cache

up: ## Start the Docker containers
	@$(DOCKER_COMPOSE) up -d

down: ## Stop the running Docker containers
	@$(DOCKER_COMPOSE) down --remove-orphans

clean: ## Remove all containers, images, volumes and other build artifacts
	@$(DOCKER_COMPOSE) down -v --rmi all --remove-orphans
	@$(DOCKER) system prune -f

bash: ## Run bash
	@$(EXEC_PHP) bash

sf: ## Run any Symfony command
	@$(eval c ?=)
	@$(EXEC_PHP) ./bin/console $(c)

unit: ## Run unit tests
	@$(DOCKER_COMPOSE) exec -e XDEBUG_MODE=coverage php ./vendor/bin/phpunit --testsuite unit --coverage-html coverage-report

phpstan: ## Run PHPStan
	@$(EXEC_PHP) ./vendor/bin/phpstan analyse

csfixer: ## Run CSFixer
	@$(EXEC_PHP) ./vendor/bin/php-cs-fixer  fix --allow-risky=yes

security: ## Run Security Checker enlightn on composer.lock
	@$(EXEC_PHP) ./vendor/bin/security-checker security:check composer.lock
