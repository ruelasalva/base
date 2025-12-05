# Soluciones a Errores Comunes

Este documento registra soluciones a errores comunes encontrados durante el desarrollo del sistema.

## 1. Property "name" not found for Model_Provider

### Error
```
OutOfBoundsException [ Error ]:
Property "name" not found for Model_Provider.
```

### Causa
El modelo `Model_Provider` utiliza `company_name` como campo principal del nombre de la empresa, no `name`.

### Solución
Cambiar todas las referencias de `$provider->name` a `$provider->company_name` en vistas y controladores.

**Ejemplo:**
```php
// ❌ Incorrecto
<?php echo $provider->name; ?>

// ✅ Correcto
<?php echo $provider->company_name; ?>
```

**Archivos afectados:**
- `views/admin/compras/index.php` - Tabla y filtros
- `views/admin/compras/form.php` - Selector de proveedores
- `views/admin/compras/view.php` - Vista de detalle

**Fecha de solución:** 5 de diciembre de 2025

---

## 2. Cannot access offset of type string on string (Paginación)

### Error
```
TypeError [ Error ]:
Cannot access offset of type string on string
APPPATH/views/admin/compras/index.php @ line 186
<?php if ($pagination['total_pages'] > 1): ?>
```

### Causa
El objeto `Pagination` de FuelPHP se estaba accediendo como array cuando es un objeto.

### Solución
Usar las propiedades del objeto `Pagination` y el método `render()` en lugar de crear la paginación manualmente.

**Ejemplo:**
```php
// ❌ Incorrecto
<?php if ($pagination['total_pages'] > 1): ?>
    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
        <a href="<?php echo Uri::create('admin/compras/index', ['page' => $i]); ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>
<?php endif; ?>

// ✅ Correcto
<?php if ($pagination->total_pages > 1): ?>
    <div class="mt-3 d-flex justify-content-between align-items-center">
        <div class="text-muted">
            Mostrando <?php echo $pagination->offset + 1; ?> - 
            <?php echo min($pagination->offset + $pagination->per_page, $pagination->total_items); ?> 
            de <?php echo $pagination->total_items; ?> registros
        </div>
        <div>
            <?php echo $pagination->render(); ?>
        </div>
    </div>
<?php endif; ?>
```

**Propiedades disponibles del objeto Pagination:**
- `$pagination->total_pages` - Total de páginas
- `$pagination->current_page` - Página actual
- `$pagination->total_items` - Total de registros
- `$pagination->per_page` - Registros por página
- `$pagination->offset` - Offset actual
- `$pagination->render()` - Método que genera el HTML de paginación

**Archivos afectados:**
- `views/admin/compras/index.php`

**Módulos de referencia que lo hacen correctamente:**
- `views/admin/productos/index.php`
- `views/admin/proveedores/index.php`
- `views/admin/ordenescompra/index.php`

**Fecha de solución:** 5 de diciembre de 2025

**Archivos actualizados con patrón estándar:**
- `controller/admin/compras.php` - Pasa HTML + info de paginación
- `views/admin/compras/index.php` - Usa HTML renderizado
- `controller/admin/ordenescompra.php` - Pasa HTML + info de paginación
- `views/admin/ordenescompra/index.php` - Usa HTML renderizado
- `controller/admin/proveedores.php` - Ya implementado correctamente
- `views/admin/proveedores/index.php` - Ya implementado correctamente

---

## 3. Attempt to read property on string (Paginación inconsistente)

### Error
```
Fuel\Core\PhpErrorException [ Warning ]: 
Attempt to read property "total_pages" on string
<?php if ($pagination->total_pages > 1): ?>
```

### Causa
Inconsistencia en cómo se pasa la paginación entre controladores: algunos pasan el objeto completo, otros pasan el HTML renderizado con `$pagination->render()`.

### Solución Estándar (Implementada en todos los módulos)
**En el Controlador:** Pasar el HTML renderizado + información de paginación por separado:

```php
$pagination = Pagination::forge('nombre_pagination', $config);
$items = $query->limit($pagination->per_page)->offset($pagination->offset)->get();

$this->template->content = View::forge('admin/modulo/index', array(
    'items' => $items,
    'pagination' => $pagination->render(),  // ← HTML renderizado
    'pagination_info' => array(            // ← Info para mostrar registros
        'offset' => $pagination->offset,
        'per_page' => $pagination->per_page,
        'total_items' => $pagination->total_items,
        'total_pages' => $pagination->total_pages,
    ),
    // ... otras variables
));
```

**En la Vista:**
```php
<!-- Pagination -->
<?php if (!empty($pagination)): ?>
    <div class="mt-3 d-flex justify-content-between align-items-center">
        <div class="text-muted">
            Mostrando <?php echo $pagination_info['offset'] + 1; ?> - 
            <?php echo min($pagination_info['offset'] + $pagination_info['per_page'], $pagination_info['total_items']); ?> 
            de <?php echo $pagination_info['total_items']; ?> registros
        </div>
        <div>
            <?php echo $pagination; ?>
        </div>
    </div>
<?php endif; ?>
```

**Ventajas de este enfoque:**
- Separación de responsabilidades: el controlador genera el HTML
- Vista más simple, solo muestra lo que recibe
- Información de paginación accesible para mostrar "Mostrando X-Y de Z registros"
- Consistente con el patrón usado en módulo de proveedores
- El HTML generado por `render()` es compatible con Bootstrap automáticamente

**Módulos actualizados con este patrón:**
- ✅ Proveedores (ya estaba correcto)
- ✅ Compras (actualizado)
- ✅ Órdenes de Compra (actualizado)
- ⚠️ Productos (usa objeto completo, pero funciona - mantener o actualizar según preferencia)

