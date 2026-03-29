# Agil365 — Sistema de Gestión de Proyectos Tecnológicos

<p align="center">
  <img src="./public/favicon.png" alt="Agil365 Logo" width="120"/>
</p>

<p align="center">
  <strong>Plataforma integral para la gestión ágil de proyectos, equipos, reuniones y tareas.</strong><br>
  Construida con Laravel 12, Tailwind CSS v4, Alpine.js y MySQL.
</p>

---

## 📌 Descripción

**Agil365** es un sistema de gestión de proyectos tecnológicos diseñado para equipos de desarrollo e ingeniería. Centraliza la administración de proyectos, clientes, tareas, reuniones, bonos, alertas y reportes en una sola plataforma moderna y responsiva.

El sistema implementa un robusto control de roles y permisos, integración con **Google Calendar**, auditoría de actividad, y un dashboard analítico con indicadores clave de rendimiento (KPIs).

---

## ✨ Características Principales

- 📊 **Dashboard Analítico** — KPIs en tiempo real: proyectos activos, tareas pendientes, reuniones próximas, bonos y alertas
- 🗂️ **Gestión de Proyectos** — Ciclo de vida completo: creación, seguimiento, notas, estados y cierre
- ✅ **Gestión de Tareas** — Asignación por proyecto, prioridades, estados y seguimiento por ingeniero
- 📅 **Reuniones con Agenda** — Programación de reuniones, gestión de participantes, logs de cambios y sincronización con Google Calendar
- ⏳ **Ítems Pendientes** — Registro y resolución de pendientes por proyecto y responsable
- 🏆 **Gestión de Bonos** — Control de bonificaciones por empleado y período
- 🔔 **Alertas del Sistema** — Notificaciones internas configurables por tipo y prioridad
- 🏢 **Gestión de Clientes** — CRUD de empresas/clientes vinculados a proyectos
- 👥 **Usuarios y Roles** — Sistema de roles granular con permisos por módulo
- 📈 **Reportes** — Generación de reportes por proyecto, tarea, usuario y período
- ⚙️ **Configuración** — Ajustes globales del sistema (logotipo, firma, sello, datos de empresa)
- 🔗 **Integración Google Calendar** — OAuth 2.0 para sincronización automática de reuniones
- 🌙 **Modo Oscuro / Claro** — Tema persistente por preferencia del usuario

---

## 🛠️ Stack Tecnológico

| Capa | Tecnología |
|---|---|
| Backend | Laravel 12 (PHP 8.2+) |
| Frontend | Tailwind CSS v4, Alpine.js |
| Build | Vite |
| Base de Datos | MySQL |
| Autenticación | Laravel Auth nativo |
| Integración externa | Google Calendar API v3 (`google/apiclient`) |
| Testing | PestPHP |

---

## 📋 Requisitos del Sistema

- **PHP** 8.2 o superior
- **Composer** (gestor de dependencias PHP)
- **Node.js** 18+ y **npm**
- **MySQL** 5.7+ (se usa XAMPP en desarrollo local)
- Extensiones PHP requeridas: `pdo_mysql`, `gd`, `zip`, `openssl`, `mbstring`

### Verificar instalaciones

```bash
php -v
composer -V
node -v
npm -v
```

---

## 🚀 Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/Anonimo2334/Agil360.git
cd Agil360
```

### 2. Instalar dependencias PHP

```bash
composer install
```

### 3. Instalar dependencias Node.js

```bash
npm install
```

### 4. Configurar el entorno

```bash
# Linux / Mac
cp .env.example .env

# Windows
copy .env.example .env
```

### 5. Generar la clave de aplicación

```bash
php artisan key:generate
```

### 6. Configurar la base de datos

Edita el archivo `.env` con tus credenciales:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agil365
DB_USERNAME=root
DB_PASSWORD=
```

Crea la base de datos en MySQL:

```sql
CREATE DATABASE agil365;
```

### 7. Ejecutar migraciones

```bash
php artisan migrate
```

### 8. Poblar la base de datos (Seeders)

```bash
php artisan db:seed
```

### 9. Crear enlace simbólico de almacenamiento

```bash
php artisan storage:link
```

---

## 👤 Roles y Credenciales por Defecto

Al ejecutar `php artisan db:seed`, se crean los siguientes usuarios y roles:

| Rol | Descripción | Email | Contraseña |
|---|---|---|---|
| `super_admin` | Acceso total al sistema | `admin@agil365.com` | `password` |
| `ingeniero` | Desarrollador / Técnico | `ana@agil365.com` | `password` |
| `ingeniero` | Desarrollador / Técnico | `carlos@agil365.com` | `password` |
| `ingeniero` | Desarrollador / Técnico | `maria@agil365.com` | `password` |
| `ingeniero` | Desarrollador / Técnico | `luis@agil365.com` | `password` |
| `ingeniero` | Desarrollador / Técnico | `pedro@agil365.com` | `password` |
| `soporte` | Soporte técnico | `sara@agil365.com` | `password` |

**Roles disponibles en el sistema:**

- `super_admin` — Control total
- `admin` — Gestión administrativa
- `gerente` — Gerencia operativa
- `ingeniero` — Desarrollador / Técnico
- `soporte` — Soporte técnico
- `visualizador` — Solo lectura

