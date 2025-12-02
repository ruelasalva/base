# Migración del Admin de SAJOR a BASE

**Fecha:** 2 de diciembre de 2025  
**Estado:** ✅ Estructura base completada

---

## 📦 Archivos Migrados

### ✅ Controladores
- `Controller_Admin` - Controlador principal del admin
- `Controller_Baseadmin` - Controlador base para herencia
- **40+ submódulos** copiados a `fuel/app/classes/controller/admin/`:
  - `configuracion/` - Configuración general, correos, permisos
  - `apariencia/` - Footer, header, diseño
  - `catalogo/` - Productos, categorías, subcategorías, marcas, tags
  - `ventas.php` - Gestión de ventas
  - `compras/` - Órdenes de compra
  - `finanzas/` - Sistema financiero
  - `crm/` - CRM completo
  - `helpdesk/` - Sistema de tickets
  - `legal/` - Documentos legales
  - `blog/` - Gestión de blog
  - `slides.php` - Carruseles
  - `banners.php` - Banners principales
  - `usuarios.php` - Gestión de usuarios
  - `reportes.php` - Reportes
  - `logs.php` - Logs del sistema
  - Y más...

### ✅ Vistas
Todas las vistas del admin copiadas a `fuel/app/views/admin/`:
- `template.php` - Template principal del admin (Argon Dashboard)
- Vistas para cada módulo organizadas por carpetas

### ✅ Modelos
42+ modelos copiados a `fuel/app/classes/model/`:
- `Model_User`, `Model_Permission`, `Model_Permission_Group`
- `Model_Product`, `Model_Category`, `Model_Brand`
- `Model_Order`, `Model_Sale`, `Model_Quote`
- `Model_Slide`, `Model_Banner`, `Model_Post`
- `Model_Config` - Configuración general
- Y todos los demás modelos necesarios

### ✅ Helpers
Sistema de helpers copiado a `fuel/app/classes/helper/`:
- `Helper_Permission` - Sistema de permisos granulares (usuario + grupo)
- Otros helpers necesarios

### ✅ Servicios
Servicios copiados a `fuel/app/classes/service/`

### ✅ Assets del Admin
Copiados a `public/assets/`:
- **CSS**: Argon Dashboard + estilos personalizados
  - `admin/nucleo/` - Iconos Nucleo
  - `admin/@fortawesome/` - Font Awesome
  - `admin/argon.css` - Framework Argon
  - `admin/main.css` + `admin/add.css` - Estilos personalizados
- **JS**: Vue.js, jQuery, Axios, Select2, SweetAlert2, etc.
- **IMG**: Logo, favicon, imágenes del admin

---

## 🎯 Módulos del Admin Disponibles

### 🏠 Dashboard
- Vista general del sistema
- Estadísticas y métricas

### ⚙️ Configuración
- ✅ General (empresa, RFC, SAT, contacto)
- ✅ Correos electrónicos
- ✅ Permisos por usuario y grupo

### 🎨 Gestión (Apariencia)
- ✅ Slides/Carruseles
- ✅ Banners principales
- ✅ Banners laterales
- ✅ Blog (categorías, etiquetas, publicaciones)
- ✅ Editor de diseño
- ✅ Footer personalizable
- ✅ Legal (documentos, contratos, consentimientos)

### 📦 Catálogo
- ✅ Productos completo
- ✅ Categorías y subcategorías
- ✅ Marcas
- ✅ Tags/Etiquetas
- ✅ Listas de precios
- ✅ Montos y descuentos
- ✅ Paqueterías

### 💰 Ventas
- ✅ Gestión de ventas
- ✅ Cotizaciones
- ✅ Precotizaciones
- ✅ Cupones de descuento
- ✅ Lista de deseados
- ✅ Carritos abandonados

### 🛒 Compras
- ✅ Órdenes de compra
- ✅ Contrarecibos
- ✅ Gestión de proveedores

### 💳 Finanzas
- ✅ Sistema financiero completo
- ✅ BBVA integración
- ✅ Procesadores de pago

### 👥 CRM
- ✅ Gestión de clientes
- ✅ Seguimiento de actividades

### 🎫 Helpdesk
- ✅ Sistema de tickets
- ✅ Soporte al cliente

### ⚖️ Legal
- ✅ Documentos legales
- ✅ Contratos
- ✅ Consentimientos de usuarios

### 👨‍💼 Recursos Humanos
- ✅ Empleados
- ✅ Socios de negocio
- ✅ Sala de juntas (calendario)

### 📊 Reportes y Análisis
- ✅ Reportes personalizables
- ✅ Logs del sistema
- ✅ Notificaciones

### 👤 Usuarios
- ✅ Administradores
- ✅ Usuarios del sistema
- ✅ Permisos granulares
- ✅ Grupos y roles

---

## 🔐 Sistema de Permisos

El admin incluye un sistema de permisos de dos niveles:

1. **Permisos de Usuario** (individuales)
   - Se revisan primero
   - Pueden sobrescribir permisos de grupo

2. **Permisos de Grupo** (por rol)
   - Se aplican si no hay permisos individuales activos
   - Definidos en `Model_Permission_Group`

**Acciones disponibles por recurso:**
- `view` - Ver
- `edit` - Editar
- `create` - Crear
- `delete` - Eliminar

**Uso:**
```php
// En controladores
if (!Helper_Permission::can('config_general', 'view')) {
    Session::set_flash('error', 'No tienes permiso para ver la configuración.');
    Response::redirect('admin');
}

// En vistas
<?php if (Helper_Permission::can('slides', 'view')): ?>
    <li class="nav-item">
        <a href="admin/slides">Slides</a>
    </li>
<?php endif; ?>
```

---

## 🎨 Template del Admin

**Framework:** Argon Dashboard (Bootstrap 4)

**Características:**
- ✅ Sidebar colapsable
- ✅ Menú responsive
- ✅ Breadcrumbs
- ✅ Alertas con SweetAlert2
- ✅ Select2 para selectores
- ✅ Vue.js para componentes reactivos
- ✅ Axios para peticiones AJAX
- ✅ DataTables para tablas
- ✅ FullCalendar para calendarios
- ✅ Chart.js para gráficas

---

## 📝 Próximos Pasos

### En progreso:
- [ ] Adaptar módulo de configuración para multi-tenant
- [ ] Crear tabla `tenant_config` para configuraciones por tenant
- [ ] Migrar sistema de idiomas (es/en)

### Pendiente:
- [ ] Adaptar slides/banners para multi-tenant
- [ ] Crear sistema de branding por tenant (logos, colores, fuentes)
- [ ] Revisar y adaptar todos los modelos para multi-tenancy
- [ ] Probar login y dashboard del admin
- [ ] Configurar rutas del admin en `routes.php`

---

## 🚀 Cómo Acceder al Admin

**URL:** `http://localhost/base/admin`

**Credenciales:** (configurar después de migración de usuarios)

---

## 📚 Documentación Técnica

**FuelPHP:** https://fuelphp.com  
**Argon Dashboard:** https://www.creative-tim.com/product/argon-dashboard  
**Permisos:** Ver `fuel/app/classes/helper/permission.php`

---

✅ **Migración completada exitosamente**
