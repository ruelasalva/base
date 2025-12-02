# Sistema de Logs - Configuración

## Configuración por Entorno

El sistema está configurado para usar diferentes niveles de log según el entorno:

### Development (Desarrollo)
- **Nivel**: `Fuel::L_ALL` - Captura todos los logs
- **Profiling**: Activado
- **Ubicación**: `fuel/app/logs/`
- Archivos generados: `YYYY/MM/DD.php`

### Staging (Pruebas)
- **Nivel**: `Fuel::L_DEBUG` - Captura debug, warnings y errors
- **Profiling**: Activado
- **Caching**: Activado (30 min)

### Production (Producción)
- **Nivel**: `Fuel::L_WARNING` - Solo warnings y errors
- **Profiling**: Desactivado
- **Caching**: Activado (60 min)

## Cambiar de Entorno

Edita el archivo `public/index.php` o configura la variable de entorno:

```php
// Development
SetEnv FUEL_ENV development

// Staging
SetEnv FUEL_ENV staging

// Production
SetEnv FUEL_ENV production
```

## Uso del Helper de Logs

### Logs Básicos

```php
// Log de información general
Log::info('Usuario accedió al dashboard', array(
    'user_id' => 123,
    'ip' => '192.168.1.1'
));

// Log de debug (solo en development)
Log::debug('Variable procesada', array(
    'variable' => $data,
    'count' => count($data)
));

// Log de advertencia
Log::warning('Intento de acceso sin permisos', array(
    'user_id' => 456,
    'resource' => '/admin/users'
));

// Log de error
Log::error('Error al guardar en BD', array(
    'exception' => $e->getMessage(),
    'trace' => $e->getTraceAsString()
));
```

### Logs de Actividad de Usuario (Auditoría)

```php
// Se guarda en fuel/app/logs/activity/YYYY-MM-DD.log
Log::activity('login', $user_id, array(
    'ip' => Input::real_ip(),
    'user_agent' => Input::user_agent()
));

Log::activity('create_record', $user_id, array(
    'table' => 'users',
    'record_id' => $new_id
));

Log::activity('delete_record', $user_id, array(
    'table' => 'products',
    'record_id' => 789
));
```

### Logs de SQL (solo Development)

```php
// Se guarda en fuel/app/logs/sql/YYYY-MM-DD.log
Log::sql(
    'SELECT * FROM users WHERE id = ?',
    0.023, // tiempo de ejecución en segundos
    array(123) // parámetros
);
```

## Logs Nativos de FuelPHP

También puedes usar los logs nativos:

```php
\Fuel\Core\Log::info('Mensaje de información');
\Fuel\Core\Log::debug('Mensaje de debug');
\Fuel\Core\Log::warning('Mensaje de advertencia');
\Fuel\Core\Log::error('Mensaje de error');
```

## Estructura de Archivos de Log

```
fuel/app/logs/
├── 2025/
│   └── 12/
│       ├── 01.php       # Logs generales del día
│       └── 02.php
├── activity/
│   ├── 2025-12-01.log  # Actividad de usuarios
│   └── 2025-12-02.log
└── sql/
    ├── 2025-12-01.log  # Consultas SQL (solo dev)
    └── 2025-12-02.log
```

## Limpiar Logs Antiguos

Crea una tarea programada para limpiar logs:

```php
// fuel/app/tasks/cleanlogs.php
public function run()
{
    // Eliminar logs de hace más de 30 días
    $path = APPPATH . 'logs/';
    $days = 30;
    
    // Implementar lógica de limpieza
}
```

Ejecutar: `php oil refine cleanlogs`

## Niveles de Log

- **L_NONE**: Sin logs
- **L_ERROR**: Solo errores críticos
- **L_WARNING**: Errores + Advertencias
- **L_DEBUG**: Errores + Advertencias + Debug
- **L_INFO**: Errores + Advertencias + Debug + Info
- **L_ALL**: Todos los logs

## Recomendaciones

1. **Development**: Usar `L_ALL` para ver todo
2. **Staging**: Usar `L_DEBUG` para detectar problemas
3. **Production**: Usar `L_WARNING` para reducir I/O
4. Rotar logs periódicamente para no llenar el disco
5. Usar logs de actividad para auditoría de seguridad
6. Monitorear logs de error en producción
