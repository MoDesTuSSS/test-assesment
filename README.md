# 📰 News Management System

Full-stack news management application with **Symfony 6.4** backend and **React 18** frontend.

---

## 🚀 Quick Start

### Prerequisites
- Docker Desktop 20.10+
- Docker Compose 2.0+
- Ports available: 3306, 5173, 8000

### Installation (One Command)

```bash
./setup.sh
```

Wait 3-5 minutes for initial setup. The script will:
- Create environment files
- Build Docker containers (MySQL, Symfony, React)
- Install dependencies
- Initialize database
- Start all services

### Access Application

- **Frontend:** http://localhost:5173
- **Backend API:** http://localhost:8000/api/news

---

## 📋 Features

✅ Full CRUD operations for news articles (title, body, image)  
✅ RESTful API with JSON responses  
✅ Modern React UI with TypeScript  
✅ Image upload with validation  
✅ Docker containerization  
✅ Unit tests (19 tests, 100% passing)  
✅ Clean Architecture (Service Layer + DTO + Repository)

---

## 🛠️ Technology Stack

**Backend:**
- Symfony 6.4 LTS
- PHP 8.3
- MySQL 8.0
- Doctrine ORM

**Frontend:**
- React 18
- TypeScript 5.0
- Vite 5.0
- Tailwind CSS + shadcn/ui

**DevOps:**
- Docker + Docker Compose
- Multi-stage builds

---

## 🧪 Testing

Run unit tests:

```bash
make test
```

Or directly:
```bash
docker-compose exec backend php bin/phpunit tests/Unit
```

**Result:** 19 tests, 45 assertions, 100% passing

---

## 📝 Common Commands

```bash
# Start application
./setup.sh

# Run tests
make test

# View logs
make logs

# Stop application
make down

# Restart
make restart

# Access backend shell
make shell-backend

# Access frontend shell
make shell-frontend

# Show all commands
make help
```

---

## 🔌 API Endpoints

### GET /api/news
Get all news articles

### GET /api/news/{id}
Get specific article

### POST /api/news
Create new article
```json
{
  "title": "Article Title",
  "body": "Article content...",
  "image": "/uploads/image.jpg"
}
```

### PUT /api/news/{id}
Update article

### DELETE /api/news/{id}
Delete article

### POST /api/news/upload
Upload image (multipart/form-data)

### 📚 API Documentation
- **Swagger UI:** `http://localhost:8000/swagger-ui.html` - Interactive API documentation with beautiful interface
- **OpenAPI JSON:** `http://localhost:8000/api/doc.json` - OpenAPI 3.0 specification
- **OpenAPI JSON (alt):** `http://localhost:8000/api/doc` - Same as above (for compatibility)

---

## 🏗️ Project Structure

```
news-management-system/
├── backend/              # Symfony 6.4 API
│   ├── src/
│   │   ├── Controller/   # API endpoints
│   │   ├── Service/      # Business logic
│   │   ├── DTO/          # Data transfer objects
│   │   ├── Entity/       # Doctrine entities
│   │   └── Repository/   # Data access
│   ├── tests/Unit/       # Unit tests
│   └── Dockerfile
│
├── frontend/             # React 18 UI
│   ├── src/
│   │   ├── components/   # UI components
│   │   ├── pages/        # Page components
│   │   └── services/     # API client
│   └── Dockerfile
│
├── docs/                 # Additional documentation
├── docker-compose.yml    # Container orchestration
├── setup.sh              # One-command setup
└── Makefile              # Task automation
```

---

## 🎯 Architecture Highlights

**Clean Architecture:**
```
Controller → DTO → Service → Entity → Repository → Database
```

**Design Patterns:**
- Service Layer Pattern
- Repository Pattern
- DTO Pattern
- Dependency Injection

**Key Features:**
- Type safety (PHP 8.3 + TypeScript 5.0)
- Unit tested (19 tests)
- SOLID principles
- Extensible design

---

## 🔧 Configuration

Environment variables are created automatically by `setup.sh` from templates:
- `.env` - Root environment (database credentials)
- `backend/.env` - Symfony base configuration (required for tests)
- `backend/.env.local` - Symfony local overrides
- `frontend/.env` - React/Vite configuration

**Default credentials:**
- Database: `news_db`
- User: `symfony_user`
- Password: `symfony_pass`

---

## 🐛 Troubleshooting

### Port already in use
```bash
# Check what's using the port
lsof -i :8000
lsof -i :5173
lsof -i :3306
```

### Containers won't start
```bash
docker-compose down -v
docker-compose up -d --build
```

### Database connection issues
```bash
# Check database logs
docker-compose logs db

# Restart database
docker-compose restart db

# Recreate database schema
docker-compose exec backend php bin/console doctrine:schema:update --force
```

### Table doesn't exist error
```bash
# Create database schema
docker-compose exec backend php bin/console doctrine:schema:update --force

# Or use make command
make schema-update
```

### Clean reinstall
```bash
docker-compose down -v
rm -f .env backend/.env backend/.env.local frontend/.env
./setup.sh
```

---

## 🚢 Production Deployment

For production deployment:
1. Update environment variables with secure credentials
2. Set `APP_ENV=prod` in backend
3. Build production Docker images
4. Configure SSL/HTTPS
5. Set up monitoring and backups

---

## ✨ Extensibility

The architecture makes it easy to add new entity types (Events, Projects, etc.):

1. Create Entity: `php bin/console make:entity Event`
2. Create DTO: `CreateEventDTO.php`
3. Create Service: `EventService.php`
4. Create Controller: `EventController.php`
5. Update database: `doctrine:schema:update --force`
6. Add frontend components

Same patterns, consistent structure!

---

## 📊 Project Stats

- **Backend:** 15+ core files
- **Frontend:** 30+ components
- **Tests:** 19 unit tests (100% passing)
- **Documentation:** 5900+ lines across 12 files
- **Code Quality:** PSR-12 (PHP), Airbnb (TypeScript)

---

## 📄 License

MIT License - See LICENSE file for details

---

## 🎓 Technical Details

This project demonstrates:
- Clean Architecture principles
- SOLID design patterns
- Test-Driven Development
- RESTful API design
- Modern full-stack development
- Docker containerization
- Comprehensive documentation

**Built for:** Technical assessment for senior developer position  
**Status:** Production-ready  
**Version:** 1.0.0

---

**🎉 Ready to run! Execute `./setup.sh` and open http://localhost:5173**
