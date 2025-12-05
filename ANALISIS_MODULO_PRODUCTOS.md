# ANÁLISIS DEL MÓDULO DE PRODUCTOS - SISTEMA BASE

**Fecha**: 4 de Diciembre de 2024  
**Analista**: GitHub Copilot  
**Objetivo**: Documentar estructura existente del módulo de productos y corregir inconsistencias

---

## 1. ESTRUCTURA EXISTENTE DEL SISTEMA

### 1.1 Controlador Principal
**Ubicación**: `fuel/app/classes/controller/admin/catalogo/productos.php`  
**Clase**: `Controller_Admin_Catalogo_Productos extends Controller_Admin`  
**Ruta URL**: `/admin/catalogo/productos`

**Acciones Implementadas** (2,981 líneas):
- ✅ `action_index($search)` - Listado con búsqueda y paginación
- ✅ `action_csv()` - Importar productos desde CSV
- ✅ `action_buscar()` - Procesamiento de búsqueda
- ✅ `action_agregar()` - Crear nuevo producto
- ✅ `action_info($product_id)` - Visualizar detalles
- ✅ `action_editar($product_id)` - Editar producto existente
- ✅ `action_eliminar($product_id)` - Borrado lógico
- ✅ `action_agregar_archivo($product_id)` - Adjuntar PDFs técnicos
- *(Y más acciones...)*

**Sistema de Permisos Actual**:
```php
# El método before() solo verifica roles:
if(!Auth::member(100) && !Auth::member(50) && !Auth::member(25))
```
⚠️ **No usa `Helper_Permission::can()`** - Sistema de permisos no implementado completamente

---

### 1.2 Modelo Principal
**Ubicación**: `fuel/app/classes/model/product.php`  
**Clase**: `Model_Product extends \Orm\Model`  
**Tabla**: `products`

**Propiedades del Modelo** (451 líneas):
```php
// Identificación
id, slug, name, name_order, code, code_order, sku

// Clasificación
category_id, subcategory_id, brand_id

// Información SAT (México)
claveprodserv, claveunidad, codebar

// Unidades y Conversión
factor, purchase_unit_id, sale_unit_id

// Inventario y Precios
available, minimum_sale, minimum_order
original_price, price_per, weight

// Multimedia
image, description

// Estados
status, status_index, deleted
soon, newproduct, temporarily_sold_out
```

**Relaciones ORM Configuradas**:
```php
// Relaciones belongs_to
- category (Model_Category)
- subcategory (Model_Subcategory)  
- brand (Model_Brand)
- sale_unit (Model_Sat_Unit)
- purchase_unit (Model_Sat_Unit)
- claveunidad_sat (Model_Sat_Unit)

// Relaciones has_many
- galleries (Model_Products_Image)
- products_prices_wholesales (Model_Products_Prices_Wholesale)
- products_files (Model_Products_File)
- products_prices (Model_Products_Price)
- products_prices_amounts (Model_Products_Prices_Amount)
```

---

### 1.3 Vistas Existentes
**Ubicación**: `fuel/app/views/admin/catalogo/productos/`

**Archivos encontrados**:
```
✅ index.php               - Listado principal
✅ agregar.php             - Formulario de creación
✅ editar.php              - Formulario de edición
✅ info.php                - Vista de detalles
✅ csv.php                 - Importación CSV
✅ agregar_foto.php        - Galería de imágenes
✅ editar_foto.php         - Editar imagen de galería
✅ info_foto.php           - Ver imagen de galería
✅ agregar_archivo.php     - Adjuntar PDFs técnicos
✅ editar_archivo.php      - Modificar archivo
✅ info_archivo.php        - Ver detalles de archivo
✅ agregar_rango.php       - Precios por cantidad (mayoreo)
✅ editar_rango.php        - Editar rangos de precio
✅ info_rango.php          - Ver rangos configurados
```

---

### 1.4 Sistema de Permisos en Base de Datos

**Consulta ejecutada**:
```sql
SELECT id, module, action FROM permissions 
WHERE module LIKE '%producto%' OR module LIKE '%product%'
ORDER BY id;
```

**Resultado ANTES de corrección**:
| ID | Módulo | Acción | Estado |
|----|--------|--------|---------|
| 15-20 | `products` | view/create/edit/delete/import/export | ✅ Original del sistema |
| 148-151 | `productos` | view/create/edit/delete | ⚠️ Sin prefijo catalogo_ |
| 181-184 | `inventory_products` | view/create/edit/delete | ❌ **Creados por error** |

