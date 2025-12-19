.PHONY: help build up down restart logs shell composer-install test clean

help: ## Mostra esta ajuda
	@echo "Comandos disponíveis:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'

build: ## Constrói os containers
	docker-compose build

up: ## Inicia os containers
	docker-compose up -d

down: ## Para os containers
	docker-compose down

restart: down up ## Reinicia os containers

logs: ## Mostra os logs dos containers
	docker-compose logs -f

logs-hyperf: ## Mostra apenas os logs do Hyperf
	docker-compose logs -f hyperf

logs-postgres: ## Mostra apenas os logs do PostgreSQL
	docker-compose logs -f postgres

shell: ## Acessa o shell do container Hyperf
	docker-compose exec hyperf sh

shell-postgres: ## Acessa o PostgreSQL
	docker-compose exec postgres psql -U wallet-core -d wallet-core

composer-install: ## Instala as dependências do Composer
	docker-compose exec hyperf composer install

composer-update: ## Atualiza as dependências do Composer
	docker-compose exec hyperf composer update

test: ## Executa os testes
	docker-compose exec hyperf composer test

migrate: ## Executa as migrations
	docker-compose exec hyperf php bin/hyperf.php migrate

migrate-rollback: ## Desfaz a última migration
	docker-compose exec hyperf php bin/hyperf.php migrate:rollback

gen-controller: ## Gera um novo controller (use NAME=NomeController)
	docker-compose exec hyperf php bin/hyperf.php gen:controller $(NAME)

gen-model: ## Gera um novo model (use NAME=NomeModel)
	docker-compose exec hyperf php bin/hyperf.php gen:model $(NAME)

clean: ## Limpa o cache e arquivos temporários
	docker-compose exec hyperf rm -rf runtime/container

rebuild: down ## Rebuild completo dos containers
	docker-compose build --no-cache
	docker-compose up -d

install: build up ## Instalação inicial do projeto
	@echo "Aguardando containers iniciarem..."
	@sleep 5
	@echo "✅ Aplicação instalada e rodando!"
	@echo "🚀 Acesse: http://localhost:9501"