---

## ▶️ Ejecutar la Aplicación

### Con XAMPP (recomendado en desarrollo)

1. Inicia **Apache** y **MySQL** desde el panel de XAMPP
2. Accede en el navegador: [http://localhost/Agil360/public](http://localhost/Agil360/public)
3. Compila los assets en modo desarrollo:

```bash
npm run dev
```

### Con servidor artisan

```bash
# Terminal 1 — Servidor PHP
php artisan serve

# Terminal 2 — Assets en tiempo real
npm run dev
```

O todo junto con:

```bash
composer run dev
```

**URL:** [http://localhost:8000](http://localhost:8000)

### Build para producción

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

---

## 🔗 Integración Google Calendar

Agil365 permite sincronizar reuniones con Google Calendar vía OAuth 2.0.

### Configuración en `.env`

```env
GOOGLE_CLIENT_ID=tu_client_id
GOOGLE_CLIENT_SECRET=tu_client_secret
GOOGLE_REDIRECT_URI=http://localhost/Agil360/public/google-calendar/callback
```

### Pasos:
1. Crear un proyecto en [Google Cloud Console](https://console.cloud.google.com/)
2. Habilitar la **Google Calendar API**
3. Crear credenciales OAuth 2.0 (tipo: Aplicación web)
4. Agregar la URI de redirección autorizada
5. Copiar el Client ID y Client Secret al `.env`

---

## 📁 Estructura del Proyecto

```
Agil360/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── DashboardController.php
│   │       ├── ProjectController.php
│   │       ├── TaskController.php
│   │       ├── MeetingController.php
│   │       ├── PendingItemController.php
│   │       ├── BonusController.php
│   │       ├── AlertController.php
│   │       ├── CompanyController.php
│   │       ├── UserController.php
│   │       ├── RoleController.php
│   │       ├── ReportController.php
│   │       ├── SettingController.php
│   │       ├── ProfileController.php
│   │       └── GoogleCalendarController.php
│   └── Models/
│       ├── User.php
│       ├── Project.php
│       ├── Task.php
│       ├── Meeting.php
│       ├── MeetingLog.php
│       ├── PendingItem.php
│       ├── Bonus.php
│       ├── Alert.php
│       ├── Company.php
│       ├── Role.php
│       ├── Setting.php
│       ├── ActivityLog.php
│       ├── ProjectNote.php
│       └── GoogleCalendarIntegration.php
├── database/
│   ├── migrations/          # 21 migraciones de la BD
│   └── seeders/             # Datos iniciales (roles, usuarios)
├── public/
│   ├── favicon.png          # Logo del sistema
│   └── index.php
├── resources/
│   ├── css/app.css
│   ├── js/app.js
│   └── views/
│       ├── layouts/         # app.blade.php, sidebar, header
│       ├── components/      # Componentes reutilizables
│       └── pages/agil365/
│           ├── dashboard.blade.php
│           ├── proyectos/
│           ├── tareas/
│           ├── reuniones/
│           ├── pendientes/
│           ├── bonos/
│           ├── alertas/
│           ├── clientes/
│           ├── usuarios/
│           ├── roles.blade.php
│           ├── reportes/
│           ├── profile/
│           ├── account/
│           └── configuracion.blade.php
├── routes/
│   └── web.php
├── .env.example
├── composer.json
├── package.json
└── vite.config.js
```

---

## 🧪 Testing

```bash
# Ejecutar todos los tests
php artisan test

# Con cobertura
php artisan test --coverage

# Filtrar tests específicos
php artisan test --filter=ExampleTest

# Usando Composer
composer run test
```

---

## 📜 Comandos Útiles

```bash
# Migraciones
php artisan migrate                    # Ejecutar migraciones pendientes
php artisan migrate:fresh --seed       # Resetear BD y poblar con seeders
php artisan migrate:rollback           # Revertir última migración

# Caché y optimización
php artisan optimize:clear             # Limpiar toda la caché
php artisan optimize                   # Cachear todo para producción
php artisan config:cache               # Cachear configuración
php artisan route:cache                # Cachear rutas

# Utilidades
php artisan storage:link               # Enlace simbólico de storage
php artisan route:list                 # Listar todas las rutas
php artisan queue:work                 # Iniciar worker de colas
php artisan make:controller MiController  # Crear controlador
php artisan make:model MiModelo -m    # Crear modelo con migración
```

---

## 🐛 Solución de Problemas

### Error "Class not found"
```bash
composer dump-autoload
```

### Error de permisos en `storage/` o `bootstrap/cache/`
```bash
# Linux/Mac
chmod -R 775 storage bootstrap/cache
```

### Error al compilar assets
```bash
rm -rf node_modules package-lock.json
npm install
npm run dev
```

### Limpiar toda la caché
```bash
php artisan optimize:clear
```

### Error de conexión a la base de datos
- Verificar que MySQL esté activo en XAMPP
- Revisar credenciales en `.env`
- Confirmar que la base de datos `agil365` exista

### El favicon no se actualiza en el navegador
- Hacer **Ctrl + Shift + R** (recarga forzada) en el navegador

---

## 📄 Licencia

Este proyecto es de uso interno. Todos los derechos reservados © 2025 Agil365.
