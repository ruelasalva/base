# Sistema Multi-Idioma

## Estructura Creada

### Archivos de Idioma

```
fuel/app/lang/
├── es/
│   ├── common.php      # Traducciones generales en español
│   └── admin.php       # Traducciones del módulo admin en español
└── en/
    ├── common.php      # Traducciones generales en inglés
    └── admin.php       # Traducciones del módulo admin en inglés
```

### Helper de Idioma

`fuel/app/classes/helper/lang.php` - Funciones auxiliares para traducciones

## Uso de Traducciones

### En Controladores

```php
// Obtener traducción
$texto = Lang::get('common.actions.save'); // "Guardar"

// Con helper corto
$texto = __('common.actions.save'); // "Guardar"

// Con parámetros
$mensaje = __('common.success.saved'); // "Registro guardado correctamente"

// Módulo admin
$titulo = __('admin.title'); // "Panel de Administración"
```

### En Vistas

```php
<!-- Mostrar traducción -->
<?php echo __('common.actions.add'); ?>

<!-- O con echo directo -->
<?php _e('common.actions.save'); ?>

<!-- En formularios -->
<label><?php echo __('common.fields.name'); ?></label>
<input type="text" name="name" placeholder="<?php echo __('common.fields.name'); ?>" />

<button type="submit"><?php echo __('common.actions.submit'); ?></button>
```

### Cambiar Idioma

```php
// Cambiar a español
set_language('es');

// Cambiar a inglés
set_language('en');

// Obtener idioma actual
$lang = get_current_language(); // 'es' o 'en'
```

### Formatear Fechas

```php
// Formato corto
echo format_date('2025-12-02', 'short'); // 02/12/2025 (es) o 12/02/2025 (en)

// Formato largo
echo format_date('2025-12-02', 'long'); // 02 de Diciembre de 2025

// Con hora
echo format_date(time(), 'datetime'); // 02/12/2025 14:30:45
```

### Pluralización

```php
echo pluralize(1, 'producto'); // "1 producto"
echo pluralize(5, 'producto'); // "5 productos"
echo pluralize(10, 'usuario'); // "10 usuarios"
```

## Configuración

El idioma por defecto está configurado en `fuel/app/config/config.php`:

```php
'language' => 'es',
'language_fallback' => 'en',
```

## Cargar Automáticamente

En `fuel/app/config/config.php` agregar a `always_load`:

```php
'always_load' => array(
    'config' => array('db', 'session'),
    'language' => array('common'), // Cargar traducciones comunes
),
```

## Agregar Nuevos Idiomas

1. Crear carpeta: `fuel/app/lang/fr/` (para francés)
2. Copiar archivos de `es/` o `en/`
3. Traducir contenido
4. Actualizar helper si necesitas reglas de pluralización específicas

## Traducciones por Módulo Tenant

Cada módulo puede tener sus propias traducciones:

```
fuel/packages_tenant/admin/lang/
├── es/
│   └── admin.php
└── en/
    └── admin.php
```

Uso:
```php
Lang::load('admin::admin'); // Cargar traducciones del módulo admin
echo __('admin::admin.dashboard.welcome');
```

## Validación con Traducciones

```php
$val = Validation::forge();
$val->add_field('name', __('common.fields.name'), 'required|min_length[3]');

if ( ! $val->run())
{
    foreach ($val->error() as $field => $error)
    {
        echo $error->get_message(); // Mensaje traducido
    }
}
```

## Ejemplo Completo en Vista

```php
<!DOCTYPE html>
<html lang="<?php echo get_current_language(); ?>">
<head>
    <title><?php echo __('admin.title'); ?></title>
</head>
<body>
    <h1><?php echo __('admin.dashboard.welcome'); ?></h1>
    
    <nav>
        <a href="/admin"><?php echo __('admin.menu.dashboard'); ?></a>
        <a href="/admin/users"><?php echo __('admin.menu.users'); ?></a>
        <a href="/admin/settings"><?php echo __('admin.menu.settings'); ?></a>
    </nav>
    
    <div class="stats">
        <div><?php echo __('admin.stats.users'); ?>: <?php echo $user_count; ?></div>
        <div><?php echo __('admin.stats.orders'); ?>: <?php echo $orders_today; ?></div>
    </div>
    
    <button><?php echo __('common.actions.add'); ?></button>
</body>
</html>
```
