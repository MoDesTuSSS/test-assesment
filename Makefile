.PHONY: help up down restart build logs shell-backend shell-frontend shell-db migrate clean install test

# Default target
help:
	@echo "Available commands:"
	@echo "  make up              - Start all containers"
	@echo "  make down            - Stop all containers"
	@echo "  make restart         - Restart all containers"
	@echo "  make build           - Build all containers"
	@echo "  make logs            - Show logs from all containers"
	@echo "  make logs-backend    - Show backend logs"
	@echo "  make logs-frontend   - Show frontend logs"
	@echo "  make logs-db         - Show database logs"
	@echo "  make shell-backend   - Open shell in backend container"
	@echo "  make shell-frontend  - Open shell in frontend container"
	@echo "  make shell-db        - Open MySQL shell"
	@echo "  make migrate         - Run database migrations"
	@echo "  make make-entity     - Create new entity (Symfony maker)"
	@echo "  make make-migration  - Create new migration"
	@echo "  make test            - Run unit tests"
	@echo "  make clean           - Remove all containers, volumes and images"
	@echo "  make install         - Initial setup (copy env files)"

# Start all containers
up:
	docker-compose up -d

# Stop all containers
down:
	docker-compose down

# Restart all containers
restart: down up

# Build all containers
build:
	docker-compose build

# Show logs
logs:
	docker-compose logs -f

logs-backend:
	docker-compose logs -f backend

logs-frontend:
	docker-compose logs -f frontend

logs-db:
	docker-compose logs -f db

# Open shell in backend container
shell-backend:
	docker-compose exec backend sh

# Open shell in frontend container
shell-frontend:
	docker-compose exec frontend sh

# Open MySQL shell
shell-db:
	docker-compose exec db mysql -u${MYSQL_USER:-symfony_user} -p${MYSQL_PASSWORD:-symfony_pass} ${MYSQL_DATABASE:-news_db}

# Run migrations
migrate:
	docker-compose exec backend php bin/console doctrine:migrations:migrate --no-interaction

# Create new entity
make-entity:
	docker-compose exec backend php bin/console make:entity

# Create new migration
make-migration:
	docker-compose exec backend php bin/console make:migration

# Update database schema
schema-update:
	docker-compose exec backend php bin/console doctrine:schema:update --force

# Run unit tests
test:
	docker-compose exec backend php bin/phpunit tests/Unit

# Clean everything
clean:
	docker-compose down -v --rmi all --remove-orphans

# Initial setup
install:
	@if [ ! -f .env ]; then cp env.template .env; echo "Created .env file"; fi
	@if [ ! -f frontend/.env ]; then cp frontend/env.template frontend/.env; echo "Created frontend/.env file"; fi
	@if [ ! -f backend/.env ]; then cp backend/env.template backend/.env; echo "Created backend/.env file"; fi
	@if [ ! -f backend/.env.local ]; then cp backend/env.template backend/.env.local; echo "Created backend/.env.local file"; fi
	@echo "Setup complete! Run 'make up' to start the project"

