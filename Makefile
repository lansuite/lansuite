.DEFAULT_GOAL := help

.PHONY: help
help: ## Outputs the help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: docker-build-dev
docker-build-dev: ## Builds the local development docker image
	docker build --file ./Dockerfile --tag lansuite/lansuite:dev .

.PHONY: docker-up
docker-up: ## Starts the local docker development setup
	docker-compose up

.PHONY: docker-rector-dry
docker-rector-dry: ## Runs rector inside docker (dry run)
	docker-compose run php /code/bin/rector process --dry-run

.PHONY: docker-rector
docker-rector: ## Runs rector inside docker
	docker-compose run php /code/bin/rector process

.PHONY: docker-compose-install
docker-compose-install: ## Runs composer install inside docker
	docker-compose run php composer install

.PHONY: docker-unit-tests
docker-unit-tests: ## Runs the unit tests inside docker
	docker-compose run php bin/phpunit --display-warnings