**Resultado DESPUÉS de corrección**:
```sql
UPDATE permissions SET module = 'catalogo_productos' 
WHERE module = 'inventory_products';
```
| ID | Módulo | Acción |
|----|--------|--------|
| 181 | `catalogo_productos` | view |
| 182 | `catalogo_productos` | create |
| 183 | `catalogo_productos` | edit |
| 184 | `catalogo_productos` | delete |

---

### 1.5 Integración en el Menú (Template)
**Ubicación**: `fuel/app/views/admin/template.php` (líneas 442-490)

```php
<!-- CATÁLOGO DE PRODUCTOS -->
<?php if (Helper_Permission::can('catalogo_productos', 'view')): ?>
    <li class="nav-item">
        <a class="nav-link" href="#navbar-productos" data-toggle="collapse">
            <i class="fa-solid fa-boxes-stacked text-info"></i>
            <span class="nav-link-text ml-2">Catálogo de Productos</span>
        </a>
        <div class="collapse" id="navbar-productos">
            <ul class="nav nav-sm flex-column ml-3">
                <?php if (Helper_Permission::can('catalogo_productos', 'view')): ?>
                    <li class="nav-item">
                        <?php echo Html::anchor('admin/catalogo/productos', 
                            '<i class="fa-solid fa-cube text-primary"></i> <span>Productos</span>', 
                            ['class' => 'nav-link']
                        ); ?>
                    </li>
                <?php endif; ?>
                
                <!-- Otros items del menú -->
                <?php if (Helper_Permission::can('catalogo_marcas', 'view')): ?>
                    <li><?php echo Html::anchor('admin/catalogo/marcas', '...'); ?></li>
                <?php endif; ?>
                
                <?php if (Helper_Permission::can('catalogo_categorias', 'view')): ?>
                    <li><?php echo Html::anchor('admin/catalogo/categorias', '...'); ?></li>
                <?php endif; ?>
                
                <?php if (Helper_Permission::can('catalogo_subcategorias', 'view')): ?>
                    <li><?php echo Html::anchor('admin/catalogo/subcategorias', '...'); ?></li>
                <?php endif; ?>
                
                <?php if (Helper_Permission::can('catalogo_montos', 'view')): ?>
                    <li><?php echo Html::anchor('admin/catalogo/montos', '...'); ?></li>
                <?php endif; ?>
            </ul>
        </div>
    </li>
<?php endif; ?>
```

**Icono**: `fa-solid fa-boxes-stacked text-info`  
**Ruta**: `admin/catalogo/productos`  
**Permiso requerido**: `catalogo_productos.view`

---

## 2. PROBLEMAS IDENTIFICADOS

### 2.1 ❌ Duplicación de Permisos
**Problema**: Existen 3 conjuntos diferentes de permisos para productos:
- `products` (original)
- `productos` (sin prefijo)
- `inventory_products` (creado por error)

**Impacto**: 
- Confusión en asignación de permisos a roles
- Inconsistencia entre menú y controlador
- Registros huérfanos en tabla `permissions`

**Solución Aplicada**:
```sql
-- ✅ Se renombró inventory_products a catalogo_productos
UPDATE permissions SET module = 'catalogo_productos' 
WHERE module = 'inventory_products';
```

---

### 2.2 ⚠️ Controlador Sin Sistema de Permisos
**Problema**: El controlador usa verificación de roles con `Auth::member()` en lugar de `Helper_Permission::can()`

**Código actual**:
```php
public function before()
{
    parent::before();
    
    // Solo verifica si el usuario pertenece a roles 100, 50 o 25
    if(!Auth::member(100) && !Auth::member(50) && !Auth::member(25))
    {
        Session::set_flash('error', 'No tienes los permisos para acceder a esta sección.');
        Response::redirect('admin');
    }
}
```

**Inconsistencia**:
- El **menú** usa: `Helper_Permission::can('catalogo_productos', 'view')`
- El **controlador** usa: `Auth::member(100) || Auth::member(50) || Auth::member(25)`

**Impacto**:
- El sistema de permisos granular (view/create/edit/delete) no funciona
- Usuarios con rol 25/50/100 tienen acceso total sin restricciones por acción

---

### 2.3 ⚠️ Tablas de Inventario Creadas por Error
**Problema**: Se crearon tablas con prefijo `inventory_*` cuando debían usar la tabla `products` existente

**Tablas creadas (migración 016)**:
```sql
CREATE TABLE inventory_products (...)
CREATE TABLE inventory_product_categories (...)
CREATE TABLE inventory_product_logs (...)
```

**Tabla correcta del sistema**:
```sql
products (ya existente desde antes)
```

**Estado actual**:
- ✅ Tablas `inventory_*` existen pero **NO SE USAN**
- ✅ El sistema usa la tabla `products` original
- ⚠️ Modelos `Model_Inventory_Product` creados **NO SON NECESARIOS**

---

