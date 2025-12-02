# ARQUITECTURA DEL SISTEMA MULTI-TENANT ERP

## 📋 VISIÓN GENERAL

Sistema ERP multi-tenant modular con gestión completa de:
- **Contabilidad** (catálogo de cuentas, pólizas, balanzas)
- **Facturación Electrónica** (CFDI 4.0, timbrado, certificados digitales)
- **Inventarios** (productos, almacenes, movimientos)
- **Ventas y CRM** (cotizaciones, pedidos, clientes)
- **Compras** (proveedores, órdenes de compra)
- **RRHH** (empleados, nómina, asistencias)
- **Reportes y Analytics** (dashboards configurables)

---

## 🏗️ MÓDULOS DEL SISTEMA (ACTUALIZADO)

### **CORE (No desactivables)**
1. ✅ **Dashboard** - Panel principal con métricas
2. ✅ **Usuarios** - Gestión de usuarios y permisos RBAC
3. ✅ **Configuración** - Settings generales del sistema
4. ✅ **Tenants** - Gestión multi-tenant

### **CONTABILIDAD (Business - Críticos)**
5. **Contabilidad** 🆕
   - Catálogo de cuentas (SAT)
   - Pólizas contables
   - Balanza de comprobación
   - Estado de resultados
   - Balance general
   - Libro diario/mayor
   - Conciliaciones bancarias

6. **Facturación Electrónica** 🆕
   - CFDI 4.0 (Facturas, Notas de crédito, Complemento de pago)
   - Timbrado con PAC (integración API)
   - Gestión de certificados digitales (.cer/.key)
   - Validación de RFC (SAT web service)
   - Cancelación de CFDI
   - Generación de PDFs
   - Envío por email
   - Almacenamiento de XMLs

7. **Finanzas**
   - Cuentas por cobrar
   - Cuentas por pagar
   - Flujo de efectivo
   - Bancos y conciliaciones
   - Proyecciones financieras

### **OPERACIONES (Business)**
8. **Inventarios**
   - Multi-almacén
   - Control de lotes y series
   - Entradas/salidas
   - Traspasos entre almacenes
   - Ajustes de inventario
   - Valuación (PEPS, Promedio, Último costo)
   - Kardex de productos

9. **Compras**
   - Proveedores (con validación RFC)
   - Requisiciones
   - Órdenes de compra
   - Recepción de mercancía
   - Devoluciones a proveedores
   - Integración con contabilidad

### **VENTAS Y CRM (Sales)**
10. **Ventas**
    - Punto de venta (POS)
    - Pedidos
    - Remisiones
    - Devoluciones
    - Integración con facturación

11. **CRM**
    - Clientes (con validación RFC)
    - Pipeline de ventas
    - Cotizaciones
    - Seguimiento de oportunidades
    - Historial de interacciones

12. **Cotizaciones**
    - Creación de cotizaciones
    - Conversión a pedido
    - Plantillas personalizables
    - Firma electrónica

### **ECOMMERCE (Sales)**
13. **E-commerce**
    - Tienda en línea
    - Catálogo de productos
    - Carrito de compras
    - Pasarela de pagos (Stripe, PayPal, Mercado Pago)
    - Integración con inventario y facturación

### **RRHH (Business)** 🆕
14. **Recursos Humanos**
    - Expedientes de empleados
    - Contratos y documentos
    - Vacaciones y permisos
    - Evaluaciones de desempeño

15. **Nómina** 🆕
    - Cálculo de nómina
    - CFDI de nómina 1.2
    - ISR, IMSS, infonavit
    - Recibos de nómina
    - Integración con contabilidad

### **MARKETING (Marketing)**
16. **Landing Pages**
    - Constructor de páginas
    - SEO optimizado
    - Formularios de contacto
    - A/B testing

17. **Email Marketing**
    - Campañas de email
    - Segmentación de clientes
    - Automatizaciones
    - Estadísticas de apertura/clicks

