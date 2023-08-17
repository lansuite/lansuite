.DEFAULT_GOAL := help

.PHONY: help
help: ## Outputs the help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: docker-build-dev
docker-build-dev: ## Builds the local docker image for development purpose
	docker build --file ./Dockerfile-development --tag lansuite/lansuite:latest .

.PHONY: docker-build-production-release
docker-build-production-release: ## Builds the local docker image to create a new software release
	docker build --file ./Dockerfile-production-release --tag lansuite/lansuite:prod-release .

.PHONY: docker-rector-dry
docker-rector-dry: ## Runs rector inside docker (Dry run)
	docker-compose run php /code/bin/rector process --dry-run

.PHONY: docker-rector
docker-rector: ## Runs rector inside docker
	docker-compose run php /code/bin/rector process
