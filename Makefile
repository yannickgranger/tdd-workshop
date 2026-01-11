.PHONY: help install test coverage clean docs-dev docs-build docs-preview docker-dev docker-build docker-prod

# =============================================================================
# PHP / Workshop
# =============================================================================

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

install: ## Install PHP dependencies
	composer install

test: ## Run PHP tests
	./vendor/bin/phpunit

coverage: ## Run tests with coverage
	XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-text --coverage-html=coverage

clean: ## Clean generated files
	rm -rf vendor coverage .phpunit.cache node_modules website/.vitepress/dist website/.vitepress/cache

# =============================================================================
# Documentation Site (Local Node.js)
# =============================================================================

docs-install: ## Install Node.js dependencies for docs
	npm install

docs-dev: ## Start docs dev server (localhost:5173)
	npm run docs:dev

docs-build: ## Build static docs site
	npm run docs:build

docs-preview: ## Preview built docs site
	npm run docs:preview

# =============================================================================
# Documentation Site (Docker)
# =============================================================================

docker-dev: ## Start docs dev server in Docker (localhost:5173)
	docker compose --profile dev up --build

docker-build: ## Build static site in Docker (output to ./dist)
	docker compose --profile build up --build
	@echo "Static site built in ./dist/"

docker-prod: ## Run production nginx server (localhost:8080)
	docker compose --profile prod up --build -d
	@echo "Production server running at http://localhost:8080"

docker-stop: ## Stop all Docker containers
	docker compose --profile dev --profile prod down

# =============================================================================
# Shortcuts
# =============================================================================

dev: docker-dev ## Alias for docker-dev
prod: docker-prod ## Alias for docker-prod
build: docker-build ## Alias for docker-build