### **REPORTES Y ANÁLISIS (System)**
18. **Reportes**
    - Reportes predefinidos
    - Constructor de reportes personalizados
    - Exportación (Excel, PDF, CSV)
    - Programación de reportes automáticos

19. **Business Intelligence** 🆕
    - Dashboards configurables por rol
    - Gráficas interactivas (Chart.js)
    - KPIs personalizables
    - Alertas automáticas

### **DOCUMENTOS Y COMUNICACIÓN (System)**
20. **Gestión Documental** 🆕
    - Almacenamiento de archivos
    - Control de versiones
    - Permisos por documento
    - Búsqueda avanzada
    - Integración con firma electrónica

21. **Notificaciones**
    - Push notifications
    - Email
    - SMS (Twilio)
    - Webhooks

---

## 🔐 MÓDULO DE FACTURACIÓN ELECTRÓNICA (DETALLADO)

### **Componentes Principales**

#### 1. **Certificados Digitales**
Tabla: `tenant_sat_certificates`
```sql
- id
- tenant_id
- certificate_type (FIEL/CSD)
- cer_file (ruta encriptada)
- key_file (ruta encriptada)
- key_password (encriptado con AES-256)
- rfc
- razon_social
- valid_from
- valid_until
- is_active
- created_at
```

#### 2. **Configuración PAC (Proveedor Autorizado de Certificación)**
Tabla: `tenant_pac_config`
```sql
- id
- tenant_id
- pac_provider (finkok, diverza, etc.)
- pac_mode (test/production)
- pac_username
- pac_password (encriptado)
- pac_api_url
- is_active
```

#### 3. **CFDI (Comprobantes Fiscales)**
Tabla: `invoices_cfdi`
```sql
- id
- tenant_id
- invoice_id (FK a tabla ventas/facturas)
- cfdi_type (I=Ingreso, E=Egreso, P=Pago, etc.)
- serie
- folio
- uuid (Folio Fiscal)
- emisor_rfc
- emisor_nombre
- receptor_rfc
- receptor_nombre
- receptor_uso_cfdi
- fecha_emision
- fecha_timbrado
- metodo_pago
- forma_pago
- moneda
- tipo_cambio
- subtotal
- descuento
- impuestos_trasladados
- impuestos_retenidos
- total
- xml_original (TEXT)
- xml_timbrado (TEXT)
- pdf_path
- status (draft/sent_to_pac/timbrado/cancelado)
- pac_response (JSON)
- fecha_cancelacion
- motivo_cancelacion
- created_by
- created_at
```

#### 4. **Conceptos CFDI**
Tabla: `invoice_cfdi_concepts`
```sql
- id
- cfdi_id
- clave_prod_serv (SAT)
- clave_unidad (SAT)
- cantidad
- descripcion
- valor_unitario
- importe
- descuento
- objeto_imp
- impuestos (JSON)
```

### **Flujo de Facturación**

1. **Crear Factura** → Captura de datos del cliente y productos
2. **Validar RFC** → Validación en línea con SAT (opcional)
3. **Generar XML** → Construcción del XML según especificación CFDI 4.0
4. **Sellar XML** → Firma con certificado digital del emisor
5. **Enviar a PAC** → Timbrado con el PAC seleccionado
6. **Recibir UUID** → Guardar respuesta y UUID
7. **Generar PDF** → PDF con código QR y datos fiscales
8. **Registrar en Contabilidad** → Póliza automática
9. **Enviar por Email** → XML + PDF al cliente

### **Archivos del Módulo**

```
fuel/app/
├── classes/
│   ├── controller/admin/
│   │   ├── facturacion.php (CRUD facturas)
│   │   ├── certificados.php (Gestión certificados)
│   │   └── pac.php (Configuración PAC)
│   ├── helper/
│   │   ├── cfdi.php (Construcción XML CFDI 4.0)
│   │   ├── sat.php (Validaciones SAT, catálogos)
│   │   ├── pac.php (Integración con PACs)
│   │   └── certificado.php (Manejo de .cer/.key)
│   └── model/
│       ├── cfdi.php
│       └── certificate.php
├── migrations/modules/
│   └── facturacion.sql
└── views/admin/facturacion/
    ├── index.php (lista facturas)
    ├── create.php (crear factura)
    ├── certificate.php (subir certificados)
    └── pac_config.php (config PAC)
```