**Fecha de solución:** 5 de diciembre de 2025

---

## Resumen de Patrones Estándar Implementados

### Módulos Completados con Patrón Moderno Bootstrap 5
Todos los siguientes módulos implementan el diseño moderno con las correcciones estándar:

1. **Productos** ✅
   - Vista index, form (5 tabs), view
   - Tags system, búsqueda avanzada
   - Tabs manuales implementados

2. **Proveedores** ✅  
   - Vista index, form (4 tabs), view
   - RFC validation, múltiples contactos
   - Paginación estándar (HTML + info)

3. **Órdenes de Compra** ✅
   - Vista index, form (3 tabs), view
   - Líneas dinámicas de productos
   - Workflow de aprobación
   - Paginación estándar aplicada

4. **Compras (Facturas)** ✅
   - Vista index, form (3 tabs), view
   - Upload de XML/PDF (CFDI)
   - Sistema de pagos parciales
   - Usa provider.company_name ✓
   - Paginación estándar ✓

5. **Contrarecibos (Delivery Notes)** ✅
   - Vista index, form (3 tabs), view
   - Tabla dinámica de productos recibidos
   - Comparativa ordenado vs recibido
   - Cálculo automático de % completitud
   - Vínculos a OC y facturas
   - Usa provider.company_name ✓
   - Paginación estándar ✓
   - Tabs manuales ✓

6. **Recepciones (Purchase Receipts)** ✅
   - Migration: `purchase_receipts.sql` (2 tablas)
   - Modelo: `model/purchasereceipt.php` (320 líneas, 13 métodos)
   - Modelo items: `model/purchasereceiptitem.php` (150 líneas)
   - Controlador: `controller/admin/recepciones.php` (600 líneas)
   - Vistas: index.php, form.php (3 tabs), view.php (3 tabs)
   - **Diferencia clave:** Ingreso físico al almacén con ubicaciones y condiciones
   - Campos: almacén, ubicación, condición (bueno/dañado/defectuoso/caducado)
   - Estados: pending → received → verified / discrepancy / cancelled
   - Lotes y fechas de caducidad
   - AJAX para cargar items de OC
   - Usa provider.company_name ✓
   - Paginación estándar ✓
   - Tabs manuales ✓

### Correcciones Aplicadas en Todos los Módulos

**1. Nombres de Campos del Modelo Provider:**
- ✅ Usar `provider.company_name` (NO `provider.name`)
- ✅ Verificado en controladores y vistas

**2. Paginación Estándar:**
- ✅ Controlador: Pasar `$pagination->render()` + `$pagination_info` array
- ✅ Vista: Mostrar contador "X-Y de Z registros" + HTML
- ✅ Verificar con `!empty($pagination)` antes de renderizar

**3. Tabs Bootstrap:**
- ✅ Implementación manual con JavaScript (NO `bootstrap.Tab`)
- ✅ Patrón documentado en `BOOTSTRAP_TABS_FIX.md`
- ✅ Aplicado consistentemente en todos los formularios y vistas

**4. Búsquedas:**
- ✅ Usar `provider.company_name` en queries LIKE
- ✅ Multi-campo con OR en búsquedas

**5. Productos:**
- ✅ Usar `products.is_active = 1` (NO `products.status`)
- ✅ Aplicado en todas las queries de productos

### Archivos de Documentación Creados
- `BOOTSTRAP_TABS_FIX.md` - Solución completa para tabs
- `COMMON_FIXES.md` - Este archivo con todas las correcciones
- `delivery_notes.sql` - Migración de contrarecibos
- `purchases.sql` - Migración de facturas de compra
- `purchase_receipts.sql` - Migración de recepciones físicas

**Última actualización:** 5 de diciembre de 2025 - Completado módulo de Recepciones

---

## Patrón Correcto de Paginación en FuelPHP

### En el Controlador:
```php
public function action_index()
{
    $query = Model_Example::query();
    
    // Configurar paginación
    $config = array(
        'pagination_url' => Uri::create('admin/modulo/index'),
        'total_items' => $query->count(),
        'per_page' => 20,
        'uri_segment' => 3,
    );
    
    // Crear objeto de paginación
    $pagination = Pagination::forge('modulo_pagination', $config);
    
    // Aplicar límite y offset a la consulta
    $items = $query
        ->limit($pagination->per_page)
        ->offset($pagination->offset)
        ->get();
    
    // Pasar el OBJETO (no array) a la vista
    $this->template->content = View::forge('admin/modulo/index', array(
        'items' => $items,
        'pagination' => $pagination, // ← Objeto completo
    ));
}
```

### En la Vista:
```php
<!-- Verificar si hay múltiples páginas -->
<?php if ($pagination->total_pages > 1): ?>
    <div class="mt-3 d-flex justify-content-between align-items-center">
        <!-- Información de registros -->
        <div class="text-muted">
            Mostrando <?php echo $pagination->offset + 1; ?> - 
            <?php echo min($pagination->offset + $pagination->per_page, $pagination->total_items); ?> 
            de <?php echo $pagination->total_items; ?> registros
        </div>
        
        <!-- Renderizar paginación automática -->
        <div>
            <?php echo $pagination->render(); ?>
        </div>
    </div>
<?php endif; ?>
```

**Nota:** El método `render()` genera automáticamente el HTML compatible con Bootstrap, incluyendo los enlaces correctos con los parámetros GET preservados.

---

## Notas Generales

- Siempre revisar módulos existentes que funcionen correctamente antes de implementar nuevas funcionalidades
- El patrón de módulos modernos está documentado en productos, proveedores y ordenescompra
- Para tabs, consultar `BOOTSTRAP_TABS_FIX.md`
- Para paginación, usar el patrón documentado en este archivo
