@echo off
echo ==========================================
echo    SIGE Satelite - Instalacion con Docker
echo ==========================================
echo.

REM Verificar Docker
docker --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: Docker no esta instalado
    pause
    exit /b 1
)

docker-compose --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: Docker Compose no esta instalado
    pause
    exit /b 1
)

echo ✓ Docker y Docker Compose detectados
echo.

REM 1. Copiar .env
if not exist .env (
    echo Creando archivo .env...
    copy .env.example .env
    echo ✓ Archivo .env creado
) else (
    echo ⚠ El archivo .env ya existe
)
echo.

REM 2. Construir contenedores
echo Construyendo contenedores Docker...
docker-compose build --no-cache
echo ✓ Contenedores construidos
echo.

REM 3. Levantar contenedores
echo Levantando contenedores...
docker-compose up -d
echo ✓ Contenedores levantados
echo.

REM Esperar base de datos
echo Esperando a que la base de datos este lista (30 segundos)...
timeout /t 30 /nobreak
echo.

REM 4. Instalar Composer
echo Instalando dependencias de Composer...
docker-compose exec -T app composer install --no-interaction --prefer-dist --optimize-autoloader
echo ✓ Dependencias instaladas
echo.

REM 5. Generar APP_KEY
echo Generando APP_KEY...
docker-compose exec -T app php artisan key:generate
echo ✓ APP_KEY generada
echo.

REM 6. Migraciones
echo Ejecutando migraciones...
docker-compose exec -T app php artisan migrate --force
echo ✓ Migraciones ejecutadas
echo.

REM 7. Seeders
echo Creando usuarios de prueba...
docker-compose exec -T app php artisan db:seed --class=UserSeeder --force
echo ✓ Usuarios creados
echo.

REM 8. Permisos
echo Configurando directorios...
docker-compose exec -T app mkdir -p storage/app/imports/temp
docker-compose exec -T app mkdir -p storage/app/imports/procesados
docker-compose exec -T app mkdir -p storage/app/imports/scraped
docker-compose exec -T app chmod -R 775 storage bootstrap/cache
echo ✓ Directorios configurados
echo.

REM 9. NPM
echo Compilando assets...
docker-compose exec -T app npm install
docker-compose exec -T app npm run dev
echo ✓ Assets compilados
echo.

echo.
echo ==========================================
echo   ✓ INSTALACION COMPLETADA EXITOSAMENTE
echo ==========================================
echo.
echo Informacion de Acceso:
echo.
echo   Aplicacion:     http://localhost:8000
echo   phpMyAdmin:     http://localhost:8080
echo   Usuario Admin:  admin@sige.com
echo   Contraseña:     password123
echo.
echo Comandos utiles:
echo   Ver logs:            docker-compose logs -f
echo   Detener:             docker-compose down
echo   Reiniciar:           docker-compose restart
echo.
echo ¡Listo para usar! 
echo.
pause