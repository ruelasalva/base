# DIAGNÓSTICO Y SOLUCIÓN - ERP Multi-Tenant

## 🔴 PROBLEMAS IDENTIFICADOS

### 1. **Rutas de Módulos Tenant Mal Configuradas**
**Problema**: Las rutas en `bootstrap.php` usaban `admin/dashboard/index` en lugar de `admin/controller_dashboard/index`

**Solución**: ✅ Corregido en `fuel/packages_tenant/admin/bootstrap.php`
- Cambié las rutas para usar el nombre completo del controlador
- Agregué prioridad alta con `prepend = true`

### 2. **Package Paths No Registrados**
**Problema**: `TENANT_PKGPATH` no se agregaba a `Config::package_paths`

**Solución**: ✅ Corregido en `fuel/app/bootstrap.php`
- Ahora se agrega `TENANT_PKGPATH` a la configuración antes de cargar bootstraps
- Se registra con `\Package::load()` para cada módulo

### 3. **Namespace y Autoloader Incompleto**
**Problema**: El namespace no se registraba correctamente con PSR-4

**Solución**: ✅ Corregido
- Agregué `\Autoloader::add_namespace('Admin', __DIR__.'/classes/', true)`
- El tercer parámetro `true` activa PSR-4

### 4. **Vistas No Encontradas**
**Problema**: Las vistas no se buscaban en el directorio del módulo

**Solución**: ✅ Corregido
- Agregué `\Finder::instance()->add_path(__DIR__.'/views/', -1)`
- Las vistas ahora se pueden llamar con `admin::dashboard/index`

### 5. **Módulos Sin Activación en Base de Datos**
**Problema**: No hay tabla `tenants` ni datos para activar módulos

**Solución**: ⚠️ **PENDIENTE** - Ver sección "Configuración de Base de Datos"

## ✅ CAMBIOS REALIZADOS

### 1. `fuel/app/bootstrap.php`
```php
// Agregado package paths ANTES de cargar bootstraps
$package_paths = \Config::get('package_paths', array());
if ( ! in_array(TENANT_PKGPATH, $package_paths)) {
    $package_paths[] = TENANT_PKGPATH;
    \Config::set('package_paths', $package_paths);
}

// Log de carga de packages
\Log::info('Tenant Package Loaded: ' . basename($package_path));
```

### 2. `fuel/packages_tenant/admin/bootstrap.php`
```php
// Carga del package
\Package::load('admin', TENANT_PKGPATH.'admin'.DIRECTORY_SEPARATOR);

// Namespace PSR-4
\Autoloader::add_namespace('Admin', __DIR__.'/classes/', true);

// Agregar path de vistas
\Finder::instance()->add_path(__DIR__.'/views/', -1);

// Rutas con prioridad
\Router::add(array(...), null, true); // true = prepend

// Modo DEVELOPMENT: Carga sin verificar tenant
if (\Fuel::$env === \Fuel::DEVELOPMENT) {
    // Carga módulo automáticamente para testing
}
```

### 3. `fuel/packages_tenant/admin/classes/controller/dashboard.php`
```php
// Cambió de Controller a Controller_Template
class Controller_Dashboard extends \Controller_Template

// Template definido
public $template = 'admin/template';

// Vista con namespace
$this->template->content = \View::forge('admin::dashboard/index', $data, false);
```

### 4. Creados:
- ✅ `fuel/app/classes/controller/diagnostico.php` - Para diagnosticar problemas
- ✅ `fuel/packages_tenant/admin/views/dashboard/index.php` - Vista del dashboard

## 🧪 PRUEBAS A REALIZAR

### 1. **Acceder al Diagnóstico**
```
http://localhost/base/diagnostico
```
Verás:
- Constantes definidas
- Módulos activos
- Packages tenant disponibles
- Routes registradas
- Tenant actual
- Base de datos
- Packages cargados

### 2. **Acceder al Módulo Admin (Modo Development)**
```
http://localhost/base/admin
http://localhost/base/admin/dashboard
```

En modo DEVELOPMENT, el módulo se carga automáticamente sin verificar la tabla tenants.

## 📋 CONFIGURACIÓN DE BASE DE DATOS (PENDIENTE)

### Paso 1: Crear Tabla Tenants

```sql
CREATE DATABASE IF NOT EXISTS erp_master;
USE erp_master;

CREATE TABLE tenants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    domain VARCHAR(255) NOT NULL UNIQUE,
    db_name VARCHAR(255) NOT NULL,
    active_modules JSON,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_domain (domain),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Paso 2: Insertar Tenant de Prueba (Localhost)

```sql
INSERT INTO tenants (domain, db_name, active_modules, is_active) VALUES
('localhost', 'erp_tenant_local', '["admin","partners","sellers","store"]', 1);
```

### Paso 3: Crear Base de Datos del Tenant

```sql
CREATE DATABASE IF NOT EXISTS erp_tenant_local;
USE erp_tenant_local;

-- Aquí van las tablas de tu aplicación
-- Por ejemplo:
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'user',
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Paso 4: Configurar Subdominios (Opcional)

