.DEFAULT_GOAL := help

.PHONY: help
help: ## Outputs the help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: init
init: ## Installs dependencies
	yarn install

.PHONY: run
run: ## Starts the development server
	yarn start

.PHONY: clean
clean: ## Deletes the generated content and node_modules
	rm -rf ./node_modules
	rm -rf ./build

.PHONY: build
build: ## Compiles the documentation into static content
	yarn build