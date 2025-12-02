# Migraciones del Sistema ERP Multi-Tenant

Este directorio contiene las migraciones SQL que crean toda la estructura de la base de datos del sistema ERP con arquitectura multi-tenant.

## 📋 Lista de Migraciones

### 001_auth_tables.sql (10 tablas)
Tablas de Autenticación y Permisos

### 002_business_entities.sql (15 tablas)  
Entidades de Negocio Básicas

### 003_extended_modules.sql (156 tablas)
Módulos Extendidos del Sistema - Incluye todos los módulos:
- Contabilidad y Finanzas
- Proveedores (extendido)
- Clientes (extendido)
- Productos (extendido)
- Ventas y Cotizaciones
- Socios de Negocio
- Empleados
- Actividades y Tareas
- Tickets y Soporte
- Plataformas E-commerce (ML, Amazon, Shopify, etc.)
- Contenido y Marketing
- Legal y Documentos
- Notificaciones
- Facturación Electrónica (SAT)
- Reportes

**TOTAL: ~181 tablas**

## 🚀 Instalación

1. Accede a: `http://localhost/base/install`
2. Configura la base de datos
3. Ejecuta las migraciones (automático)
4. Crea el usuario administrador

¡Listo! El sistema estará completamente instalado.
