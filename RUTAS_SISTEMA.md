# Rutas del Sistema Multi-Tenant ERP

Documento generado: 2 de diciembre de 2025

## Resumen

Este documento lista todas las rutas configuradas en el sistema para los módulos tenant y funcionalidades principales.

---

## 🏠 Rutas Principales

| Ruta | Descripción | Controlador |
|------|-------------|-------------|
| `/` | Página principal | `main/index` |
| `/diagnostico` | Diagnóstico del sistema | `diagnostico/index` |

---

## 🔧 Instalador

| Ruta | Descripción | Controlador | Método |
|------|-------------|-------------|--------|
| `/install` | Página principal del instalador | `install/index` | GET |
| `/install/configurar` | Configurar base de datos | `install/configurar` | GET/POST |
| `/install/ejecutar` | Ejecutar migraciones manualmente | `install/ejecutar` | GET/POST |
| `/install/auto_install` | Instalación automática | `install/auto_install` | GET/POST |
| `/install/crear_admin` | Crear usuario administrador | `install/crear_admin` | GET/POST |
| `/install/completado` | Instalación completada | `install/completado` | GET |
| `/install/verificar_db` | Verificar conexión BD (AJAX) | `install/verificar_db` | POST |

---

## 🔐 Autenticación

| Ruta | Descripción | Controlador |
|------|-------------|-------------|
| `/auth/login` | Iniciar sesión | `auth/login` |
| `/auth/logout` | Cerrar sesión | `auth/logout` |
| `/auth/register` | Registrar usuario | `auth/register` |
| `/auth/forgot` | Recuperar contraseña | `auth/forgot` |
| `/auth/reset/:token` | Reset contraseña con token | `auth/reset/$1` |

---

## 👤 Módulo Admin (Administración)

**Namespace:** `Admin\Controller_*`

### Panel y Configuración

| Ruta | Descripción | Controlador |
|------|-------------|-------------|
| `/admin` | Dashboard principal | `admin/dashboard/index` |
| `/admin/dashboard` | Dashboard | `admin/dashboard/index` |
| `/admin/settings` | Configuraciones | `admin/settings/index` |
| `/admin/reports` | Reportes | `admin/reports/index` |
| `/admin/logs` | Logs del sistema | `admin/logs/index` |

### Gestión de Usuarios

| Ruta | Descripción | Controlador |
|------|-------------|-------------|
| `/admin/users` | Listado de usuarios | `admin/users/index` |
| `/admin/users/add` | Agregar usuario | `admin/users/add` |
| `/admin/users/edit/:id` | Editar usuario | `admin/users/edit/$1` |
| `/admin/users/delete/:id` | Eliminar usuario | `admin/users/delete/$1` |

**Traducciones disponibles:**
- `fuel/app/lang/es/admin.php`
- `fuel/app/lang/en/admin.php`

---

## 🤝 Módulo Partners (Socios)

**Namespace:** `Partners\Controller_*`

| Ruta | Descripción | Controlador |
|------|-------------|-------------|
| `/partners` | Dashboard de socios | `partners/dashboard/index` |
| `/partners/dashboard` | Dashboard | `partners/dashboard/index` |
| `/partners/list` | Listado de socios | `partners/partners/index` |
| `/partners/add` | Agregar socio | `partners/partners/add` |
| `/partners/edit/:id` | Editar socio | `partners/partners/edit/$1` |
| `/partners/view/:id` | Ver detalle socio | `partners/partners/view/$1` |
| `/partners/contracts` | Contratos | `partners/contracts/index` |
| `/partners/commissions` | Comisiones | `partners/commissions/index` |

**Traducciones disponibles:**
- `fuel/app/lang/es/partners.php`
- `fuel/app/lang/en/partners.php`

**Campos de traducción:**
- `partners.menu.*` - Menú del módulo
- `partners.fields.*` - Campos del formulario
- `partners.messages.*` - Mensajes de éxito/error

---

## 📦 Módulo Providers (Proveedores)

**Namespace:** `Providers\Controller_*`

| Ruta | Descripción | Controlador |
|------|-------------|-------------|
| `/providers` | Dashboard de proveedores | `providers/dashboard/index` |
| `/providers/dashboard` | Dashboard | `providers/dashboard/index` |
| `/providers/list` | Listado de proveedores | `providers/providers/index` |
| `/providers/add` | Agregar proveedor | `providers/providers/add` |
| `/providers/edit/:id` | Editar proveedor | `providers/providers/edit/$1` |
| `/providers/view/:id` | Ver detalle proveedor | `providers/providers/view/$1` |
| `/providers/orders` | Órdenes de compra | `providers/orders/index` |
| `/providers/invoices` | Facturas | `providers/invoices/index` |
| `/providers/payments` | Pagos | `providers/payments/index` |

