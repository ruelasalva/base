# ERP Multi-tenant - FuelPHP

Sistema ERP multi-tenant basado en FuelPHP con soporte para múltiples backends y frontend público.

* Version: 1.0.0
* Base Framework: FuelPHP 1.8.2
* [Documentación de FuelPHP](https://fuelphp.com/docs)

## Descripción

Este es un sistema ERP completo multi-tenant que incluye:

### Backends

- **Admin** (`/admin`) - Panel de administración del sistema
  - Gestión de usuarios y roles
  - Configuración del sistema
  - Reportes y estadísticas

- **Provider** (`/provider`) - Portal para proveedores
  - Gestión de productos
  - Control de inventario
  - Órdenes de compra

- **Sellers** (`/sellers`) - Portal para vendedores
  - Gestión de ventas
  - CRM de clientes
  - Cotizaciones
  - Comisiones

- **Clients** (`/clients`) - Portal de autoservicio para clientes
  - Historial de pedidos
  - Perfil del cliente
  - Tickets de soporte

### Frontend

- **Store** (`/tienda`) - Tienda online
  - Catálogo de productos
  - Carrito de compras
  - Proceso de checkout
  - Búsqueda de productos

- **Landing** (`/landing`) - Página de aterrizaje
  - Página principal
  - Información de la empresa
  - Formulario de contacto
  - Páginas de contenido

## Arquitectura Multi-tenant

El sistema utiliza una arquitectura multi-tenant donde:

1. **Base de datos por tenant**: Cada tenant tiene su propia base de datos
2. **Resolución por dominio**: El tenant se determina por HTTP_HOST
3. **Módulos condicionales**: Los módulos se cargan según configuración del tenant
4. **Base actualizable**: El código base puede actualizarse sin afectar a los tenants

### Estructura de la Base de Datos Master

```sql
CREATE TABLE tenants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    domain VARCHAR(255) NOT NULL UNIQUE,
    db_name VARCHAR(255) NOT NULL,
    active_modules JSON,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME,
    updated_at DATETIME
);
```

### Módulos Disponibles

- `erp_admin` - Panel de administración
- `erp_provider` - Portal de proveedores
- `erp_sellers` - Portal de vendedores
- `erp_clients` - Portal de clientes
- `erp_store` - Tienda online
- `erp_landing` - Landing page

## Estructura del Proyecto

```
fuel/
├── app/                    # Aplicación principal
│   ├── classes/
│   │   ├── controller/     # Controladores base
│   │   └── model/          # Modelos base
│   ├── config/
│   │   ├── config.php      # Configuración general
│   │   ├── config_tenant.php  # Configuración multi-tenant
│   │   └── db.php          # Configuración de base de datos
│   └── views/              # Vistas principales
├── packages/               # Paquetes de FuelPHP
└── packages_tenant/        # Módulos del ERP
    ├── erp_admin/
    ├── erp_provider/
    ├── erp_sellers/
    ├── erp_clients/
    ├── erp_store/
    └── erp_landing/
```

## Instalación

1. Clonar el repositorio
2. Ejecutar `composer install`
3. Configurar la base de datos en `fuel/app/config/db.php`
4. Crear la base de datos master y la tabla de tenants
5. Crear los tenants necesarios

## Configuración de Tenant

Para activar módulos para un tenant, actualizar el campo `active_modules`:

```sql
UPDATE tenants 
SET active_modules = '["erp_admin", "erp_store", "erp_landing"]'
WHERE domain = 'ejemplo.com';
```

## Desarrollo

### Crear un nuevo módulo

1. Crear directorio en `fuel/packages_tenant/nombre_modulo/`
2. Crear `bootstrap.php` con la lógica de carga condicional
3. Crear controladores en `classes/controller/`
4. Crear modelos en `classes/model/`
5. Crear servicios en `classes/service/`
6. Crear vistas en `views/`

### Estructura de un módulo

```
nombre_modulo/
├── bootstrap.php
├── classes/
│   ├── controller/
│   ├── model/
│   └── service/
└── views/
    └── nombre_modulo/
```

## Equipo de Desarrollo

ERP Development Team

## Licencia

MIT License
