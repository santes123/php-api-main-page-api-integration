# Mini App PHP – Login + CRUD

Pequeña aplicación en PHP puro que incluye autenticación de usuarios y CRUD básico (crear, ver, editar y eliminar registros).

---

## ⚙️ Requisitos

- PHP 8+
- MySQL
- Extensión PDO habilitada
- Composer (opcional)

---

## ⚙️ Instalación

### 1️⃣ Clona el repositorio y copia `.env.example` → `.env`

DB_HOST=localhost
DB_NAME=gestor_tareas
DB_USER=root
DB_PASSWORD=

pgsql

### 2️⃣ Crea la base de datos

```sql
CREATE DATABASE gestor_tareas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
3️⃣ Ejecuta el script SQL incluido
(seeds, tabla users y datos de prueba)

4️⃣ Levantar el proyecto (sin Apache)
bash

php -S localhost:8000 -t public
Abrir: http://localhost:8000

🔑 Login por defecto
Usuario	Password
admin@demo.com	demo123
```