## 3. ARQUITECTURA CORRECTA DEL SISTEMA

### 3.1 Patrón de Rutas FuelPHP
```
URL:         /admin/catalogo/productos
Controlador: fuel/app/classes/controller/admin/catalogo/productos.php
Clase:       Controller_Admin_Catalogo_Productos
Acción:      action_index(), action_agregar(), action_editar(), etc.
```

**Convención del Framework**:
- Ruta con `/` → Subcarpeta en `controller/`
- Nombre de archivo → Último segmento de la ruta
- Clase → CamelCase con guiones bajos de la ruta

---

### 3.2 Tabla de Productos Principal
```sql
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `subcategory_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `name_order` varchar(255) DEFAULT NULL,
  `code` varchar(255) NOT NULL,
  `code_order` varchar(255) DEFAULT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `factor` int(11) DEFAULT NULL,
  `purchase_unit_id` int(11) DEFAULT NULL,
  `sale_unit_id` int(11) DEFAULT NULL,
  `claveprodserv` int(11) DEFAULT NULL,
  `claveunidad` varchar(255) DEFAULT NULL,
  `codebar` bigint(20) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  `original_price` float NOT NULL,
  `available` int(11) NOT NULL DEFAULT '0',
  `minimum_sale` int(11) DEFAULT NULL,
  `minimum_order` int(11) DEFAULT NULL,
  `weight` float NOT NULL,
  `price_per` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `status_index` int(11) NOT NULL DEFAULT '0',
  `soon` int(11) NOT NULL DEFAULT '0',
  `newproduct` int(11) NOT NULL DEFAULT '0',
  `temporarily_sold_out` int(11) NOT NULL DEFAULT '0',
  `deleted` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

### 3.3 Tablas Relacionadas
```sql
-- Precios por tipo de cliente
products_prices (type_id, product_id, price)

-- Precios mayoristas por cantidad
products_prices_wholesales (product_id, min_quantity, max_quantity, price)

-- Montos especiales
products_prices_amounts (product_id, amount_id)

-- Galería de imágenes
galleries / products_images (product_id, image, order)

-- Archivos técnicos (PDFs, fichas)
products_files (product_id, file_type_id, file_name, file_path, downloads)

-- Tipos de archivos
products_file_types (name, description)
```

---

## 4. RECOMENDACIONES PARA INTEGRACIÓN

### 4.1 Implementar Sistema de Permisos Completo
**Actualizar método `before()` del controlador**:

```php
public function before()
{
    parent::before();
    
    // Verificar permiso base del módulo
    if (!Helper_Permission::can('catalogo_productos', 'view')) {
        Session::set_flash('error', 'No tienes permisos para acceder a esta sección.');
        Response::redirect('admin');
    }
}
```

**Agregar verificaciones por acción**:

```php
public function action_agregar()
{
    // Verificar permiso de creación
    if (!Helper_Permission::can('catalogo_productos', 'create')) {
        Session::set_flash('error', 'No tienes permisos para crear productos.');
        Response::redirect('admin/catalogo/productos');
    }
    
    // Resto del código...
}

public function action_editar($product_id = 0)
{
    // Verificar permiso de edición
    if (!Helper_Permission::can('catalogo_productos', 'edit')) {
        Session::set_flash('error', 'No tienes permisos para editar productos.');
        Response::redirect('admin/catalogo/productos');
    }
    
    // Resto del código...
}

public function action_eliminar($product_id = 0)
{
    // Verificar permiso de eliminación
    if (!Helper_Permission::can('catalogo_productos', 'delete')) {
        Session::set_flash('error', 'No tienes permisos para eliminar productos.');
        Response::redirect('admin/catalogo/productos');
    }
    
    // Resto del código...
}
```

---

### 4.2 Limpiar Permisos Duplicados
**Revisar y consolidar**:

```sql
-- Verificar permisos duplicados
SELECT module, action, COUNT(*) as total
FROM permissions
WHERE module IN ('products', 'productos', 'catalogo_productos')
GROUP BY module, action;

-- DECISIÓN PENDIENTE:
-- ¿Mantener 'products' (original) o 'catalogo_productos' (nuevo)?
-- ¿Eliminar 'productos' (sin prefijo)?
```

**Opción A - Mantener estructura original**:
```sql
-- Eliminar permisos nuevos y usar 'products'
DELETE FROM permissions WHERE module = 'catalogo_productos';
DELETE FROM permissions WHERE module = 'productos';

-- Actualizar template.php para usar 'products'
Helper_Permission::can('products', 'view')
```

