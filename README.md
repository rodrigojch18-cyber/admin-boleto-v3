# Boleto Admin Pro v3.0

> Dashboard de auditoría de boletos con PHP 8+, Tailwind CSS y Supabase JS Client.  
> Diseño **Fintech Dark** (minimalista estilo Apple).

![PHP](https://img.shields.io/badge/PHP-8+-blue?logo=php)
![Tailwind](https://img.shields.io/badge/Tailwind-CSS-06B6D4?logo=tailwindcss)
![Supabase](https://img.shields.io/badge/Supabase-Client-3ECF8E?logo=supabase)

## ✨ Funcionalidades

| Feature | Descripción |
|---------|------------|
| 📊 **Dashboard** | Tabla de participantes con métricas de ingresos en tiempo real |
| 👁️ **Ocultar sin DNI** | Botón toggle que oculta/muestra participantes sin DNI |
| 🔍 **Escanear Duplicados** | Detecta tickets duplicados por `numero_operacion` y los resalta en rojo |
| 📥 **Exportar CSV** | Descarga todos los datos en formato CSV compatible con Excel |
| 🔎 **Búsqueda** | Filtro en tiempo real por nombre, DNI, operación o monto |

## 🛠️ Stack Tecnológico

- **Backend**: PHP 8+
- **Frontend**: Tailwind CSS (CDN) + Google Fonts Inter
- **Database**: Supabase (tabla `compras`)
- **Design**: Fintech Dark Theme

## 📁 Estructura del Proyecto

```
admin-boleto-v3/
├── index.php              # Dashboard principal
├── config.php             # Configuración Supabase
├── .htaccess              # Seguridad y rewrite rules
├── deploy.sh              # Script de despliegue DigitalOcean
├── includes/
│   ├── header.php         # Head + navegación
│   └── footer.php         # Footer + scripts
├── api/
│   ├── export.php         # Endpoint exportar CSV
│   └── scan_duplicates.php # Endpoint escaneo duplicados
└── assets/
    ├── css/
    │   └── custom.css     # Estilos Fintech Dark
    ├── js/
    │   └── app.js         # Lógica frontend + Supabase
    └── icons/
        ├── scan.svg       # Ícono escaneo
        ├── export.svg     # Ícono exportación
        └── filter.svg     # Ícono filtro
```

## 🚀 Instalación

### 1. Clonar el repositorio
```bash
git clone https://github.com/rodrigojch18-cyber/admin-boleto-v3.git
cd admin-boleto-v3
```

### 2. Configurar Supabase
Editar `config.php` con tus credenciales:
```php
define('SUPABASE_URL',      'https://tu-proyecto.supabase.co');
define('SUPABASE_ANON_KEY', 'tu-anon-key-aqui');
```

### 3. Ejecutar localmente
```bash
php -S localhost:8000
```
Abrir `http://localhost:8000` en el navegador.

## 🌊 Deploy en DigitalOcean

```bash
# En tu Droplet Ubuntu 22.04+
wget https://raw.githubusercontent.com/rodrigojch18-cyber/admin-boleto-v3/main/deploy.sh
chmod +x deploy.sh
sudo ./deploy.sh
```

## 📋 Tabla Supabase Requerida

La tabla `compras` debe tener la siguiente estructura:

| Columna | Tipo | Descripción |
|---------|------|-------------|
| `id` | int8 (PK) | ID autoincremental |
| `nombre_completo` | text | Nombre del comprador |
| `dni` | text | Documento de identidad |
| `monto_pagado` | numeric | Monto de la compra |
| `numero_operacion` | text | Número de operación/ticket |
| `created_at` | timestamptz | Fecha de creación |

---

**Desarrollado por** [@rodrigojch18-cyber](https://github.com/rodrigojch18-cyber)
