# Guía de Migración a Módulo Unificado

## Introducción

Esta guía proporciona instrucciones detalladas para migrar funcionalidades existentes a un **Módulo Unificado** dentro de la arquitectura Multi-tenancy Modular (SaaS) de FuelPHP 1.8.2.

**Principio Rector**: "Todo es un Módulo"

Cada área del sistema (Admin, Partners, Providers, etc.) es tratada como un package que gestiona tanto la funcionalidad como el acceso a sus respectivas áreas.

---

## Estructura de un Módulo Unificado

Cada módulo debe residir en `fuel/packages_tenant/` con la siguiente estructura:

```
fuel/packages_tenant/mi_modulo/
├── bootstrap.php           # Archivo de inicialización del módulo
├── classes/
│   ├── controller/         # Controladores del módulo
│   │   └── dashboard.php
│   ├── model/              # Modelos del módulo
│   │   └── example.php
│   └── service/            # Servicios del módulo
│       └── example.php
├── migrations/             # Migraciones de BD del módulo
│   ├── 001_create_tables.php
│   └── 002_add_columns.php
├── views/                  # Vistas del módulo
│   └── dashboard/
│       └── index.php
└── config/                 # Configuración del módulo (opcional)
    └── mi_modulo.php
```

---

## Paso 1: Crear la Clase del Módulo

Crear una clase que extienda `Module_Abstract` en `classes/module/mi_modulo.php`:

```php
<?php
/**
 * Mi Módulo - Clase de Módulo
 *
 * @package    Mi_Modulo
 * @version    1.0.0
 */

namespace Mi_Modulo;

/**
 * Clase principal del módulo que implementa el contrato de módulo
 */
class Module extends \Module_Abstract
{
    /**
     * @var string Versión del módulo
     */
    protected $version = '1.0.0';

    /**
     * @var array Dependencias del módulo
     */
    protected $dependencies = array();

    /**
     * @var array Permisos que registra el módulo
     */
    protected $permissions = array(
        'mi_modulo.access',
        'mi_modulo.create',
        'mi_modulo.edit',
        'mi_modulo.delete',
    );

    /**
     * Obtener el nombre legible del módulo
     */
    public function get_module_name()
    {
        return 'Mi Módulo';
    }

    /**
     * Obtener la clave única del módulo
     */
    public function get_module_key()
    {
        return 'mi_modulo';
    }

    /**
     * Obtener descripción del módulo
     */
    public function get_description()
    {
        return 'Descripción de mi módulo para el sistema ERP.';
    }

    /**
     * Obtener las rutas del módulo
     */
    public function get_routes()
    {
        return array(
            'mi_modulo'              => 'mi_modulo/dashboard/index',
            'mi_modulo/dashboard'    => 'mi_modulo/dashboard/index',
            'mi_modulo/items'        => 'mi_modulo/items/index',
            'mi_modulo/items/(:any)' => 'mi_modulo/items/$1',
        );
    }

    /**
     * Instalar el módulo
     */
    public function install()
    {
        try
        {
            // Ejecutar migraciones en la BD del tenant
            $this->run_migrations('up');
            
            // Configuración inicial (opcional)
            $this->setup_initial_data();
            
            \Log::info('Mi Módulo: Instalación completada');
            return true;
        }
        catch (\Exception $e)
        {
            \Log::error('Mi Módulo: Error en instalación - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Desinstalar el módulo
     */
    public function uninstall($preserve_data = true)
    {
        try
        {
            if (!$preserve_data)
            {
                // Revertir migraciones (eliminar tablas)
                $this->run_migrations('down');
            }
            
            \Log::info('Mi Módulo: Desinstalación completada');
            return true;
        }
        catch (\Exception $e)
        {
            \Log::error('Mi Módulo: Error en desinstalación - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Registrar clases del módulo
     */
    protected function register_classes()
    {
        \Autoloader::add_classes(array(
            'Mi_Modulo\\Controller_Dashboard' => __DIR__.'/controller/dashboard.php',
            'Mi_Modulo\\Controller_Items'     => __DIR__.'/controller/items.php',
            'Mi_Modulo\\Model_Item'           => __DIR__.'/model/item.php',
            'Mi_Modulo\\Service_ItemManager'  => __DIR__.'/service/itemmanager.php',
        ));

        \Autoloader::add_namespace('Mi_Modulo', __DIR__.'/');
    }

    /**
     * Configuración inicial del módulo
     */
    protected function setup_initial_data()
    {
        // Insertar datos iniciales si es necesario
    }
}
```

