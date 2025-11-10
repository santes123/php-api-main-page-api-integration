@echo off
setlocal
echo =============================
echo Lanzando Frontend API-integration(PHP), Backend main-page (PHP), MYSQL y API(PHP)
echo =============================

rem ---------- RUTAS (ajusta si son diferentes en tu PC) ----------
rem true = una ventana con pestañas (Windows Terminal), false = CMDs separados
set "USE_WT=true"

:: MYSQL
set "MYSQL_DIR=C:\xampp\mysql\bin"
set "MYSQL_EXE=%MYSQL_DIR%\mysqld.exe"
set "MYSQL_SERVICE=mysql"

set "FRONTEND_DIR=C:\xampp\htdocs\0PLANTILLAS\prueba-tecnica-final\integracion-api"
set "BACKEND_DIR=C:\xampp\htdocs\0PLANTILLAS\prueba-tecnica-final\main-page"
set "API_DIR=C:\xampp\htdocs\0PLANTILLAS\prueba-tecnica-final\php-puro-api"

rem --- phpMyAdmin ---
rem Opción 1 (recomendada si no usas Apache): Servir phpMyAdmin con el servidor embebido de PHP
set "PHPMYADMIN_DIR=C:\xampp\phpMyAdmin"
set "PHPMYADMIN_PORT=8081"
set "PHPMYADMIN_URL=http://127.0.0.1:%PHPMYADMIN_PORT%"

rem -------------------------------------------------------------
rem  Opcion A: Windows Terminal con pestañas (si wt.exe existe)
rem -------------------------------------------------------------

if /I "%USE_WT%"=="true" (

	where wt >nul 2>nul
	if %errorlevel%==0 (
		echo Detectado Windows Terminal. Abriendo pestañas...
		wt -w 0 ^
			new-tab --title "MySQL (consola)" -d "%MYSQL_DIR%" cmd /k "\"%MYSQL_EXE%\" --console" ^
			; new-tab -d ^"%BACKEND_DIR%^" --title "Backend" --tabColor "#2563EB" cmd /k ^"php -S localhost:8000 -t public^" ^
			; new-tab -d ^"%FRONTEND_DIR%^" --title "API-integration" --tabColor "#2563EB" cmd /k ^"php -S localhost:5500 ^" ^
			; new-tab -d ^"%API_DIR%^" --title "API" --tabColor "#2563EB" cmd /k ^"php -S localhost:8001 -t public^" ^
			; new-tab --title "phpMyAdmin" -d "%PHPMYADMIN_DIR%" cmd /k "php -S 127.0.0.1:%PHPMYADMIN_PORT%"
		goto :eof
	)
)
rem -------------------------------------------------------------
rem  Opcion B: Ventanas CMD normales (fallback)
rem -------------------------------------------------------------

echo =============================
echo Iniciando MySQL...
echo =============================

rem ---- Opción 1: Iniciar MySQL como servicio de Windows ----
::start "MySQL (servicio)" cmd /k "net start \"%MYSQL_SERVICE%\" ^&^& echo MySQL servicio iniciado. ^&^& echo. ^&^& pause"

rem ---- Opción 2: Iniciar MySQL como proceso en consola ----
start "MySQL (consola)" /D "%MYSQL_DIR%" cmd /k ""%MYSQL_EXE%" --console"

:: --- Frontend (API-integration) ---
start "API-integration" /D "%FRONTEND_DIR%" cmd /k "php -S localhost:5500"

:: --- Backend (PHP) ---
start "BACKEND" /D "%BACKEND_DIR%" cmd /k "php -S localhost:8000 -t public"

:: --- Backend API (PHP) ---
start "BACKEND API" /D "%API_DIR%" cmd /k "php -S localhost:8001 -t public"


rem ---------- INICIAR phpMyAdmin ----------
echo =============================
echo Iniciando phpMyAdmin...
echo =============================

start "phpMyAdmin" /D "%PHPMYADMIN_DIR%" cmd /k "php -S 127.0.0.1:%PHPMYADMIN_PORT%"

exit /b
