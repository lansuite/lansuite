.DEFAULT_GOAL := help

.PHONY: help
help: ## Outputs the help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: docker-build
docker-build: ## Builds the local docker image
	docker build --file ./Dockerfile --tag lansuite/lansuite:latest .

.PHONY: docker-rector-dry
docker-rector-dry: ## Runs rector inside docker (Dry run)
	docker-compose run php /code/bin/rector process --dry-run

.PHONY: docker-rector
docker-rector: ## Runs rector inside docker
	docker-compose run php /code/bin/rector process