---

## 💾 MÓDULO DE CONTABILIDAD (DETALLADO)

### **Catálogo de Cuentas SAT**

Tabla: `accounting_accounts`
```sql
- id
- tenant_id
- account_code (VARCHAR(20)) -- ej: 1.1.01.001
- account_name
- account_type (Activo/Pasivo/Capital/Ingreso/Egreso)
- parent_id (jerarquía)
- level (1-5)
- is_summary (si tiene subcuentas)
- sat_code (clave agrupador SAT)
- nature (Deudora/Acreedora)
- is_active
```

### **Pólizas Contables**

Tabla: `accounting_journals`
```sql
- id
- tenant_id
- journal_type (Ingreso/Egreso/Diario)
- journal_number
- date
- description
- reference_type (factura/compra/nomina/etc)
- reference_id
- total_debit
- total_credit
- status (draft/posted/cancelled)
- posted_by
- posted_at
```

Tabla: `accounting_journal_entries`
```sql
- id
- journal_id
- account_id
- debit
- credit
- description
- cost_center (opcional)
```

---

## 🎨 SISTEMA DE TEMPLATES

### **Templates Disponibles**

1. **CoreUI** (Actual) ✅
   - Moderno y minimalista
   - Bootstrap 5
   - Font Awesome icons

2. **AdminLTE** (Por crear)
   - Clásico y robusto
   - Muchas opciones de personalización
   - Compatible con plugins jQuery

3. **Argon Dashboard** (Por crear)
   - Diseño elegante con degradados
   - Tarjetas con sombras
   - Animaciones suaves

### **Implementación**

Cada template debe tener:
- `fuel/app/views/admin/template_{nombre}.php`
- Misma estructura de datos (`$data`)
- Estilos en `public/assets/css/{template}/`
- Scripts en `public/assets/js/{template}/`

---

## 📊 DASHBOARD CONFIGURABLE

### **Widgets Disponibles**

1. **Ventas del Día**
   - Total ventas
   - Comparación con ayer
   - Gráfica de barras

2. **Facturas Pendientes**
   - Lista de facturas sin timbrar
   - Alertas de certificados próximos a vencer

3. **Inventario Crítico**
   - Productos con stock mínimo
   - Alertas de reorden

4. **Cuentas por Cobrar**
   - Total adeudado
   - Facturas vencidas
   - Próximos vencimientos

5. **Flujo de Efectivo**
   - Ingresos vs egresos (últimos 30 días)
   - Gráfica de líneas

6. **Top 10 Productos**
   - Más vendidos del mes
   - Gráfica de dona

7. **Actividad Reciente**
   - Últimas acciones del sistema
   - Timeline

### **Configuración por Usuario**

Tabla: `user_dashboard_widgets` (ya contemplada en user_preferences.dashboard_widgets JSON)

JSON structure:
```json
{
  "widgets": [
    {"id": "sales_today", "position": 1, "size": "col-md-3"},
    {"id": "pending_invoices", "position": 2, "size": "col-md-3"},
    {"id": "critical_inventory", "position": 3, "size": "col-md-6"}
  ],
  "refresh_interval": 60
}
```

---

## 🔒 SEGURIDAD Y ENCRIPTACIÓN

### **Datos Sensibles Encriptados**

- Certificados digitales (.cer/.key)
- Contraseñas de certificados
- Credenciales de PAC
- Tokens de API
- Datos bancarios

### **Método de Encriptación**

```php
// Helper_Encryption
class Helper_Encryption
{
    // AES-256-CBC con IV único por registro
    public static function encrypt($data, $key = null)
    public static function decrypt($data, $key = null)
    
    // Para archivos (certificados)
    public static function encrypt_file($source, $dest, $key = null)
    public static function decrypt_file($source, $dest, $key = null)
}
```