**Traducciones disponibles:**
- `fuel/app/lang/es/providers.php`
- `fuel/app/lang/en/providers.php`

---

## 💼 Módulo Sellers (Vendedores)

**Namespace:** `Sellers\Controller_*`

| Ruta | Descripción | Controlador |
|------|-------------|-------------|
| `/sellers` | Dashboard de vendedores | `sellers/dashboard/index` |
| `/sellers/dashboard` | Dashboard | `sellers/dashboard/index` |
| `/sellers/list` | Listado de vendedores | `sellers/sellers/index` |
| `/sellers/add` | Agregar vendedor | `sellers/sellers/add` |
| `/sellers/edit/:id` | Editar vendedor | `sellers/sellers/edit/$1` |
| `/sellers/view/:id` | Ver detalle vendedor | `sellers/sellers/view/$1` |
| `/sellers/sales` | Ventas | `sellers/sales/index` |
| `/sellers/commissions` | Comisiones | `sellers/commissions/index` |
| `/sellers/goals` | Metas | `sellers/goals/index` |
| `/sellers/reports` | Reportes | `sellers/reports/index` |

**Traducciones disponibles:**
- `fuel/app/lang/es/sellers.php`
- `fuel/app/lang/en/sellers.php`

**Estadísticas disponibles:**
- `sellers.stats.total_sales` - Ventas totales
- `sellers.stats.commission_earned` - Comisión ganada
- `sellers.stats.goal_progress` - Progreso de meta
- `sellers.stats.active_sellers` - Vendedores activos

---

## 👥 Módulo Clients (Clientes)

**Namespace:** `Clients\Controller_*`

| Ruta | Descripción | Controlador |
|------|-------------|-------------|
| `/clients` | Dashboard de clientes | `clients/dashboard/index` |
| `/clients/dashboard` | Dashboard | `clients/dashboard/index` |
| `/clients/list` | Listado de clientes | `clients/clients/index` |
| `/clients/add` | Agregar cliente | `clients/clients/add` |
| `/clients/edit/:id` | Editar cliente | `clients/clients/edit/$1` |
| `/clients/view/:id` | Ver detalle cliente | `clients/clients/view/$1` |
| `/clients/orders` | Pedidos | `clients/orders/index` |
| `/clients/invoices` | Facturas | `clients/invoices/index` |
| `/clients/payments` | Pagos | `clients/payments/index` |
| `/clients/history/:id` | Historial del cliente | `clients/history/index/$1` |

**Traducciones disponibles:**
- `fuel/app/lang/es/clients.php`
- `fuel/app/lang/en/clients.php`

**Estadísticas disponibles:**
- `clients.stats.total_clients` - Clientes totales
- `clients.stats.active_clients` - Clientes activos
- `clients.stats.new_this_month` - Nuevos este mes
- `clients.stats.total_revenue` - Ingresos totales

---

## 🛒 Módulo Store (Tienda)

**Namespace:** `Store\Controller_*`

### Productos y Categorías

| Ruta | Descripción | Controlador |
|------|-------------|-------------|
| `/store` | Catálogo de productos | `store/products/index` |
| `/store/products` | Listado de productos | `store/products/index` |
| `/store/product/:id` | Detalle de producto | `store/products/view/$1` |
| `/store/categories` | Categorías | `store/categories/index` |
| `/store/category/:id` | Ver categoría | `store/categories/view/$1` |

### Carrito y Compras

| Ruta | Descripción | Controlador |
|------|-------------|-------------|
| `/store/cart` | Ver carrito | `store/cart/index` |
| `/store/cart/add/:id` | Agregar al carrito | `store/cart/add/$1` |
| `/store/cart/remove/:id` | Eliminar del carrito | `store/cart/remove/$1` |
| `/store/checkout` | Finalizar compra | `store/checkout/index` |

### Pedidos

| Ruta | Descripción | Controlador |
|------|-------------|-------------|
| `/store/orders` | Mis pedidos | `store/orders/index` |
| `/store/order/:id` | Ver pedido | `store/orders/view/$1` |

**Traducciones disponibles:**
- `fuel/app/lang/es/store.php`
- `fuel/app/lang/en/store.php`

**Secciones especiales:**
- `store.cart.*` - Traducciones del carrito
- `store.stats.*` - Estadísticas de la tienda

---

## 🌐 Módulo Landing (Página de Aterrizaje)

**Namespace:** `Landing\Controller_*`

| Ruta | Descripción | Controlador |
|------|-------------|-------------|
| `/landing` | Página de inicio | `landing/home/index` |
| `/landing/home` | Inicio | `landing/home/index` |
| `/landing/about` | Acerca de | `landing/about/index` |
| `/landing/services` | Servicios | `landing/services/index` |
| `/landing/contact` | Contacto | `landing/contact/index` |
| `/landing/contact/send` | Enviar mensaje (POST) | `landing/contact/send` |
| `/landing/blog` | Blog | `landing/blog/index` |
| `/landing/blog/:slug` | Artículo del blog | `landing/blog/view/$1` |

