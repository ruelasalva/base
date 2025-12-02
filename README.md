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

- **Providers** (`/providers`) - Portal para proveedores
  - Gestión de productos
  - Control de inventario
  - Órdenes de compra

- **Partners** (`/partners`) - Portal para socios comerciales
  - Gestión de alianzas
  - Contratos
  - Comisiones de partner

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

- `admin` - Panel de administración
- `providers` - Portal de proveedores
- `partners` - Portal de socios comerciales
- `sellers` - Portal de vendedores
- `clients` - Portal de clientes
- `store` - Tienda online
- `landing` - Landing page

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
    ├── admin/
    ├── providers/
    ├── partners/
    ├── sellers/
    ├── clients/
    ├── store/
    └── landing/
```

## Instalación

### Opción 1: Usando el Instalador Web (Recomendado)

1. Clonar el repositorio
2. Ejecutar `composer install`
3. Crear una base de datos vacía en MySQL
4. Acceder a `/install` en el navegador
5. Seguir el asistente de instalación:
   - Configurar conexión a base de datos
   - Ejecutar migraciones
   - Crear usuario administrador

### Opción 2: Instalación Manual

1. Clonar el repositorio
2. Ejecutar `composer install`
3. Configurar la base de datos en `fuel/app/config/db.php`
4. Ejecutar las migraciones SQL en `fuel/app/migrations/`
5. Crear el usuario administrador manualmente

## Instalador de Base de Datos

El sistema incluye un instalador web accesible en `/install` que permite:

- **Configurar la conexión** a la base de datos MySQL
- **Ejecutar migraciones** para crear/actualizar tablas
- **Crear el usuario administrador** inicial
- **Verificar el estado** del sistema

### Añadir Nuevas Migraciones

Para extender el proyecto con nuevas tablas:

1. Crear un archivo SQL en `fuel/app/migrations/` con el formato:
   ```
   NNN_nombre_descriptivo.sql
   ```
   Ejemplo: `002_productos.sql`, `003_categorias.sql`

2. Acceder a `/install` y ejecutar las migraciones pendientes

Ver `fuel/app/migrations/README.md` para más detalles.

## Configuración de Tenant

Para activar módulos para un tenant, actualizar el campo `active_modules`:

```sql
UPDATE tenants 
SET active_modules = '["admin", "providers", "partners", "sellers", "clients", "store", "landing"]'
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
