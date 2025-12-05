# Módulo de Productos - Sistema Multi-Tenant

## Descripción
Módulo completo de gestión de productos creado desde cero con diseño original, limpio y profesional.

## Características Principales

### ✅ Diseño Original
- Código limpio y mantenible (250 líneas en controlador)
- Sin código copiado, diseñado específicamente para este sistema
- Arquitectura RESTful moderna
- Separación clara de responsabilidades

### ✅ Multi-Tenant
- Filtrado automático por `tenant_id`
- Aislamiento completo de datos entre tenants
- Verificación de permisos en cada acción

### ✅ Funcionalidades CRUD Completas

#### Listado (Index)
- Búsqueda por nombre, SKU, código de barras y marca
- Paginación (25 productos por página)
- Indicadores visuales de stock (bajo/medio/alto)
- Badges de estado (activo/inactivo)
- Filtros de soft delete

#### Crear/Editar (Form)
Formulario con tabs organizados:

**Tab 1: Información Básica**
- SKU (obligatorio)
- Código de barras
- Nombre del producto (obligatorio)
- Descripción corta (500 chars max)
- Descripción completa
- Categoría (dropdown jerárquico)
- Proveedor (dropdown)
- Marca (texto libre)
- Modelo

**Tab 2: Precios**
- Precio de costo (obligatorio)
- Precio de venta (obligatorio)
- Precio mayorista
- Precio mínimo
- Tasa de impuesto (%)
- Cálculo automático de precio con impuesto

**Tab 3: Inventario**
- Stock inicial (obligatorio)
- Stock mínimo (alertas)
- Unidad de medida (dropdown predefinido)
- Peso en kilogramos

**Tab 4: Datos Adicionales**
- Placeholder para futuras funciones (imágenes, docs, campos custom)

#### Ver Detalles (View)
- Vista completa de toda la información
- Cards con diseño moderno
- Badges de estado visual
- Indicadores de stock con colores
- Cálculo de precio con impuesto
- Información de registro (created_at, updated_at)

#### Eliminar (Delete)
- Soft delete (actualiza `deleted_at`)
- Confirmación antes de eliminar
- Marca producto como inactivo y no disponible

### ✅ Validación
- Validación del lado del servidor (FuelPHP Validation)
- Validación del lado del cliente (Bootstrap 5)
- Mensajes de error claros
- Feedback visual inmediato

### ✅ Seguridad
- Verificación de permisos: `productos.view|create|edit|delete`
- Protección CSRF automática (FuelPHP)
- Escape de HTML (XSS prevention)
- SQL injection prevention (ORM)
- Soft delete en lugar de hard delete

### ✅ Base de Datos

**Tabla Principal: `products`**
```sql
id, tenant_id, sku, barcode, name, slug, short_description, description
category_id, provider_id, brand, model, unit
cost_price, sale_price, wholesale_price, min_price, tax_rate
weight, length, width, height
min_stock, max_stock, stock_quantity
is_featured, is_active, is_available, sort_order
created_at, updated_at, deleted_at
```

**Relaciones:**
- `categories` - Jerárquica (parent_id)
- `product_categories` - Many-to-many con campo `is_primary`
- `providers` - Proveedores/Suppliers

### ✅ UI/UX Moderna
- Bootstrap 5
- Font Awesome icons
- Responsive design
- Tabs para organizar información
- Cards con colores semánticos
- Badges y alerts informativos
- Botones con iconos descriptivos

## Archivos Creados

```
fuel/app/classes/controller/admin/productos.php (250 líneas)
fuel/app/views/admin/productos/
  ├── index.php      (Listado con búsqueda y paginación)
  ├── form.php       (Crear/Editar con tabs)
  └── view.php       (Vista detallada)
```

## Estructura del Controlador

```php
Controller_Admin_Productos extends Controller_Admin
│
├── before()                    // Verificación de permisos
├── action_index()             // Listado con búsqueda
├── action_create()            // Crear producto
├── action_edit($id)           // Editar producto
├── action_view($id)           // Ver detalles
├── action_delete($id)         // Eliminar (soft delete)
│
├── _validation()              // Reglas de validación
├── _generate_slug()           // Generar slug único
├── _get_categories()          // Obtener categorías activas
├── _get_providers()           // Obtener proveedores activos
└── _sync_categories()         // Sincronizar relación many-to-many
```