**Traducciones disponibles:**
- `fuel/app/lang/es/landing.php`
- `fuel/app/lang/en/landing.php`

**Secciones especiales:**
- `landing.hero.*` - Sección hero/banner
- `landing.features.*` - Características
- `landing.contact.*` - Formulario de contacto

---

## ❌ Rutas de Error

| Ruta | Descripción | Controlador |
|------|-------------|-------------|
| `/error/403` | Sin permisos | `error/403` |
| `/error/404` | No encontrado | `error/404` |
| `/error/500` | Error del servidor | `error/500` |
| `_404_` | Ruta por defecto 404 | `welcome/404` |

---

## 🌍 Sistema de Traducciones

### Archivos de Idioma Disponibles

Todos los módulos cuentan con traducciones en **Español** e **Inglés**:

```
fuel/app/lang/
├── es/
│   ├── common.php      # Traducciones comunes (200+ keys)
│   ├── admin.php       # Módulo admin
│   ├── partners.php    # Módulo partners
│   ├── providers.php   # Módulo providers
│   ├── sellers.php     # Módulo sellers
│   ├── clients.php     # Módulo clients
│   ├── store.php       # Módulo store
│   └── landing.php     # Módulo landing
└── en/
    ├── common.php
    ├── admin.php
    ├── partners.php
    ├── providers.php
    ├── sellers.php
    ├── clients.php
    ├── store.php
    └── landing.php
```

### Funciones Helper

```php
// Traducir una clave
echo __('common.actions.save');
echo __('admin.menu.dashboard');
echo __('sellers.stats.total_sales');

// Traducir y mostrar directamente
_e('common.messages.success');

// Cambiar idioma
set_language('es'); // o 'en'

// Formatear fecha según idioma
echo format_date($date); // 2 de diciembre de 2025

// Pluralizar
echo pluralize(5, 'producto', 'productos'); // 5 productos
```

### Auto-carga de Traducciones

El archivo `fuel/app/config/config.php` auto-carga todos los archivos:

```php
'language' => array(
    'common',    // Traducciones comunes
    'admin',     // Módulo admin
    'partners',  // Módulo partners
    'providers', // Módulo providers
    'sellers',   // Módulo sellers
    'clients',   // Módulo clients
    'store',     // Módulo store
    'landing',   // Módulo landing
),
```

---

## 📁 Estructura de Módulos Tenant

Todos los módulos están en: `fuel/packages_tenant/`

```
fuel/packages_tenant/
├── admin/
│   ├── bootstrap.php
│   ├── classes/
│   ├── config/
│   └── views/
├── partners/
│   └── bootstrap.php
├── providers/
│   └── bootstrap.php
├── sellers/
│   └── bootstrap.php
├── clients/
│   └── bootstrap.php
├── store/
│   └── bootstrap.php
├── landing/
│   └── bootstrap.php
└── example_module/
    └── bootstrap.php
```

Cada módulo tiene su propio `bootstrap.php` que:
1. Verifica si el módulo está activo
2. Carga el package con `Package::load()`
3. Registra namespace: `Module\Controller_*`
4. Registra rutas prepend (prioridad)
5. Agrega paths de views

---

## 🔄 Carga de Módulos

### Modo DEVELOPMENT

En modo desarrollo (`FUEL_ENV = 'development'`), todos los módulos se cargan automáticamente:

```php
// fuel/app/config/config_tenant.php
if (in_array(Fuel::$env, array('development', 'staging'))) {
    // Auto-cargar todos los módulos en desarrollo
    define('TENANT_ACTIVE_MODULES', 'admin,partners,providers,sellers,clients,store,landing,example_module');
}
```

### Modo PRODUCTION

En producción, solo se cargan los módulos activos del tenant según la base de datos.

---

## 🎯 Próximos Pasos

1. **Crear Controladores**: Implementar los controladores para cada módulo
2. **Crear Vistas**: Diseñar las interfaces de usuario
3. **Implementar Lógica**: Agregar funcionalidad CRUD
4. **Testing**: Probar todas las rutas y funcionalidades
5. **Migraciones**: Crear tablas necesarias para cada módulo

---

## 📞 Contacto y Soporte

Para más información sobre el sistema, consulte:
- `README.md` - Información general del proyecto
- `CHANGELOG.md` - Historial de cambios
- `CONTRIBUTING.md` - Guía de contribución
- `fuel/app/lang/README.md` - Documentación del sistema de idiomas

---

**Última actualización:** 2 de diciembre de 2025