---

## Paso 2: Crear el Bootstrap del Módulo

El archivo `bootstrap.php` es el punto de entrada del módulo:

```php
<?php
/**
 * Mi Módulo - Bootstrap
 *
 * @package    Mi_Modulo
 * @version    1.0.0
 */

// Definir clave del módulo
if (!defined('MI_MODULO_KEY'))
{
    define('MI_MODULO_KEY', 'mi_modulo');
}

/**
 * Verificar si el módulo está activo para el tenant actual
 */
function mi_modulo_is_active()
{
    if (!defined('TENANT_ACTIVE_MODULES'))
    {
        return false;
    }

    $serialized = TENANT_ACTIVE_MODULES;

    if (empty($serialized) || !is_string($serialized))
    {
        return false;
    }

    $active_modules = unserialize($serialized, array('allowed_classes' => false));

    if ($active_modules === false || !is_array($active_modules))
    {
        return false;
    }

    return in_array(MI_MODULO_KEY, $active_modules, true);
}

/**
 * Inicializar el módulo solo si está activo
 */
if (mi_modulo_is_active())
{
    // Registrar clases con autoloader
    \Autoloader::add_classes(array(
        'Mi_Modulo\\Controller_Dashboard' => __DIR__.'/classes/controller/dashboard.php',
        'Mi_Modulo\\Controller_Items'     => __DIR__.'/classes/controller/items.php',
        'Mi_Modulo\\Model_Item'           => __DIR__.'/classes/model/item.php',
        'Mi_Modulo\\Service_ItemManager'  => __DIR__.'/classes/service/itemmanager.php',
    ));

    // Agregar namespace
    \Autoloader::add_namespace('Mi_Modulo', __DIR__.'/classes/');

    // Registrar rutas
    \Router::add(array(
        'mi_modulo'              => 'mi_modulo/dashboard/index',
        'mi_modulo/dashboard'    => 'mi_modulo/dashboard/index',
        'mi_modulo/items'        => 'mi_modulo/items/index',
        'mi_modulo/items/(:any)' => 'mi_modulo/items/$1',
    ));

    \Log::info('Mi Módulo: Cargado y activado para el tenant');
}
else
{
    \Log::debug('Mi Módulo: No activo para el tenant actual');
}
```

---

## Paso 3: Crear Controladores usando Controller_Module_Base

Los controladores deben extender `Controller_Module_Base` para obtener verificación automática de módulo y permisos:

```php
<?php
/**
 * Mi Módulo - Dashboard Controller
 */

namespace Mi_Modulo;

/**
 * Controlador del Dashboard del módulo
 */
class Controller_Dashboard extends \Controller_Module_Base
{
    /**
     * @var string Clave del módulo
     */
    protected $module_key = 'mi_modulo';

    /**
     * @var string Permiso requerido
     */
    protected $required_permission = 'mi_modulo.access';

    /**
     * @var array Permisos por acción
     */
    protected $action_permissions = array(
        'agregar'  => 'mi_modulo.create',
        'editar'   => 'mi_modulo.edit',
        'eliminar' => 'mi_modulo.delete',
    );

    /**
     * Dashboard principal
     */
    public function action_index()
    {
        $data = array(
            'title' => 'Dashboard de Mi Módulo',
        );

        $this->template->title = $data['title'];
        $this->template->content = \View::forge('mi_modulo/dashboard/index', $data);
    }
}
```

---

## Paso 4: Crear Migraciones de Base de Datos

Las migraciones del módulo van en `migrations/` y **se ejecutan SOLO en la BD del tenant**.

### Importante sobre Migraciones:
- Las migraciones se ejecutan en la conexión `default` (tenant actual)
- NUNCA se ejecutan en la conexión `master`
- Se ejecutan automáticamente cuando el módulo se activa por primera vez

