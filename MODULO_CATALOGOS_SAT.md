# Módulo Catálogos SAT - Documentación

## Descripción General

El módulo **Catálogos SAT** proporciona acceso a los catálogos oficiales del Servicio de Administración Tributaria (SAT) necesarios para la emisión de Comprobantes Fiscales Digitales por Internet (CFDI) y el cumplimiento de obligaciones fiscales en México.

## Características Principales

### 1. Catálogos Incluidos

El módulo incluye 7 catálogos fiscales fundamentales:

#### a) Productos y Servicios (c_ClaveProdServ)
- **Tabla**: `sat_productos_servicios`
- **Registros iniciales**: 81 códigos más comunes
- **Campos principales**:
  - `clave`: Código SAT de 8 dígitos
  - `descripcion`: Descripción del producto/servicio
  - `palabras_similares`: Palabras clave para búsqueda
  - `incluye_iva`: Indica si incluye IVA (Sí/No/Opcional)
  - `incluye_ieps`: Indica si incluye IEPS (Sí/No/Opcional)
  - `complemento`: Complemento que debe usarse

#### b) Unidades de Medida (c_ClaveUnidad)
- **Tabla**: `sat_unidades`
- **Registros iniciales**: 10 unidades más comunes
- **Campos principales**:
  - `clave`: Código SAT (ej: H87, E48, KGM)
  - `nombre`: Nombre de la unidad
  - `simbolo`: Símbolo de la unidad (ej: Pza, kg, m)
  - `descripcion`: Descripción detallada

#### c) Uso de CFDI (c_UsoCFDI)
- **Tabla**: `sat_uso_cfdi`
- **Registros iniciales**: 10 usos más comunes
- **Campos principales**:
  - `clave`: Código SAT (ej: G01, G03, P01)
  - `descripcion`: Descripción del uso
  - `aplica_fisica`: Si aplica para persona física
  - `aplica_moral`: Si aplica para persona moral

#### d) Formas de Pago (c_FormaPago)
- **Tabla**: `sat_formas_pago`
- **Registros iniciales**: 6 formas más comunes
- **Campos principales**:
  - `clave`: Código SAT (01-99)
  - `descripcion`: Forma de pago
  - `bancarizado`: Si requiere información bancaria
  - `numero_operacion`: Si permite capturar número de operación

#### e) Métodos de Pago (c_MetodoPago)
- **Tabla**: `sat_metodos_pago`
- **Registros iniciales**: 2 métodos (PUE, PPD)
- **Campos principales**:
  - `clave`: PUE (Pago en Una Exhibición) o PPD (Pago en Parcialidades)
  - `descripcion`: Descripción del método

#### f) Tipos de Comprobante (c_TipoDeComprobante)
- **Tabla**: `sat_tipos_comprobante`
- **Registros iniciales**: 5 tipos (I, E, T, N, P)
- **Campos principales**:
  - `clave`: I (Ingreso), E (Egreso), T (Traslado), N (Nómina), P (Pago)
  - `descripcion`: Descripción del tipo
  - `valor_maximo`: Valor máximo permitido (si aplica)

#### g) Regímenes Fiscales (c_RegimenFiscal)
- **Tabla**: `sat_regimenes_fiscales`
- **Registros iniciales**: 17 regímenes vigentes
- **Campos principales**:
  - `clave`: Código SAT (601, 612, 626, etc.)
  - `descripcion`: Descripción del régimen
  - `aplica_fisica`: Si aplica para persona física
  - `aplica_moral`: Si aplica para persona moral

### 2. Funcionalidades del Módulo

#### Visualización
- **Dashboard principal**: Muestra las 7 categorías de catálogos con estadísticas
- **Vistas individuales**: Cada catálogo tiene su propia vista con tabla y filtros
- **Paginación**: Los catálogos grandes tienen paginación automática
- **Búsqueda**: Sistema de búsqueda por clave o descripción

#### Filtrado
- **Búsqueda por texto**: En claves, descripciones y palabras similares
- **Filtros específicos**:
  - Uso de CFDI: Por tipo de persona (física/moral)
  - Regímenes Fiscales: Por tipo de persona (física/moral)

#### Exportación
- **Formato CSV**: Todos los catálogos pueden exportarse a CSV
- **Ruta**: `/admin/sat/export/{catalogo}`
- **Ejemplos**:
  - `/admin/sat/export/productos`
  - `/admin/sat/export/unidades`
  - `/admin/sat/export/uso_cfdi`

#### API AJAX
- **Búsqueda general**: `/admin/sat/search?catalog=all&q=busqueda&limit=10`
- **Obtener por clave**: `/admin/sat/get?catalog=productos&clave=50202300`

### 3. Arquitectura del Código

#### Modelo (`Model_SatCatalog`)
Ubicación: `fuel/app/classes/model/satcatalog.php`

Métodos principales:
```php
// Obtener registros de cada catálogo con filtros
get_productos_servicios($filters, $limit, $offset)
get_unidades($filters, $limit, $offset)
get_uso_cfdi($filters, $limit, $offset)
get_formas_pago($filters, $limit, $offset)
get_metodos_pago($filters, $limit, $offset)
get_tipos_comprobante($filters, $limit, $offset)
get_regimenes_fiscales($filters, $limit, $offset)

// Métodos auxiliares
count_productos_servicios($filters)
search_all($search, $catalog, $limit)
get_statistics()
get_by_clave($catalog, $clave)
```

#### Controlador (`Controller_Admin_Sat`)
Ubicación: `fuel/app/classes/controller/admin/sat.php`

