# Guía de Migraciones del Sistema ERP

Este directorio contiene las migraciones SQL del sistema. Las migraciones permiten crear y actualizar el esquema de la base de datos de manera controlada y versionada.

## Estructura de Archivos

Las migraciones deben seguir el siguiente formato de nomenclatura:

```
NNN_nombre_descriptivo.sql
```

Donde:
- `NNN` es un número de 3 dígitos (001, 002, 003, etc.)
- `nombre_descriptivo` describe brevemente qué hace la migración

### Ejemplos

```
001_auth_tables.sql        - Tablas de autenticación y permisos
002_productos.sql          - Tablas de productos
003_categorias.sql         - Tablas de categorías
004_pedidos.sql            - Tablas de pedidos
005_add_campo_telefono.sql - Añade campo teléfono a tabla existente
```

## Cómo Crear una Nueva Migración

1. **Crear el archivo SQL** en este directorio con el número de versión siguiente

2. **Escribir el SQL** con las instrucciones DDL necesarias:

```sql
-- ============================================================================
-- Migración: 002_products
-- Descripción: Crea las tablas para el módulo de productos
-- Fecha: 2024-01-15
-- ============================================================================

CREATE TABLE IF NOT EXISTS `products` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `sku` VARCHAR(50) NOT NULL COMMENT 'Stock Keeping Unit',
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `stock_quantity` INT(11) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_sku` (`sku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**IMPORTANTE:** Se usa nomenclatura en inglés para las tablas como estándar internacional:
- `products` (no productos)
- `categories` (no categorias)
- `providers` (no proveedores)
- `customers` (no clientes)
- `orders` (no pedidos)
- `product_attributes` (para atributos adicionales)
- `order_items` (para detalles de pedidos)

3. **Acceder al instalador** en `/install` para ejecutar la migración

## Buenas Prácticas

### ✅ Hacer

- Usar `CREATE TABLE IF NOT EXISTS` para evitar errores si la tabla ya existe
- Incluir comentarios descriptivos en cada migración
- Usar `utf8mb4` como charset predeterminado
- Usar nombres de tablas en inglés como estándar internacional
- Definir índices apropiados para campos de búsqueda frecuente
- Usar `DATETIME` con `DEFAULT CURRENT_TIMESTAMP` para campos de fecha
- Incluir campos `created_at` y `updated_at` en todas las tablas
- Usar `INT UNSIGNED` para IDs auto-incrementales
- Nombrar foreign keys con prefijo `fk_`

### ❌ Evitar

- Modificar migraciones ya ejecutadas en producción
- Usar `DROP TABLE` sin verificar primero
- Crear migraciones con múltiples responsabilidades
- Nombres de tabla sin prefijo claro del módulo

## Modificar Tablas Existentes

Para modificar tablas existentes, crear una nueva migración:

```sql
-- ============================================================================
-- Migración: 010_add_telefono_usuarios
-- Descripción: Añade campo teléfono a la tabla de usuarios
-- Fecha: 2024-02-20
-- ============================================================================

-- Verificar si la columna no existe antes de añadirla
SET @dbname = DATABASE();
SET @tablename = 'users';
SET @columnname = 'phone_secondary';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = @dbname
        AND TABLE_NAME = @tablename
        AND COLUMN_NAME = @columnname
    ) > 0,
    'SELECT 1',
    CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' VARCHAR(20) DEFAULT NULL AFTER phone')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;
```

## Registro de Migraciones

El instalador mantiene un registro de todas las migraciones ejecutadas en la tabla `migrations`:

```sql
SELECT * FROM migrations ORDER BY executed_at;
```

Esta tabla contiene:
- `migration`: Nombre del archivo de migración
- `batch`: Número de lote (grupo de migraciones ejecutadas juntas)
- `executed_at`: Fecha y hora de ejecución

## Rollback (Reversión)

Actualmente, el sistema no soporta rollback automático. Si necesita revertir cambios:

1. Cree una nueva migración que deshaga los cambios
2. O restaure un backup de la base de datos

## Entornos

Las migraciones se ejecutan por entorno (development, production, etc.). Cada entorno mantiene su propio registro de migraciones ejecutadas.

## Soporte

Para más información, consulte la documentación del sistema o contacte al equipo de desarrollo.