### Estructura de Migración:

```php
<?php
/**
 * Migración: Crear tablas de Mi Módulo
 */

class Migration_001_Create_Mi_Modulo_Tables
{
    /**
     * Ejecutar migración
     */
    public function up()
    {
        // Usar conexión 'default' (tenant actual)
        \DBUtil::create_table('mi_modulo_items', array(
            'id' => array(
                'type' => 'int',
                'constraint' => 11,
                'auto_increment' => true,
            ),
            'name' => array(
                'type' => 'varchar',
                'constraint' => 255,
            ),
            'description' => array(
                'type' => 'text',
                'null' => true,
            ),
            'is_active' => array(
                'type' => 'tinyint',
                'constraint' => 1,
                'default' => 1,
            ),
            'created_at' => array(
                'type' => 'datetime',
            ),
            'updated_at' => array(
                'type' => 'datetime',
            ),
        ), array('id'), true, 'InnoDB', 'utf8_unicode_ci');

        // Crear índices
        \DBUtil::create_index('mi_modulo_items', 'is_active', 'idx_active');
    }

    /**
     * Revertir migración
     */
    public function down()
    {
        \DBUtil::drop_table('mi_modulo_items');
    }
}
```

---

## Paso 5: Activar el Módulo para un Tenant

Para activar el módulo para un tenant específico:

```php
// Usando Model_Tenant
$tenant_id = 1;
Model_Tenant::activate_module($tenant_id, 'mi_modulo');

// El módulo ejecutará automáticamente sus migraciones
// la próxima vez que el tenant acceda al sistema
```

---

## Paso 6: Verificación de Seguridad

### Protección de la Conexión Master

⚠️ **IMPORTANTE**: Nunca ejecutar migraciones o queries de módulos en la conexión `master`.

```php
// ❌ MAL - Nunca hacer esto en un módulo
\DB::query("INSERT INTO users...")->execute('master');

// ✅ BIEN - Usar siempre la conexión default (tenant)
\DB::query("INSERT INTO users...")->execute();
// o explícitamente
\DB::query("INSERT INTO users...")->execute('default');
```

### Model_Tenant usa siempre conexión Master

Solo `Model_Tenant` debe usar la conexión `master`:

```php
class Model_Tenant extends Model_Base
{
    // Solo esta clase usa 'master'
    protected static $_connection = 'master';
}
```

---

## Flujo de Activación de Módulo

```
1. Admin activa módulo para tenant X
   ↓
2. Model_Tenant::activate_module($tenant_id, 'mi_modulo')
   ↓
3. Se actualiza active_modules en tabla tenants (BD master)
   ↓
4. Próximo request del tenant X:
   ↓
5. Tenant_Resolver carga módulos activos
   ↓
6. bootstrap.php del módulo se carga
   ↓
7. Módulo ejecuta install() si es primera vez
   ↓
8. Migraciones se ejecutan en BD del tenant
   ↓
9. Rutas y clases del módulo están disponibles
```

---

## Checklist de Migración

- [ ] Crear directorio del módulo en `fuel/packages_tenant/`
- [ ] Crear `bootstrap.php` con verificación de módulo activo
- [ ] Crear clase de módulo extendiendo `Module_Abstract`
- [ ] Implementar métodos abstractos requeridos
- [ ] Crear controladores extendiendo `Controller_Module_Base`
- [ ] Definir `$module_key` y `$required_permission` en controladores
- [ ] Crear migraciones para tablas del módulo
- [ ] Crear modelos y servicios necesarios
- [ ] Crear vistas del módulo
- [ ] Probar activación/desactivación del módulo
- [ ] Verificar que migraciones solo afectan BD del tenant
- [ ] Documentar permisos y roles del módulo

---

## Ejemplo Completo

Ver el módulo `example_module` en `fuel/packages_tenant/example_module/` como referencia de implementación.

---

## Soporte

Para dudas sobre la arquitectura modular, consultar:
- `fuel/app/classes/module/abstract.php` - Contrato de módulo
- `fuel/app/classes/model/tenant.php` - Gestión de tenants
- `fuel/app/classes/controller/module/base.php` - Controlador base de módulo
