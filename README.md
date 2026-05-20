# 🚗 RentDrive — Gestor de Alquiler de Vehículos con Tracking GPS

![WordPress](https://img.shields.io/badge/WordPress-Tema%20custom-21759B?style=flat-square&logo=wordpress&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-API%20REST-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white)
![GPS](https://img.shields.io/badge/GPS-Tracking%20en%20vivo-00c8ff?style=flat-square)
![Status](https://img.shields.io/badge/Status-TFG%20Grupal-gold?style=flat-square)

> **Trabajo de Fin de Grado (grupal)** — Tema WordPress completamente artesanal con Custom Post Type propio, conectado a una API REST en Laravel, con **tracking GPS en tiempo real** de la flota de vehículos.

---

## 📋 Descripcióngeolocation

RentDrive es una solución fullstack de dos capas: **WordPress actúa como frontend** con un tema desarrollado desde cero —sin plantilla base— presentando el catálogo de vehículos y el sistema de reservas, mientras que **Laravel expone una API REST** que gestiona toda la lógica de negocio.

El elemento diferencial del proyecto es el **sistema de tracking GPS en tiempo real**: cada vehículo envía continuamente su posición (latitud, longitud e ID) en formato JSON a un endpoint de Laravel, que procesa y sirve los datos para visualizarlos sobre un mapa renderizado en JavaScript desde una vista Blade.

---

## ⚙️ Tecnologías

| Capa | Tecnologías |
|------|------------|
| Frontend | WordPress · Tema custom (sin plantilla base) · CPT · PHP · HTML5 · CSS3 · JS |
| Backend | Laravel · API REST · Eloquent ORM |
| Base de datos | MySQL |
| Tracking GPS | JSON payload · Endpoint Laravel · Mapa JS en Blade |
| Comunicación | Fetch API (WordPress → Laravel REST API) |

---

## ✨ Funcionalidades

### 👤 Usuario público
- Consulta del **catálogo de vehículos** gestionado mediante Custom Post Type
- **Reserva de vehículos** con selección de fechas y comprobación de disponibilidad en tiempo real

### 🛠️ Panel de Administración
- **Gestión de flota** — alta, edición y eliminación de vehículos
- **Panel de reservas** — listado y gestión de reservas activas
- **Gestión de clientes** — administración de usuarios registrados
- **Tracking GPS en vivo** — visualización en mapa de la posición en tiempo real de cada vehículo de la flota

---

## 📡 Tracking GPS en tiempo real

El sistema recibe la posición de cada vehículo de forma continua:

1. Una app simuladora envía un **payload JSON** con `latitud`, `longitud` e `id_vehiculo` a un endpoint de Laravel
2. Laravel procesa los datos y los almacena / sirve al frontend
3. Una vista **Blade** renderiza el mapa con **JavaScript** y pinta la posición actualizada de cada vehículo
4. El panel de administración muestra el seguimiento en vivo de toda la flota

---

## 🏗️ Arquitectura

```
┌──────────────────────────────┐     HTTP / JSON      ┌──────────────────────────┐
│         FRONTEND              │ ──────────────────▶  │        BACKEND           │
│   WordPress (Tema artesanal)  │                      │    Laravel (API REST)    │
│   Custom Post Type (CPT)      │ ◀──────────────────  │    Lógica de negocio     │
│   Reservas · Admin · Mapa GPS │    JSON Response     │    GPS · Disponibilidad  │
└──────────────────────────────┘                      └──────────────────────────┘
                                                                 ▲
                                                                 │ JSON
                                                      ┌──────────────────────┐
                                                      │   App GPS Simuladora  │
                                                      │  lat · lng · id_veh  │
                                                      └──────────────────────┘
```

---

## 🚀 Instalación

### Requisitos
- PHP 8.x · Composer · MySQL · WordPress 6.x
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
# 4. Configura la URL del backend en functions.php:
#    define('RENTDRIVE_API_URL', 'http://localhost:8000/api');
```

---

## 📁 Estructura relevante

```
rentdrive/
├── backend/                          # Laravel API
│   ├── app/Http/Controllers/Api/
│   │   ├── VehiculoController.php
│   │   ├── ReservaController.php
│   │   ├── ClienteController.php
│   │   └── GpsController.php         # Recepción y servicio de datos GPS
│   ├── database/migrations/
│   └── routes/api.php
│
└── frontend/                         # Tema WordPress artesanal
    ├── functions.php                 # Registro del CPT + hooks + config API
    ├── cpt-vehiculos.php             # Custom Post Type: Vehículos
    ├── templates/
    │   ├── page-catalogo.php
    │   ├── page-reserva.php
    │   ├── page-admin.php
    │   └── page-tracking.php         # Mapa GPS en tiempo real
    └── assets/
        ├── css/
        └── js/
            ├── api.js                # Comunicación con Laravel API
            └── tracking.js          # Renderizado del mapa y posiciones GPS
```

---

## 👥 Equipo

Proyecto desarrollado en equipo como **Trabajo de Fin de Grado** del Grado Superior de Desarrollo de Aplicaciones Web (DAW). El desarrollo fue compartido entre los miembros del grupo, cubriendo tanto el frontend WordPress como el backend Laravel.

---

## 📄 Licencia

Proyecto académico compartido con fines de portfolio. No está permitida su distribución o uso comercial sin autorización expresa del autor.
