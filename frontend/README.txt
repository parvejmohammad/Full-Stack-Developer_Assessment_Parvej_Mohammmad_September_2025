# GreenCart Logistics — Simulation & Management System ---Parvej Mohammad

## 1. Project Overview & Purpose
GreenCart Logistics is a **full-stack Laravel-based system** that allows logistics managers to:
- Manage **drivers, routes, and orders**.  
- Run **delivery simulations** to calculate KPIs such as total profit, efficiency, late deliveries, and fuel costs.  
- View **interactive dashboards** (charts & KPIs).  
- Securely access APIs using **JWT/Sanctum authentication**.  

The goal is to simulate logistics operations, measure performance, and optimize efficiency in real-world scenarios.

---

## 2. Setup Steps

1. Install dependencies:
   ```bash
   composer install
   npm install && npm run build
   ```
2. Copy environment file:
   ```bash
   cp .env.example .env
   ```
3. Generate application key:
   ```bash
   php artisan key:generate
   ```
4. Run migrations & seeders:
   ```bash
   php artisan migrate --seed
   ```
   (This creates tables and seeds a default manager account: `parvej@example.com / root123`).
5. Start the server:
   ```bash
   php artisan serve
   ```
6. Access the app in browser:  
   👉 `http://127.0.0.1:8000`

---

## 3. Tech Stack Used
- **Backend**: Laravel 12 (PHP 8.2+), Sanctum for API authentication.  
- **Frontend**: Blade (server-rendered) with Axios + Chart.js for interactivity.  
- **Database**: MySQL (or MariaDB).  
- **Testing**: PHPUnit (Feature & Unit tests).  
- **API Docs**: Postman Collection / Swagger (optional).

---

## 4. Setup Instructions (Frontend & Backend)

### Backend
- Located in `app/`, `routes/`, `database/`.  
- Exposes RESTful API under `/api/*`.  
- Run migrations & seeders to prepare DB.  
- Auth is handled via **Laravel Sanctum** tokens.

### Frontend
- Blade templates in `resources/views/`:  
  - `dashboard.blade.php` — charts & KPIs.  
  - `simulation.blade.php` — run simulations.  
  - `management.blade.php` — CRUD for drivers, routes, orders.  
- Axios automatically attaches `Authorization: Bearer {{token}}` if token exists in `localStorage`.  
- Login/logout is handled in the header navigation.  

---

## 5. Environment Variables
List of required `.env` variables (without actual values):

```env
APP_NAME=
APP_ENV=
APP_KEY=
APP_DEBUG=
APP_URL=

DB_CONNECTION=
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

SANCTUM_STATEFUL_DOMAINS=
SESSION_DOMAIN=
```

---

## 6. Deployment Instructions
1. Prepare a production server (Ubuntu/Debian, PHP 8.2+, MySQL, Nginx/Apache).  
2. Clone repo and install dependencies (`composer install --optimize-autoloader --no-dev`).  
3. Build frontend assets (`npm install && npm run build`).  
4. Configure `.env` for production (DB, app URL, etc).  
5. Run migrations & seeders:  
   ```bash
   php artisan migrate --force --seed
   ```
6. Setup queue & scheduler if required.  
7. Configure web server root to `public/`.  
8. Use Laravel’s caching for optimization:  
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

---

## 7. API Documentation

### Postman Collection
- Import the included collection:  
  📂 `docs/GreenCart_API.postman_collection.json`  
- Environment file:  
  📂 `docs/GreenCart_API_Env.postman_environment.json`  

This provides all endpoints with authentication, CRUD, and simulation requests.

### Example Requests & Responses

#### Register
```http
POST /api/register
Content-Type: application/json

{
  "name": "Manager",
  "email": "parvej@example.com",
  "password": "root123",
  "password_confirmation": "root123"
}
```

**Response**
```json
{
  "user": {
    "id": 1,
    "name": "Manager",
    "email": "manager@example.com"
  },
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJI..."
}
```

#### Run Simulation
```http
POST /api/simulate
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "available_drivers": 5,
  "route_start_time": "09:00",
  "max_hours_per_driver": 8
}
```

**Response**
```json
{
  "results": {
    "total_orders": 15,
    "on_time_deliveries": 12,
    "late_deliveries": 3,
    "efficiency_score": 80,
    "total_profit": 1520.50,
    "total_fuel_cost": 300.00,
    "assignments": [
      {
        "order_code": "O1",
        "driver": "Driver A",
        "eta": "09:45",
        "is_late": false,
        "order_profit": 120.50,
        "fuel_cost": 20.00
      }
    ]
  }
}
```

---
