# 🚗 RentDrive — Gestor de Alquiler de Vehículos

![WordPress](https://img.shields.io/badge/WordPress-Theme-21759B?style=flat-square&logo=wordpress&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-API-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white)
![Status](https://img.shields.io/badge/Status-TFG-gold?style=flat-square)

> Proyecto de Fin de Grado — Tema WordPress personalizado con **Custom Post Type** propio para la gestión de flotas de vehículos, conectado a una **API REST en Laravel** como backend.

---

## 📋 Descripción

RentDrive es una solución fullstack de dos capas: **WordPress actúa como frontend** presentando el catálogo de vehículos y el sistema de reservas al usuario final, mientras que **Laravel expone una API REST** que gestiona toda la lógica de negocio — disponibilidad, reservas y administración de flota.

La arquitectura desacopla completamente la capa de presentación de la lógica de negocio, permitiendo que WordPress consuma los datos del backend mediante peticiones asíncronas.

---

## ⚙️ Tecnologías

| Capa | Tecnologías |
|------|------------|
| Frontend | WordPress · Tema custom · Custom Post Type (CPT) · PHP · HTML5 · CSS3 · JS |
| Backend | Laravel · API REST · Eloquent ORM |
| Base de datos | MySQL |
| Comunicación | Fetch API (WordPress → Laravel REST API) |

---

## ✨ Funcionalidades

### 👤 Usuario público
- Consulta del **catálogo de vehículos** (gestionado mediante CPT)
- **Reserva de vehículos** con selección de fechas y comprobación de disponibilidad en tiempo real

### 🛠️ Panel de Administración
- **Gestión de flota** — alta, edición y eliminación de vehículos desde el panel WordPress
- **Panel de reservas** — listado y gestión de todas las reservas activas
- **Gestión de clientes** — administración de usuarios registrados

---

## 🏗️ Arquitectura

```
┌─────────────────────────────┐       HTTP / REST       ┌──────────────────────────┐
│        FRONTEND              │ ─────────────────────▶  │        BACKEND           │
│   WordPress (Tema custom)    │                         │    Laravel (API REST)    │
│   Custom Post Type (CPT)     │ ◀─────────────────────  │    Lógica de negocio     │
│   Vistas · Reservas · Admin  │       JSON Response     │    Disponibilidad        │
└─────────────────────────────┘                         └──────────────────────────┘
```

---

## 🚀 Instalación

### Requisitos
- PHP 8.x · Composer · MySQL
- WordPress 6.x
- Servidor local (Laragon, XAMPP...)

### Backend — Laravel

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate

# Configura la BD en .env
php artisan migrate --seed
php artisan serve
# API disponible en http://localhost:8000/api
```

### Frontend — WordPress

```bash
# 1. Instala WordPress en tu servidor local
# 2. Copia la carpeta del tema a /wp-content/themes/rentdrive
# 3. Activa el tema desde Panel → Apariencia → Temas
# 4. Configura la URL del backend en el archivo de configuración del tema
#    define('RENTDRIVE_API_URL', 'http://localhost:8000/api');
```

---

## 📁 Estructura relevante

```
rentdrive/
├── backend/                        # Laravel API
│   ├── app/Http/Controllers/Api/
│   │   ├── VehiculoController.php
│   │   ├── ReservaController.php
│   │   └── ClienteController.php
│   ├── database/migrations/
│   └── routes/api.php
│
└── frontend/                       # Tema WordPress
    ├── functions.php               # Registro del CPT + hooks
    ├── cpt-vehiculos.php           # Custom Post Type: Vehículos
    ├── templates/
    │   ├── page-catalogo.php
    │   ├── page-reserva.php
    │   └── page-admin.php
    └── assets/
        ├── css/
        └── js/
            └── api.js              # Comunicación con Laravel API
```

---

## 👨‍💻 Contexto

**Trabajo de Fin de Grado** del Grado Superior de Desarrollo de Aplicaciones Web (DAW). El proyecto demuestra el dominio de arquitecturas desacopladas, desarrollo de temas WordPress desde cero, implementación de Custom Post Types y diseño de APIs REST con Laravel.

---

## 📄 Licencia

Proyecto académico compartido con fines de portfolio. No está permitida su distribución o uso comercial sin autorización expresa del autor.
