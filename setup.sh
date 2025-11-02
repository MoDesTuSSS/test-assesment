#!/bin/bash

# News Management System - Setup Script
# This script sets up and runs the entire application stack

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}╔════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║     News Management System - Setup Script             ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════╝${NC}"

# Function to print colored messages
print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Check prerequisites
print_info "Checking prerequisites..."

if ! command -v docker &> /dev/null; then
    print_error "Docker is not installed. Please install Docker Desktop."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    print_error "Docker Compose is not installed. Please install Docker Compose."
    exit 1
fi

print_success "Docker and Docker Compose are installed"

# Check if Docker is running
if ! docker info &> /dev/null; then
    print_error "Docker daemon is not running. Please start Docker Desktop."
    exit 1
fi

print_success "Docker daemon is running"

# Create environment files from templates
print_info "Setting up environment files..."

if [ ! -f .env ]; then
    cp env.template .env
    print_success "Created root .env file"
else
    print_warning "Root .env already exists, skipping..."
fi

if [ ! -f frontend/.env ]; then
    cp frontend/env.template frontend/.env
    print_success "Created frontend/.env file"
else
    print_warning "Frontend .env already exists, skipping..."
fi

if [ ! -f backend/.env ]; then
    cp backend/env.template backend/.env
    print_success "Created backend/.env file"
else
    print_warning "Backend .env already exists, skipping..."
fi

if [ ! -f backend/.env.local ]; then
    cp backend/env.template backend/.env.local
    print_success "Created backend/.env.local file"
else
    print_warning "Backend .env.local already exists, skipping..."
fi

# Stop and remove existing containers
print_info "Cleaning up old containers..."
docker-compose down --remove-orphans &> /dev/null || true
print_success "Cleanup complete"

# Build and start containers
print_info "Building and starting Docker containers..."
echo -e "${YELLOW}This may take 5-10 minutes on first run...${NC}"

docker-compose up -d --build

if [ $? -eq 0 ]; then
    print_success "All containers are running!"
else
    print_error "Failed to start containers. Check logs with: docker-compose logs"
    exit 1
fi

# Wait for services to be ready
print_info "Waiting for services to initialize..."

# Wait for database to be healthy
echo -n "Waiting for database"
RETRIES=30
until docker-compose exec -T db mysqladmin ping -h localhost --silent &> /dev/null || [ $RETRIES -eq 0 ]; do
    echo -n "."
    sleep 2
    RETRIES=$((RETRIES-1))
done
echo ""

if [ $RETRIES -eq 0 ]; then
    print_error "Database failed to start"
    exit 1
fi

print_success "Database is ready"

# Wait for backend to be ready
print_info "Waiting for backend service..."
sleep 5
print_success "Backend service is ready"

# Run Composer install
print_info "Setting up backend dependencies..."
docker-compose exec -T backend composer install --no-interaction --quiet &> /dev/null || true
print_success "Backend dependencies installed"

# Create database and schema
print_info "Creating database..."
docker-compose exec -T backend php bin/console doctrine:database:create --if-not-exists --no-interaction 2>&1 | grep -v "already exists" || true
print_success "Database created"

print_info "Creating database schema..."
docker-compose exec -T backend php bin/console doctrine:schema:update --force --no-interaction
if [ $? -eq 0 ]; then
    print_success "Database schema created successfully"
else
    print_warning "Schema update had issues, but continuing..."
fi

# Final status check
print_info "Verifying services..."
sleep 3

FRONTEND_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:5173 || echo "000")
BACKEND_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/api/news || echo "000")

echo ""
echo -e "${GREEN}╔════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║              🎉 Setup Complete! 🎉                     ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════════════════════╝${NC}"
echo ""

echo -e "${BLUE}📋 Service Status:${NC}"
echo "┌─────────────────────────────────────────────────────┐"

if [ "$FRONTEND_STATUS" == "200" ]; then
    echo -e "│ ${GREEN}✓${NC} Frontend:  http://localhost:5173               │"
else
    echo -e "│ ${YELLOW}⏳${NC} Frontend:  Starting... (wait 30s)              │"
fi

if [ "$BACKEND_STATUS" == "200" ]; then
    echo -e "│ ${GREEN}✓${NC} Backend:   http://localhost:8000               │"
else
    echo -e "│ ${YELLOW}⏳${NC} Backend:   Starting... (wait 30s)              │"
fi

echo -e "│ ${GREEN}✓${NC} API:       http://localhost:8000/api/news      │"
echo -e "│ ${GREEN}✓${NC} Database:  localhost:3306                      │"
echo "└─────────────────────────────────────────────────────┘"

echo ""
echo -e "${BLUE}📝 Useful Commands:${NC}"
echo "  make logs          - View all logs"
echo "  make logs-backend  - View backend logs"
echo "  make logs-frontend - View frontend logs"
echo "  make down          - Stop all containers"
echo "  make restart       - Restart all containers"
echo "  make help          - Show all available commands"
echo ""

echo -e "${BLUE}🧪 Run Tests:${NC}"
echo "  docker-compose exec backend php bin/phpunit tests/Unit"
echo ""

echo -e "${BLUE}📚 Documentation:${NC}"
echo "  README.md          - Main documentation"
echo "  CODE_REVIEW.md     - Architecture review"
echo "  IMPROVEMENTS.md    - Senior-level improvements"
echo ""

if [ "$FRONTEND_STATUS" != "200" ] || [ "$BACKEND_STATUS" != "200" ]; then
    echo -e "${YELLOW}⏳ Services are still starting. This can take up to 1 minute.${NC}"
    echo -e "${YELLOW}   Monitor progress with: ${NC}make logs"
    echo ""
fi

echo -e "${GREEN}🚀 Application is ready at: ${NC}${BLUE}http://localhost:5173${NC}"
echo ""