**Opción B - Migrar a nueva convención** (RECOMENDADO):
```sql
-- Consolidar todo en 'catalogo_productos'
UPDATE permissions SET module = 'catalogo_productos' WHERE module = 'products';
DELETE FROM permissions WHERE module = 'productos';

-- Ya está en template.php correctamente
Helper_Permission::can('catalogo_productos', 'view')
```

---

### 4.3 Eliminar Código No Utilizado
**Archivos creados por error que NO se usan**:

```
❌ fuel/app/classes/model/inventory/product.php
❌ fuel/app/classes/model/inventory/product/category.php
❌ fuel/app/classes/model/inventory/product/log.php
❌ fuel/app/classes/helper/inventory/product.php
❌ fuel/app/classes/controller/admin/inventory/products.php (si existe)
❌ fuel/app/views/admin/inventory/products/* (si existen)
```

**Acción**:
```bash
# Eliminar directorio completo de inventory
Remove-Item -Recurse -Force fuel/app/classes/model/inventory
Remove-Item -Recurse -Force fuel/app/classes/helper/inventory
Remove-Item -Recurse -Force fuel/app/classes/controller/admin/inventory (si existe)
Remove-Item -Recurse -Force fuel/app/views/admin/inventory (si existe)
```

---

### 4.4 Decidir Sobre Tablas `inventory_*`
**Opciones**:

**A) Eliminar tablas creadas por error**:
```sql
DROP TABLE IF EXISTS inventory_product_logs;
DROP TABLE IF EXISTS inventory_product_categories;
DROP TABLE IF EXISTS inventory_products;
```

**B) Reutilizar para sistema de inventario separado** (futuro):
- Mantener tablas pero renombrar
- Usar para gestión de stock/almacenes independiente
- Crear módulo separado `/admin/inventario`

---

## 5. RESUMEN EJECUTIVO

### ✅ Estado Actual del Sistema
1. **Controlador funcional** en `admin/catalogo/productos.php` (2,981 líneas)
2. **Modelo ORM completo** con todas las relaciones configuradas
3. **14 vistas** implementadas para CRUD y operaciones avanzadas
4. **Menú configurado** correctamente con permiso `catalogo_productos`
5. **Permisos corregidos** en base de datos (IDs 181-184)

### ⚠️ Problemas Pendientes
1. Controlador usa `Auth::member()` en lugar de `Helper_Permission::can()`
2. Permisos duplicados: `products`, `productos`, `catalogo_productos`
3. Tablas `inventory_*` no utilizadas ocupando espacio
4. Modelos/helpers/vistas de "inventory" no se usan

### 📋 Tareas Recomendadas (Orden de Prioridad)

**PRIORIDAD ALTA**:
1. ✅ Actualizar permisos en BD (`inventory_products` → `catalogo_productos`) - **COMPLETADO**
2. ⏳ Implementar `Helper_Permission::can()` en todas las acciones del controlador
3. ⏳ Consolidar permisos duplicados (decidir entre `products` vs `catalogo_productos`)

**PRIORIDAD MEDIA**:
4. ⏳ Eliminar archivos no utilizados del directorio `inventory/`
5. ⏳ Decidir qué hacer con tablas `inventory_*` (eliminar o reutilizar)
6. ⏳ Actualizar roles de usuario para usar permisos granulares

**PRIORIDAD BAJA**:
7. ⏳ Documentar API del helper `Helper_Permission`
8. ⏳ Crear tests unitarios para verificar permisos
9. ⏳ Implementar logs de auditoría en cambios de productos

---

## 6. CONVENCIONES DEL PROYECTO

### Nomenclatura de Permisos
```
Patrón: {seccion}_{modulo}.{accion}

Ejemplos:
- catalogo_productos.view
- catalogo_productos.create
- catalogo_productos.edit
- catalogo_productos.delete
- catalogo_marcas.view
- catalogo_categorias.view
```

### Estructura de Rutas
```
URL: /admin/{seccion}/{modulo}/{accion}/{id}

Ejemplos:
/admin/catalogo/productos              → index
/admin/catalogo/productos/agregar      → agregar
/admin/catalogo/productos/editar/123   → editar (product_id=123)
/admin/catalogo/productos/info/123     → info (product_id=123)
```

### Organización de Archivos
```
fuel/app/
├── classes/
│   ├── controller/
│   │   └── admin/
│   │       └── catalogo/
│   │           └── productos.php          ← Controlador principal
│   ├── model/
│   │   └── product.php                    ← Modelo principal
│   └── helper/
│       └── permission.php                 ← Helper de permisos
└── views/
    └── admin/
        └── catalogo/
            └── productos/                  ← Vistas del módulo
                ├── index.php
                ├── agregar.php
                ├── editar.php
                └── info.php
```

---

**Documento generado automáticamente**  
**Última actualización**: 4 de Diciembre de 2024  
**Versión**: 1.0

