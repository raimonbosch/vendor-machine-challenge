all: help

.PHONY: help up test build shell run run-interactive install

help: Makefile
	@sed -n 's/^##//p' $<

## shell:               Interactive shell to use commands inside docker
shell:
	docker compose exec vending-machine bash

## test:               Run all tests
test:
	docker compose exec vending-machine composer install
	docker compose exec -e XDEBUG_MODE=coverage vending-machine ./vendor/bin/phpunit tests/VendingMachine/
	docker compose exec vending-machine npm install
	docker compose exec vending-machine npm test

## build:                  Run the necessary services to build
build:
	docker compose build


## install:            Run the necessary services to build, start docker and run tests
install: build up test

## up:                  Run the necessary services to run repo
up:
	docker compose up -d


## run:                  Run vendor machine such as make run EXPR="1, 0.25, 0.25, GET-SODA"
run:
ifndef EXPR
	$(error EXPR is undefined. Example: make run EXPR="1, 0.25, 0.25, GET-SODA")
endif
	docker compose exec vending-machine ./bin/vending-machine "$(EXPR)"

## run-interactive:                  Run vendor machine in a shell GUI
run-interactive:
	docker compose exec vending-machine bash -c "php spark vending_machine:interactive"