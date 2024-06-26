# Цели
up: docker-compose-up ## Запуск контейнеров
down: docker-compose-down ## Остановка контейнеров
restart: down up ## Перезапуск контейнеров
init: down-clear pull build up project-init ## Инициализация контейнеров

docker-compose-up: ## Запуск контейнеров
	docker-compose up -d

docker-compose-down: ## Остановка контейнеров
	docker-compose down --remove-orphans

down-clear: ## Остановка контейнеров с очисткой
	docker-compose down -v --remove-orphans

pull: ## Получение обновлений образов
	docker-compose pull

build: ## Сборка образов
	docker-compose build

project-init: ## Инициализация приложения
	docker-compose run --rm project-php-cli composer install

cs-fix: ## Запуск PHP Coding Standards Fixer  (/src, /tests)
	docker-compose run --rm project-php-cli ./vendor/bin/php-cs-fixer fix src

phpstan: ## Запуск PHPStan (/src, /tests)
	docker-compose run --rm project-php-cli ./vendor/bin/phpstan analyse --level 5 src tests

colleges-collect: ## Запустить скрапинг
	docker-compose run --rm project-php-cli php bin/console app:collect-colleges

clear-colleges: ## Удалить колледжи
	docker-compose run --rm project-php-cli php bin/console app:clear-colleges

show-colleges: ## Посмотреть колледжи
	docker-compose run --rm project-php-cli php bin/console app:show-colleges

cli: ## Запуск интерактивной оболочки PHP
	docker-compose run --rm project-php-cli

fpm: ## Запуск PHP-FPM контейнера
	docker-compose run --rm project-php-fpm

help: ## Отображение доступных команд
	@echo "Доступные команды:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: up down restart init docker-compose-up docker-compose-down down-clear pull build project-init cli console help