Si quieres usar subdominios como `admin.miempresa.local` o `papeleria.miempresa.local`:

**Windows hosts** (`C:\Windows\System32\drivers\etc\hosts`):
```
127.0.0.1 admin.local
127.0.0.1 papeleria.local
127.0.0.1 ferreteria.local
```

**Apache Virtual Hosts** (`C:\xampp\apache\conf\extra\httpd-vhosts.conf`):
```apache
<VirtualHost *:80>
    ServerName admin.local
    DocumentRoot "C:/xampp/htdocs/base/public"
    <Directory "C:/xampp/htdocs/base/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Luego insertar en tenants:
```sql
INSERT INTO tenants (domain, db_name, active_modules, is_active) VALUES
('admin.local', 'erp_admin_db', '["admin"]', 1),
('papeleria.local', 'erp_papeleria_db', '["admin","store","sellers"]', 1),
('ferreteria.local', 'erp_ferreteria_db', '["admin","store","providers"]', 1);
```

## 🏗️ ARQUITECTURA DE ACTUALIZACIÓN CENTRALIZADA

### Estructura de Archivos

```
base/                          ← Core compartido (actualizable)
├── fuel/
│   ├── core/                  ← FuelPHP core
│   ├── packages/              ← Packages base
│   │   ├── orm/
│   │   ├── auth/
│   │   └── email/
│   ├── packages_tenant/       ← Módulos tenant (actualizable)
│   │   ├── admin/             ← Módulo admin
│   │   ├── partners/          ← Módulo partners
│   │   ├── sellers/           ← Módulo sellers
│   │   └── store/             ← Módulo store
│   └── app/                   ← Configuración base
│       ├── config/
│       │   ├── config.php
│       │   ├── db.php
│       │   └── config_tenant.php
│       └── bootstrap.php
└── public/
    └── index.php
```

### Flujo de Actualización

1. **Desarrollo**: Trabajas en `base/` (repositorio Git)
2. **Commit y Push**: Subes cambios a repositorio remoto
3. **Despliegue**: Cada instalación (papelería, ferretería) hace `git pull`
4. **Migración**: Se ejecutan migraciones automáticamente

### Script de Actualización

Crear `fuel/app/tasks/update.php`:

```php
<?php

namespace Fuel\Tasks;

class Update
{
    public static function run()
    {
        \Cli::write('Actualizando sistema...', 'blue');
        
        // 1. Git pull
        exec('git pull origin main 2>&1', $output, $return);
        \Cli::write(implode("\n", $output));
        
        if ($return !== 0) {
            \Cli::error('Error al actualizar código');
            return;
        }
        
        // 2. Ejecutar migraciones
        \Cli::write('Ejecutando migraciones...', 'blue');
        exec('php oil refine migrate 2>&1', $output);
        \Cli::write(implode("\n", $output));
        
        // 3. Limpiar cache
        \Cli::write('Limpiando cache...', 'blue');
        \Cache::delete_all();
        
        \Cli::write('✓ Actualización completada', 'green');
    }
}
```

Ejecutar: `php oil refine update`

## 📝 CHECKLIST DE CONFIGURACIÓN

### En Desarrollo (localhost)
- ✅ Archivos bootstrap corregidos
- ✅ Vista de dashboard creada
- ✅ Controller de diagnóstico creado
- ⚠️ Crear tabla `tenants` en BD
- ⚠️ Insertar tenant para localhost
- ⚠️ Crear BD del tenant

### Para Cada Instalación (Papelería, Ferretería, etc.)
- [ ] Clonar repositorio base
- [ ] Configurar `fuel/app/config/db.php` con credenciales locales
- [ ] Crear entrada en tabla `tenants` con dominio específico
- [ ] Crear base de datos del tenant
- [ ] Ejecutar migraciones: `php oil refine migrate`
- [ ] Configurar `.htaccess` para producción
- [ ] Configurar FUEL_ENV a `production`

### Para Actualizaciones
- [ ] `git pull origin main` en cada instalación
- [ ] `php oil refine migrate` para actualizar BD
- [ ] Limpiar cache si es necesario

## 🚀 PRÓXIMOS PASOS

1. **Crear tabla tenants y datos de prueba**
   ```bash
   # Ejecutar SQL en phpMyAdmin o consola MySQL
   ```

2. **Probar acceso al diagnóstico**
   ```
   http://localhost/base/diagnostico
   ```

3. **Probar acceso al módulo admin**
   ```
   http://localhost/base/admin
   ```

4. **Crear otros módulos tenant** (partners, sellers, etc.)
   - Copiar estructura de `admin/`
   - Modificar namespace y rutas
   - Agregar bootstrap.php

5. **Implementar sistema de migraciones**
   - Crear migraciones para cada módulo
   - Automatizar ejecución post-update

6. **Configurar autenticación**
   - Implementar Auth en cada módulo
   - Roles y permisos por tenant

¿Quieres que te ayude con alguno de estos pasos específicamente?
