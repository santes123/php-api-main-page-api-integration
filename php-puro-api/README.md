# API REST en PHP + Main Page + API Integration

Proyecto modular en PHP nativo que implementa una API REST con autenticación JWT, sistema de migraciones SQL, CRUD de tareas y una interfaz web sencilla para pruebas visuales (login, tareas y usuarios).

---

## ⚙️ Requisitos

- PHP 8.1+ con PDO/pdomysql
- Composer 2+
- MySQL / MariaDB
- Navegador web (para main page / integración)
- Opcional: XAMPP/WAMP, phpMyAdmin

---

## 🚀 Instalación rápida

### 1️⃣ Clonar el proyecto y configurar `.env`

```bash
# En php-puro-api/.env
APP_ENV=local
DB_HOST=localhost
DB_NAME=gestor_tareas
DB_USER=root
DB_PASSWORD=
JWT_SECRET=pon_un_secreto_seguro
2️⃣ Crear base de datos
sql

CREATE DATABASE gestor_tareas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
3️⃣ Instalar dependencias y migraciones
bash

cd php-puro-api
composer install
php scripts/migrate.php      # Crea tablas y usuario admin demo
▶️ Cómo ejecutar (dos métodos)
✅ Opción A – XAMPP / Apache
Copia php-puro-api, main-page e integracion-api en htdocs/

Inicia Apache + MySQL

Levantar API en consola:

bash

php -S localhost:8001 -t public
Abrir:

http://localhost/main-page → login (demo@demo.com / demo123)

http://localhost/integracion-api → versión API+frontend básica

✅ Opción B – Servidor embebido de PHP

#usar script "start-dev.bat", configurando las variables "MYSQL_DIR", "FRONTEND_DIR", "BACKEND_DIR", "API_DIR" y "PHPMYADMIN_DIR" (opcional esta ultima) dentro 
# o usar los pasos siguientes:
bash

# API
cd php-puro-api
php -S localhost:8001 -t public

# Main page
cd main-page
php -S localhost:8000 -t public

# API Integration
cd integracion-api
php -S localhost:5500
✅ Credenciales por defecto
Email	Password
demo@demo.com	demo123

📌 Endpoints principales
POST /api/v1/auth/login

GET /api/v1/tasks (token requerido)

GET /api/v1/users (solo admin)
