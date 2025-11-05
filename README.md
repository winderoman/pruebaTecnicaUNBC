# Sistema Satélite SIGE

## Descripción
Sistema Satélite SIGE es una aplicación desarrollada con **Laravel 9**
1 módulo completo: Idoneidad Docente
Base de datos: Diseño e implementación de tablas necesarias
Importación: Carga de datos con validación anti-duplicados
Autenticación básica: Laravel Auth (sin roles ni permisos)
Vista de consulta: Listado de datos con filtros básicos

---

## Requisitos previos

Antes de ejecutar el proyecto, asegúrate de tener instalado lo siguiente:

| Herramienta | Versión mínima recomendada |
|--------------|-----------------------------|
| PHP | 8.0.+ |
| Composer | 2.x |
| Node.js | 18.x o superior |
| MySQL | 5.7+ |

---

## Instalación y configuración

1. **Clonar el repositorio**

   ```bash
   git clone https://github.com/winderoman/pruebaTecnicaUNBC.git
   cd sige-satelite
   ```

2. **Instalar dependencias de PHP**

   ```bash
   composer install
   ```

3. **Instalar dependencias de Node (Vite, Tailwind, etc.)**

   ```bash
   npm install
   ```

4. **Configurar el entorno**

   - Copiar el archivo de entorno de ejemplo:

     ```bash
     cp .env.example .env
     ```

   - Generar la clave de aplicación:

     ```bash
     php artisan key:generate
     ```

   - Configurar las credenciales de base de datos en `.env`:

     ```env
     DB_DATABASE=sige_satelite
     DB_USERNAME=root
     DB_PASSWORD=
     ```

5. **Ejecutar migraciones (y seeders si aplica)**

   ```bash
   php artisan migrate --seed
   ```

---

## Compilación de assets (Vite)

Para compilar los estilos y scripts con **Vite**, ejecuta:

- En modo desarrollo (actualiza automáticamente al guardar):

  ```bash
  npm run dev
  ```

- En modo producción (archivos optimizados):

  ```bash
  npm run build
  ```

Asegúrate de tener el proceso de `npm run dev` corriendo mientras usas `php artisan serve`,  
ya que Vite genera el archivo *manifest.json* necesario para los estilos y scripts.

---

## Ejecutar el servidor local

Inicia el servidor de desarrollo de Laravel:

```bash
php artisan serve
```

Esto ejecutará la aplicación en:

http://127.0.0.1:8000

---

## Autenticación

email: admin@gmail.com
passw: password

---

Desarrollado por ** Winder Román **  
Laravel 9 + Breeze + Vite
