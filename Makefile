.PHONY: help install test coverage clean

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}'

install: ## Install dependencies
	composer install

test: ## Run tests
	./vendor/bin/phpunit

coverage: ## Run tests with coverage
	XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-text --coverage-html=coverage

clean: ## Clean generated files
	rm -rf vendor coverage .phpunit.cache