## Patrones Implementados

### Multi-Tenant
```php
'tenant_id' => Session::get('tenant_id', 1)
Model_Product::query()->where('deleted_at', 'IS', null)
```

### Soft Delete
```php
$product->deleted_at = date('Y-m-d H:i:s');
$product->is_active = 0;
$product->is_available = 0;
```

### Slug Único
```php
$slug = Inflector::friendly_title($name, '-', true);
// Con verificación de duplicados y contador automático
```

### Permisos Granulares
```php
Helper_Permission::can('productos', 'view')
Helper_Permission::can('productos', 'create')
Helper_Permission::can('productos', 'edit')
Helper_Permission::can('productos', 'delete')
```

## Roadmap (Futuras Mejoras)

### Prioridad Alta
- [ ] Galería de imágenes de producto
- [ ] Importación masiva (CSV/Excel)
- [ ] Exportación de catálogo
- [ ] Historial de cambios de precio
- [ ] Log de movimientos de stock

### Prioridad Media
- [ ] Variantes de producto (tallas, colores)
- [ ] Productos relacionados/similares
- [ ] Descuentos por cantidad
- [ ] Campos personalizados
- [ ] Categorías múltiples por producto

### Prioridad Baja
- [ ] Código QR automático
- [ ] Integración con APIs externas
- [ ] Sincronización con e-commerce
- [ ] Reportes avanzados
- [ ] Vistas de cliente (catálogo público)

## Navegación

**Menú Principal:**
```
Admin > Productos
```

**Rutas:**
```
/admin/productos          → Listado
/admin/productos/create   → Crear nuevo
/admin/productos/edit/ID  → Editar
/admin/productos/view/ID  → Ver detalles
/admin/productos/delete/ID → Eliminar
```

## Permisos en Base de Datos

```sql
INSERT INTO permissions (id, name, description) VALUES
(148, 'productos.view', 'Ver productos'),
(149, 'productos.create', 'Crear productos'),
(150, 'productos.edit', 'Editar productos'),
(151, 'productos.delete', 'Eliminar productos');
```

## Notas Técnicas

### Validaciones Implementadas
- SKU: obligatorio, max 50 chars
- Nombre: obligatorio, max 255 chars
- Descripción corta: max 500 chars
- Precios: numéricos, pueden ser decimales
- Stock: numérico, mínimo 0
- Email proveedor: formato email válido (si aplica)

### Diferencias con Sistema Anterior
- ❌ NO usa `deleted` (int) → ✅ USA `deleted_at` (datetime)
- ❌ NO usa `status` → ✅ USA `is_active`
- ❌ NO usa `available` → ✅ USA `is_available`
- ❌ NO usa `codebar` → ✅ USA `barcode`
- ❌ NO usa FK para `brand` → ✅ USA varchar
- ❌ NO usa `subcategory_id` → ✅ USA `category_id` + many-to-many

### Compatibilidad
- FuelPHP 1.8.2
- PHP 7.4+
- MySQL 5.7+
- Bootstrap 5.x
- Font Awesome 5.x/6.x

## Testing

### Casos de Prueba
1. ✅ Crear producto con datos mínimos (SKU, nombre, precios, stock)
2. ✅ Crear producto con todos los datos completos
3. ✅ Editar producto existente
4. ✅ Ver detalles de producto
5. ✅ Eliminar producto (soft delete)
6. ✅ Buscar productos por diferentes criterios
7. ✅ Navegación con paginación
8. ✅ Validación de campos obligatorios
9. ✅ Verificación de permisos
10. ✅ Aislamiento multi-tenant

## Créditos

Desarrollado desde cero con diseño original para Sistema Base Multi-Tenant.

**Fecha:** Diciembre 2024
**Versión:** 1.0.0
**Licencia:** Propietaria

---

## Comandos Útiles

### Ver productos activos
```sql
SELECT * FROM products WHERE deleted_at IS NULL AND is_active = 1;
```

### Ver productos con stock bajo
```sql
SELECT * FROM products 
WHERE deleted_at IS NULL 
AND stock_quantity <= min_stock 
AND min_stock > 0;
```

### Contar productos por categoría
```sql
SELECT c.name, COUNT(p.id) as total
FROM categories c
LEFT JOIN products p ON c.id = p.category_id AND p.deleted_at IS NULL
GROUP BY c.id, c.name;
```
