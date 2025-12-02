# Configuración del Sistema Multi-Tenant ERP

## ✅ Configuraciones Activadas en `config.php`

### 1. **Configuraciones Básicas**
- ✅ `base_url` → Auto-detectado
- ✅ `url_suffix` → Sin sufijo
- ✅ `index_file` → false (URL rewriting activo)
- ✅ `profiling` → false (activado en development/config.php)

### 2. **Cache y Performance**
- ✅ `cache_dir` → `fuel/app/cache/`
- ✅ `caching` → false (dev), true (producción)
- ✅ `cache_lifetime` → 3600 segundos (1 hora)

### 3. **Manejo de Errores**
- ✅ `errors.continue_on` → array() (no continuar en errores)
- ✅ `errors.throttle` → 10 (límite de errores mostrados)
- ✅ `errors.notices` → true (mostrar notices)

### 4. **Localización (IMPORTANTE)**
- ✅ `language` → 'es' (español por defecto)
- ✅ `language_fallback` → 'en' (inglés como respaldo)
- ✅ `locale` → 'es_MX.UTF-8'
- ✅ `encoding` → 'UTF-8'
- ✅ `default_timezone` → 'America/Mexico_City'

### 5. **Logging (COMPLETO)**
- ✅ `log_threshold` → `Fuel::L_ALL` (todos los logs en dev)
- ✅ `log_path` → `fuel/app/logs/`
- ✅ `log_date_format` → 'Y-m-d H:i:s'
- ✅ Logs divididos por entorno (development/staging/production)
- ✅ Logs de actividad separados
- ✅ Logs de SQL separados (solo dev)

### 6. **Seguridad (CRÍTICO para Multi-Tenant)**
- ✅ **CSRF Protection**:
  - `csrf_autoload` → true
  - `csrf_autoload_methods` → ['post', 'put', 'delete']
  - `csrf_bad_request_on_fail` → true
  - `csrf_auto_token` → true
  - `csrf_token_key` → 'fuel_csrf_token'
  - `csrf_expiration` → 7200 (2 horas)
  
- ✅ **Token Security**:
  - `token_salt` → Salt único generado
  
- ✅ **Headers**:
  - `allow_x_headers` → true (para reverse proxy)
  
- ✅ **Filtros**:
  - `uri_filter` → htmlentities
  - `output_filter` → Security::htmlentities
  
- ✅ **Whitelisted Classes** → Definidas

### 7. **Cookies (Configurado)**
- ✅ `expiration` → 0 (sesión del navegador)
- ✅ `path` → '/'
- ✅ `domain` → null (auto)
- ✅ `secure` → false (dev), true (producción HTTPS)
- ✅ `http_only` → true (protección XSS)

### 8. **Rutas de Módulos**
- ✅ `module_paths` → `fuel/app/modules/`
- ✅ Los módulos tenant (`packages_tenant/`) se cargan dinámicamente

### 9. **Rutas de Packages**
- ✅ `package_paths` → PKGPATH + TENANT_PKGPATH (dinámico)

### 10. **Auto-carga (IMPORTANTE)**
- ✅ **Packages**:
  - `orm` → ORM para base de datos
  - `auth` → Sistema de autenticación
  - `email` → Envío de correos
  - `parser` → Parser de templates
  
- ✅ **Config Auto-cargada**:
  - `db` → Configuración de base de datos
  - `session` → Configuración de sesiones

## 📁 Módulos Tenant Disponibles

En `fuel/packages_tenant/`:
- ✅ `admin` - Panel de administración
- ✅ `clients` - Gestión de clientes
- ✅ `landing` - Página de aterrizaje
- ✅ `partners` - Gestión de socios
- ✅ `providers` - Gestión de proveedores
- ✅ `sellers` - Gestión de vendedores
- ✅ `store` - Tienda/comercio
- ✅ `example_module` - Módulo de ejemplo

**Nota**: Estos módulos se activan/desactivan por tenant según la tabla `tenants.active_modules` (JSON).

## 🔧 Configuraciones por Entorno

### Development (`fuel/app/config/development/config.php`)
```php
'profiling' => true
'log_threshold' => Fuel::L_ALL
'caching' => false
'errors.notices' => true
```

### Staging (`fuel/app/config/staging/config.php`)
```php
'profiling' => true
'log_threshold' => Fuel::L_DEBUG
'caching' => true (30 min)
```

### Production (`fuel/app/config/production/config.php`)
```php
'profiling' => false
'log_threshold' => Fuel::L_WARNING
'caching' => true (60 min)
'errors.notices' => false
'cookie.secure' => true (HTTPS)
```

## 🔐 Sistema Multi-Tenant

### Resolución de Tenant
1. Se obtiene el `HTTP_HOST` (dominio)
2. Se consulta tabla `tenants` en base de datos `master`
3. Se obtiene `db_name` y `active_modules` del tenant
4. Se reconfigura la conexión `default` a la BD del tenant
5. Se cargan módulos activos del tenant

### Archivos Importantes
- `fuel/app/bootstrap.php` → Inicializa el sistema tenant
- `fuel/app/config/config_tenant.php` → Configuración y clase `Tenant_Resolver`
- `fuel/app/config/db.php` → Configuración de bases de datos (master + default)

## ⚠️ Pendientes de Configurar

### 1. Archivos de Configuración Faltantes:
- ❌ `fuel/app/config/db.php` - Configurar conexiones master/default
- ❌ `fuel/app/config/session.php` - Configurar sesiones
- ❌ `fuel/packages/auth/config/auth.php` - Configurar autenticación

### 2. Variables de Entorno:
- Cambiar `FUEL_ENV` en producción a `production`
- En `public/index.php` o `.htaccess`: `SetEnv FUEL_ENV production`

### 3. Seguridad en Producción:
- Generar un `token_salt` único y seguro
- Cambiar `cookie.secure` a `true` (requiere HTTPS)
- Configurar CSP (Content Security Policy) headers

## 📝 Siguientes Pasos

1. **Configurar Base de Datos**:
   - Crear `fuel/app/config/db.php`
   - Definir conexión `master` (tabla tenants)
   - Definir conexión `default` (se sobreescribe por tenant)

2. **Configurar Sesiones**:
   - Crear `fuel/app/config/session.php`
   - Definir driver (db, cookie, file, redis, etc.)

3. **Configurar Autenticación**:
   - Configurar `fuel/packages/auth/config/auth.php`
   - Elegir driver (SimpleAuth, OrmAuth, custom)

4. **Crear Tabla Tenants**:
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

¿Necesitas ayuda con alguno de estos puntos?
