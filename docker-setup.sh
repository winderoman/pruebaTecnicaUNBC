#!/bin/bash

echo "=========================================="
echo "   SIGE Satélite - Instalación con Docker"
echo "==========================================="
echo ""

# Colores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Verificar que Docker esté instalado
if ! command -v docker &> /dev/null; then
    echo -e "${RED} Docker no está instalado. Por favor instala Docker primero.${NC}"
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo -e "${RED} Docker Compose no está instalado. Por favor instala Docker Compose primero.${NC}"
    exit 1
fi

echo -e "${GREEN} Docker y Docker Compose detectados${NC}"
echo ""

# 1. Copiar archivo .env
if [ ! -f .env ]; then
    echo -e "${YELLOW} Creando archivo .env desde .env.example...${NC}"
    cp .env.example .env
    echo -e "${GREEN} Archivo .env creado${NC}"
else
    echo -e "${YELLOW}  El archivo .env ya existe, no se sobrescribirá${NC}"
fi
echo ""

# 2. Construir contenedores
echo -e "${YELLOW}🏗️  Construyendo contenedores Docker...${NC}"
docker-compose build --no-cache
echo -e "${GREEN} Contenedores construidos${NC}"
echo ""

# 3. Levantar contenedores
echo -e "${YELLOW} Levantando contenedores...${NC}"
docker-compose up -d
echo -e "${GREEN} Contenedores levantados${NC}"
echo ""

# Esperar a que la base de datos esté lista
echo -e "${YELLOW} Esperando a que la base de datos esté lista (30 segundos)...${NC}"
sleep 30

# 4. Instalar dependencias de Composer
echo -e "${YELLOW} Instalando dependencias de Composer...${NC}"
docker-compose exec -T app composer install --no-interaction --prefer-dist --optimize-autoloader
echo -e "${GREEN} Dependencias instaladas${NC}"
echo ""

# 5. Generar APP_KEY
echo -e "${YELLOW}Generando APP_KEY...${NC}"
docker-compose exec -T app php artisan key:generate
echo -e "${GREEN} APP_KEY generada${NC}"
echo ""

# 6. Ejecutar migraciones
echo -e "${YELLOW}  Ejecutando migraciones de base de datos...${NC}"
docker-compose exec -T app php artisan migrate --force
echo -e "${GREEN} Migraciones ejecutadas${NC}"
echo ""

# 7. Crear usuario de prueba
echo -e "${YELLOW} Creando usuarios de prueba...${NC}"
docker-compose exec -T app php artisan db:seed --class=UserSeeder --force
echo -e "${GREEN} Usuarios creados${NC}"
echo ""

# 8. Crear directorios de storage
echo -e "${YELLOW} Configurando permisos de storage...${NC}"
docker-compose exec -T app mkdir -p storage/app/imports/temp
docker-compose exec -T app mkdir -p storage/app/imports/procesados
docker-compose exec -T app mkdir -p storage/app/imports/scraped
docker-compose exec -T app chmod -R 775 storage bootstrap/cache
docker-compose exec -T app chown -R www-data:www-data storage bootstrap/cache
echo -e "${GREEN} Permisos configurados${NC}"
echo ""

# 9. Instalar dependencias de NPM y compilar assets
echo -e "${YELLOW} Compilando assets...${NC}"
docker-compose exec -T app npm install
docker-compose exec -T app npm run dev
echo -e "${GREEN} Assets compilados${NC}"
echo ""

# Resumen final
echo ""
echo -e "${GREEN}=========================================="
echo " INSTALACIÓN COMPLETADA EXITOSAMENTE"
echo "==========================================${NC}"
echo ""
echo -e "${YELLOW}Información de Acceso:${NC}"
echo ""
echo -e "Aplicación:       ${GREEN}http://localhost:8000${NC}"
echo -e " phpMyAdmin:       ${GREEN}http://localhost:8080${NC}"
echo -e "Usuario Admin:    ${GREEN}admin@sige.com${NC}"
echo -e "Contraseña:       ${GREEN}password123${NC}"
echo ""
echo -e "${YELLOW} Comandos útiles:${NC}"
echo ""
echo -e "  Ver logs:              ${GREEN}docker-compose logs -f${NC}"
echo -e "  Detener contenedores:  ${GREEN}docker-compose down${NC}"
echo -e "  Reiniciar:             ${GREEN}docker-compose restart${NC}"
echo -e "  Ejecutar comandos:     ${GREEN}docker-compose exec app php artisan <comando>${NC}"
echo ""
echo -e "${YELLOW} Para importar datos de ejemplo:${NC}"
echo -e "  ${GREEN}docker-compose exec app php artisan sige:importar-idoneidad storage/app/imports/idoneidad.csv${NC}"
echo ""
echo -e "${GREEN}¡Listo para usar!${NC}"
echo ""