---

## 📁 ESTRUCTURA DE ARCHIVOS

```
fuel/app/
├── classes/
│   ├── controller/admin/
│   │   ├── facturacion.php 🆕
│   │   ├── certificados.php 🆕
│   │   ├── contabilidad.php 🆕
│   │   ├── polizas.php 🆕
│   │   ├── nomina.php 🆕
│   │   └── ...
│   ├── helper/
│   │   ├── cfdi.php 🆕
│   │   ├── sat.php 🆕
│   │   ├── pac.php 🆕
│   │   ├── certificado.php 🆕
│   │   ├── encryption.php 🆕
│   │   ├── accounting.php 🆕
│   │   └── ...
│   └── model/
│       ├── cfdi.php 🆕
│       ├── certificate.php 🆕
│       ├── journal.php 🆕
│       └── ...
├── migrations/modules/
│   ├── facturacion.sql 🆕
│   ├── contabilidad.sql 🆕
│   ├── nomina.sql 🆕
│   └── ...
└── views/admin/
    ├── facturacion/ 🆕
    ├── contabilidad/ 🆕
    ├── certificados/ 🆕
    └── ...
```

---

## 🚀 PLAN DE IMPLEMENTACIÓN

### **Fase 1: Fundamentos (ACTUAL)** ✅
- [x] Sistema de módulos
- [x] RBAC (permisos)
- [x] Multi-tenant
- [x] Configuración general
- [x] Template CoreUI

### **Fase 2: Templates y Dashboard** ⏳ SIGUIENTE
- [ ] Template AdminLTE
- [ ] Template Argon
- [ ] Dashboard configurable con Chart.js
- [ ] Widgets drag-and-drop

### **Fase 3: Facturación Electrónica** 🔜
- [ ] Gestión de certificados digitales
- [ ] Integración con PAC
- [ ] Generación CFDI 4.0
- [ ] Timbrado y cancelación
- [ ] PDF con código QR

### **Fase 4: Contabilidad** 🔜
- [ ] Catálogo de cuentas SAT
- [ ] Pólizas contables
- [ ] Reportes contables
- [ ] Balanza de comprobación
- [ ] Estados financieros

### **Fase 5: Inventarios y Compras** 🔜
- [ ] Multi-almacén
- [ ] Movimientos de inventario
- [ ] Proveedores y órdenes de compra
- [ ] Integración con contabilidad

### **Fase 6: Nómina** 🔜
- [ ] Empleados y contratos
- [ ] Cálculo de nómina
- [ ] CFDI de nómina
- [ ] Integración con contabilidad

### **Fase 7: Optimizaciones** 🔜
- [ ] Caché de consultas
- [ ] Cola de trabajos (jobs)
- [ ] API REST
- [ ] Webhooks
- [ ] Documentación completa

---

## 💡 RECOMENDACIONES TÉCNICAS

1. **Certificados Digitales**: Almacenar en directorio fuera de `public/` y encriptar
2. **PAC**: Crear adaptadores para múltiples proveedores (Finkok, Diverza, SW)
3. **CFDI 4.0**: Usar librería XML validada contra XSD oficial del SAT
4. **Catálogos SAT**: Importar y actualizar desde web services del SAT
5. **Dashboards**: Usar Vue.js o Alpine.js para reactividad
6. **Reportes**: Implementar sistema de cola para reportes pesados
7. **Backups**: Automatizar respaldos diarios de DB y archivos críticos

---

## 📝 NOTAS IMPORTANTES

- Todos los módulos de facturación deben validar datos contra catálogos SAT actualizados
- Implementar logging exhaustivo en operaciones fiscales (auditoría)
- Los certificados vencidos deben alertarse con 30 días de anticipación
- Las pólizas contables deben ser inmutables una vez publicadas
- Considerar API para integraciones con tiendas en línea externas