Acciones disponibles:
```php
action_index()                     // Dashboard principal
action_productos($page)            // Lista de productos y servicios
action_unidades($page)             // Lista de unidades
action_uso_cfdi($page)             // Lista de usos de CFDI
action_formas_pago()               // Lista de formas de pago
action_metodos_pago()              // Lista de métodos de pago
action_tipos_comprobante()         // Lista de tipos de comprobante
action_regimenes_fiscales($page)   // Lista de regímenes fiscales
action_search()                    // API de búsqueda AJAX
action_get()                       // API obtener por clave
action_export($catalog)            // Exportar a CSV
```

#### Vistas
Ubicación: `fuel/app/views/admin/sat/`

Archivos de vistas:
- `index.php`: Dashboard con 7 tarjetas de catálogos
- `productos.php`: Lista de productos y servicios
- `unidades.php`: Lista de unidades de medida
- `uso_cfdi.php`: Lista de usos de CFDI
- `formas_pago.php`: Lista de formas de pago
- `metodos_pago.php`: Lista de métodos de pago
- `tipos_comprobante.php`: Lista de tipos de comprobante
- `regimenes_fiscales.php`: Lista de regímenes fiscales

### 4. Sistema de Permisos

El módulo utiliza el sistema de permisos por recurso:

- **Recurso**: `sat`
- **Acciones disponibles**:
  - `view`: Ver catálogos (requerido para acceder al módulo)
  - Exportación usa mismo permiso `view`

#### Configuración de Permisos

Para otorgar acceso a un usuario:
```sql
INSERT INTO permissions (user_id, resource, can_view, can_edit, can_delete, can_create, created_at, updated_at)
VALUES (1, 'sat', 1, 0, 0, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
```

### 5. Integración con Otros Módulos

Los catálogos SAT pueden ser utilizados por otros módulos del sistema:

#### Facturación Electrónica
- Uso de CFDI para facturas
- Forma y método de pago
- Tipo de comprobante (I, E, P)
- Productos y servicios para conceptos
- Unidades de medida para cantidades

#### Contabilidad
- Productos y servicios para clasificación contable
- Tipos de comprobante para registro de operaciones

#### Clientes/Proveedores
- Regímenes fiscales para configuración de empresas
- Uso de CFDI preferido del cliente

### 6. Mantenimiento y Actualización

#### Actualización de Catálogos

El SAT actualiza periódicamente los catálogos. Para actualizarlos:

1. **Obtener catálogos oficiales**: Descargar de http://omawww.sat.gob.mx/tramitesyservicios/Paginas/documentos/catCFDI.xls

2. **Importar nuevos registros**: Crear script SQL con INSERT ... ON DUPLICATE KEY UPDATE

3. **Ejecutar actualización**:
```bash
mysql -u root sajor < actualizacion_catalogos_sat.sql
```

#### Respaldo de Catálogos

Exportar catálogos actuales:
```bash
mysqldump -u root sajor sat_productos_servicios sat_unidades sat_uso_cfdi sat_formas_pago sat_metodos_pago sat_tipos_comprobante sat_regimenes_fiscales > backup_catalogos_sat.sql
```

### 7. Rutas del Módulo

| Ruta | Descripción |
|------|-------------|
| `/admin/sat` | Dashboard principal de catálogos |
| `/admin/sat/productos` | Lista de productos y servicios |
| `/admin/sat/productos/page/2` | Paginación de productos |
| `/admin/sat/unidades` | Lista de unidades de medida |
| `/admin/sat/uso_cfdi` | Lista de usos de CFDI |
| `/admin/sat/formas_pago` | Lista de formas de pago |
| `/admin/sat/metodos_pago` | Lista de métodos de pago |
| `/admin/sat/tipos_comprobante` | Lista de tipos de comprobante |
| `/admin/sat/regimenes_fiscales` | Lista de regímenes fiscales |
| `/admin/sat/export/{catalogo}` | Exportar catálogo a CSV |
| `/admin/sat/search` | API de búsqueda AJAX |
| `/admin/sat/get` | API obtener registro por clave |

### 8. Uso en Código

#### Ejemplo: Obtener productos para un selector

```php
// En un controlador
$productos = Model_SatCatalog::get_productos_servicios(
    array('active' => 1),
    100,  // límite
    0     // offset
);

// Pasar a la vista
$view->productos_sat = $productos;
```

#### Ejemplo: Buscar un producto específico

```php
// Buscar por clave
$producto = Model_SatCatalog::get_by_clave('productos', '50202300');

// Buscar por texto
$resultados = Model_SatCatalog::get_productos_servicios(
    array('search' => 'pan', 'active' => 1),
    10,
    0
);
```

#### Ejemplo: Obtener estadísticas

```php
$stats = Model_SatCatalog::get_statistics();
// Retorna:
// array(
//     'productos_activos' => 81,
//     'unidades_activas' => 10,
//     'usos_cfdi_activos' => 10,
//     ...
// )
```

### 9. Mejoras Futuras

Posibles mejoras para el módulo:

1. **Sincronización automática**: Descargar y actualizar catálogos desde el SAT automáticamente
2. **Búsqueda avanzada**: Búsqueda por múltiples criterios con operadores AND/OR
3. **Historial de cambios**: Registrar cambios en los catálogos del SAT
4. **API REST**: Exponer catálogos mediante API REST para integraciones externas
5. **Importación masiva**: Interface para importar catálogos desde archivos Excel/CSV
6. **Favoritos**: Marcar productos/servicios más utilizados para acceso rápido

## Conclusión

El módulo Catálogos SAT proporciona una base sólida para el cumplimiento fiscal en México, con acceso rápido y organizado a los catálogos oficiales del SAT. Su arquitectura modular permite fácil integración con otros módulos del sistema y facilita el mantenimiento y actualización de los datos fiscales.
