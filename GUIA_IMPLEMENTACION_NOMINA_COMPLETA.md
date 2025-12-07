# Guía de Instalación y Configuración - Módulo de Nómina Completo

## 📋 Contenido

1. [Resumen de Implementación](#resumen-de-implementación)
2. [Requisitos del Sistema](#requisitos-del-sistema)
3. [Instalación de Dependencias](#instalación-de-dependencias)
4. [Configuración](#configuración)
5. [Archivos Creados](#archivos-creados)
6. [Acciones del Controlador](#acciones-del-controlador)
7. [Integración CFDI](#integración-cfdi)
8. [Sistema de Notificaciones](#sistema-de-notificaciones)
9. [Uso y Ejemplos](#uso-y-ejemplos)
10. [Troubleshooting](#troubleshooting)

---

## 🎯 Resumen de Implementación

Se han completado todas las mejoras solicitadas para el módulo de nómina:

### ✅ Vistas Completadas
1. **edit.php** - Edición de períodos con validaciones
2. **calculate.php** - Preview antes de calcular con estadísticas
3. **approve.php** - Aprobación con resumen detallado
4. **Vistas existentes mejoradas** (index, create, view)

### ✅ Funcionalidades Implementadas
1. **Exportación a Excel** (PHPSpreadsheet)
2. **Generación de PDF** (TCPDF)
3. **Timbrado CFDI** (Finkok, SW Sapien, Ecodex)
4. **Notificaciones Multi-canal** (Email, SMS, WhatsApp, Push)

---

## 💻 Requisitos del Sistema

### Requisitos Básicos
- PHP 7.4 o superior
- FuelPHP 1.8.2
- MariaDB/MySQL 10.x
- Composer
- OpenSSL (para CFDI)

### Extensiones PHP Requeridas
```bash
php -m | grep -E 'curl|openssl|mbstring|xml|zip|gd'
```

Deben estar habilitadas:
- curl
- openssl
- mbstring
- xml
- zip
- gd (para PDFs)
- soap (para PACs)

---

## 📦 Instalación de Dependencias

### 1. Instalar Librerías de PHP

```bash
cd C:\xampp\htdocs\base

# PHPSpreadsheet (Exportación Excel)
composer require phpoffice/phpspreadsheet

# TCPDF (Generación de PDFs)
composer require tecnickcom/tcpdf

# Twilio SDK (SMS y WhatsApp) - Opcional
composer require twilio/sdk

# Firebase Cloud Messaging - Opcional
composer require google/cloud-firestore
```

### 2. Verificar Instalación

```bash
composer show phpoffice/phpspreadsheet
composer show tecnickcom/tcpdf
```

---

## ⚙️ Configuración

### 1. Configuración de Email

Editar: `fuel/app/config/production/email.php` o `fuel/app/config/email.php`

```php
<?php
return array(
    'driver' => 'smtp', // smtp, mail, sendmail
    'smtp' => array(
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'username' => 'tu-email@empresa.com',
        'password' => 'tu-password',
        'timeout' => 5,
        'encryption' => 'tls', // tls o ssl
    ),
);
```

### 2. Configuración de Notificaciones

Crear: `fuel/app/config/notifications.php`

```php
<?php
return array(
    // Email
    'from_email' => 'noreply@tuempresa.com',
    'from_name' => 'Sistema de Nómina',
    'attach_pdf' => true, // Adjuntar PDF en emails

    // SMS - Twilio
    'sms_provider' => 'twilio', // twilio, nexmo
    'twilio' => array(
        'account_sid' => 'tu_account_sid',
        'auth_token' => 'tu_auth_token',
        'from_number' => '+1234567890',
        
        // WhatsApp (opcional)
        'whatsapp_from' => 'whatsapp:+14155238886',
    ),

    // SMS - Nexmo/Vonage (alternativa)
    'nexmo' => array(
        'api_key' => 'tu_api_key',
        'api_secret' => 'tu_api_secret',
        'from_number' => 'NOMINA',
    ),

    // Push Notifications - Firebase
    'push_provider' => 'fcm',
    'fcm' => array(
        'server_key' => 'tu_server_key',
    ),

    // WhatsApp Business API
    'whatsapp_provider' => 'twilio', // twilio, 360dialog
);
```

### 3. Configuración de CFDI/PAC

Crear: `fuel/app/config/cfdi.php`

```php
<?php
return array(
    // Proveedor PAC
    'pac_provider' => 'finkok', // finkok, sw, ecodex

    // Configuración Finkok
    'pac_config' => array(
        'finkok' => array(
            'username' => 'tu_usuario_finkok',
            'password' => 'tu_password_finkok',
            'wsdl' => 'https://facturacion.finkok.com/servicios/soap/stamp.wsdl',
            'environment' => 'production', // production, testing
        ),

        // Configuración SW Sapien
        'sw' => array(
            'token' => 'tu_token_sw',
            'url' => 'https://api.sw.com.mx',
        ),

        // Configuración Ecodex
        'ecodex' => array(
            'username' => 'tu_usuario',
            'password' => 'tu_password',
            'url' => 'https://cfdi.ecodex.com.mx',
        ),
    ),
);
```

### 4. Configuración de Rutas (Opcional)

Agregar a: `fuel/app/config/routes.php`

```php
// Rutas de nómina
'admin/nomina/preview-calculate/(:num)' => 'admin/nomina/preview_calculate/$1',
'admin/nomina/preview-approve/(:num)' => 'admin/nomina/preview_approve/$1',
'admin/nomina/export-excel/(:num)' => 'admin/nomina/export_excel/$1',
'admin/nomina/generate-pdf/(:num)' => 'admin/nomina/generate_pdf/$1',
'admin/nomina/stamp-cfdi/(:num)' => 'admin/nomina/stamp_cfdi/$1',
'admin/nomina/notify/(:num)' => 'admin/nomina/notify_receipt/$1',
```

---

## 📁 Archivos Creados

### Vistas (Views)
```
fuel/app/views/admin/nomina/
├── index.php                 ✅ Ya existía (actualizada)
├── create.php                ✅ Ya existía
├── view.php                  ✅ Ya existía
├── edit.php                  🆕 Nueva (edición con validaciones)
├── calculate.php             🆕 Nueva (preview de cálculo)
└── approve.php               🆕 Nueva (aprobación detallada)

fuel/app/views/emails/
└── payroll_receipt.php       🆕 Nueva (plantilla email HTML)
```

### Clases (Classes)
```
fuel/app/classes/
├── controller/admin/
│   ├── nomina.php                    ✅ Ya existía
│   └── nomina_extensions.php         🆕 Nueva (extensiones)
├── cfdi/
│   └── payrollstamper.php            🆕 Nueva (timbrado CFDI)
└── payroll/
    └── notifier.php                  🆕 Nueva (notificaciones)
```

### Modelos (Models)
```
fuel/app/classes/model/payroll/
├── period.php                ✅ Ya existía
├── concept.php               ✅ Ya existía
├── receipt.php               ✅ Ya existía
└── receiptdetail.php         ✅ Ya existía
```

---

## 🎮 Acciones del Controlador

### Controller_Admin_Nomina - Acciones Principales

| Acción | Ruta | Descripción | Permiso |
|--------|------|-------------|---------|
| `action_index()` | `/admin/nomina` | Listado de períodos | nomina.view |
| `action_view($id)` | `/admin/nomina/view/{id}` | Ver detalle de período | nomina.view |
| `action_create()` | `/admin/nomina/create` | Crear nuevo período | nomina.create |
| `action_edit($id)` | `/admin/nomina/edit/{id}` | Editar período (solo draft) | nomina.edit |
| `action_calculate($id)` | `/admin/nomina/calculate/{id}` | Calcular nómina | nomina.calculate |
| `action_approve($id)` | `/admin/nomina/approve/{id}` | Aprobar nómina | nomina.approve |
| `action_delete($id)` | `/admin/nomina/delete/{id}` | Eliminar período | nomina.delete |

### Nuevas Acciones - Extensiones

| Acción | Ruta | Descripción | Permiso |
|--------|------|-------------|---------|
| `action_preview_calculate($id)` | `/admin/nomina/preview-calculate/{id}` | Preview antes de calcular | nomina.calculate |
| `action_preview_approve($id)` | `/admin/nomina/preview-approve/{id}` | Preview antes de aprobar | nomina.approve |
| `action_export_excel($id)` | `/admin/nomina/export-excel/{id}` | Exportar a Excel | nomina.export |
| `action_generate_pdf($receipt_id)` | `/admin/nomina/generate-pdf/{receipt_id}` | Generar PDF recibo | nomina.view |
| `action_stamp_cfdi($receipt_id)` | `/admin/nomina/stamp-cfdi/{receipt_id}` | Timbrar CFDI | nomina.stamp |
| `action_notify_receipt($receipt_id)` | `/admin/nomina/notify/{receipt_id}` | Notificar empleado | nomina.notify |

### Integrar Acciones al Controlador

Las nuevas acciones están en `nomina_extensions.php`. Para integrarlas:

**Opción 1: Copiar y pegar** las funciones a `fuel/app/classes/controller/admin/nomina.php`

**Opción 2: Usar autoload** (agregar al controlador):

```php
// Al inicio de Controller_Admin_Nomina
public function __construct()
{
    parent::__construct();
    // Cargar extensiones
    require_once APPPATH . 'classes/controller/admin/nomina_extensions.php';
}
```

---

## 🧾 Integración CFDI

### Uso Básico

```php
// En el controlador o donde se necesite
use Cfdi_Payroll_Stamper;

public function action_stamp_cfdi($receipt_id)
{
    try {
        $stamper = new Cfdi_Payroll_Stamper($this->tenant_id);
        $result = $stamper->stamp_receipt($receipt_id);

        if ($result['success']) {
            Session::set_flash('success', 'Recibo timbrado. UUID: ' . $result['uuid']);
        } else {
            Session::set_flash('error', 'Error al timbrar: ' . $result['error']);
        }
    } catch (Exception $e) {
        Session::set_flash('error', 'Error: ' . $e->getMessage());
    }

    Response::redirect('admin/nomina/view/' . $receipt->payroll_period_id);
}
```

### Flujo de Timbrado

1. **Período debe estar aprobado** (status = 'approved' o 'paid')
2. **Recibo no debe estar timbrado** (is_stamped = false)
3. **Generar XML** según especificaciones SAT
4. **Enviar al PAC** (Finkok, SW, Ecodex)
5. **Guardar resultado** (UUID, XML, PDF)
6. **Actualizar recibo** (is_stamped = true, stamped_at, cfdi_uuid, cfdi_xml)

### PACs Soportados

#### Finkok
- SOAP WebService
- Requiere: username, password
- URL: https://facturacion.finkok.com

#### SW Sapien
- REST API
- Requiere: token Bearer
- URL: https://api.sw.com.mx

#### Ecodex
- REST API
- Requiere: usuario, contraseña
- URL: https://cfdi.ecodex.com.mx

---

## 📧 Sistema de Notificaciones

### Uso Básico

```php
use Payroll_Notifier;

// Notificar a un empleado
$notifier = new Payroll_Notifier($this->tenant_id);

// Enviar por email solamente
$result = $notifier->notify_receipt_available($receipt_id, array('email'));

// Enviar por múltiples canales
$result = $notifier->notify_receipt_available($receipt_id, array('email', 'sms', 'push'));

// Notificar a todo un período
$results = $notifier->notify_period_receipts($period_id, array('email', 'sms'));
```

### Canales Disponibles

#### 1. Email
- **Proveedor**: SMTP (Gmail, Office365, etc.)
- **Características**:
  - Plantilla HTML profesional
  - Adjunto de PDF opcional
  - Soporte para múltiples destinatarios
- **Configuración**: `fuel/app/config/email.php`

#### 2. SMS
- **Proveedores**: Twilio, Nexmo/Vonage
- **Características**:
  - Envío masivo
  - Confirmación de entrega
  - Costos por mensaje
- **Configuración**: `fuel/app/config/notifications.php`

#### 3. WhatsApp
- **Proveedores**: Twilio, 360Dialog
- **Características**:
  - Mensajes multimedia
  - Confirmación de lectura
  - Requiere aprobación de Meta
- **Configuración**: `fuel/app/config/notifications.php`

#### 4. Push Notifications
- **Proveedores**: Firebase Cloud Messaging (FCM)
- **Características**:
  - Notificaciones en tiempo real
  - Soporte iOS y Android
  - Requiere app móvil
- **Configuración**: `fuel/app/config/notifications.php`

### Ejemplo de Notificación Completa

```php
public function action_notify_all($period_id)
{
    if (!Auth::has_access('nomina.notify')) {
        Session::set_flash('error', 'Sin permisos');
        Response::redirect('admin/nomina');
    }

    $notifier = new Payroll_Notifier($this->tenant_id);

    try {
        // Notificar a todos los empleados del período
        $results = $notifier->notify_period_receipts(
            $period_id,
            array('email', 'sms') // Canales a usar
        );

        Session::set_flash('success', 
            "Notificaciones enviadas: {$results['success']} exitosas, {$results['failed']} fallidas"
        );
    } catch (Exception $e) {
        Session::set_flash('error', 'Error: ' . $e->getMessage());
    }

    Response::redirect('admin/nomina/view/' . $period_id);
}
```

---

## 💡 Uso y Ejemplos

### Flujo Completo de Nómina

```
1. CREAR PERÍODO
   └─> admin/nomina/create
       • Ingresar datos del período
       • Estado inicial: draft

2. PREVIEW CALCULAR
   └─> admin/nomina/preview-calculate/{id}
       • Ver empleados que se procesarán
       • Ver estadísticas previas
       • Confirmar cálculo

3. CALCULAR NÓMINA
   └─> admin/nomina/calculate/{id}
       • Generar recibos automáticamente
       • Calcular percepciones y deducciones
       • Estado cambia a: calculated

4. PREVIEW APROBAR
   └─> admin/nomina/preview-approve/{id}
       • Revisar totales por departamento
       • Ver resumen de recibos
       • Validar montos

5. APROBAR NÓMINA
   └─> admin/nomina/approve/{id}
       • Confirmar autorización
       • Agregar comentarios
       • Estado cambia a: approved

6. EXPORTAR DISPERSIÓN
   └─> admin/nomina/export/{id}
       • Generar archivo TXT bancario
       • Formato: CLABE|MONTO|REFERENCIA|NOMBRE

7. TIMBRAR CFDI (opcional)
   └─> Para cada recibo:
       • admin/nomina/stamp-cfdi/{receipt_id}
       • Genera XML y solicita UUID al PAC
       • Guarda UUID y XML timbrado

8. NOTIFICAR EMPLEADOS
   └─> admin/nomina/notify/{receipt_id}
       • Enviar email con recibo
       • SMS opcional
       • WhatsApp opcional
       • Push notification opcional

9. GENERAR PDFs
   └─> admin/nomina/generate-pdf/{receipt_id}
       • Recibo en PDF profesional
       • Incluye UUID si está timbrado
       • Descarga directa

10. EXPORTAR A EXCEL
    └─> admin/nomina/export-excel/{period_id}
        • Reporte completo en Excel
        • Todos los recibos del período
        • Formato profesional
```

### Ejemplo de Uso desde el Código

```php
// Calcular nómina programáticamente
$period = Model_Payroll_Period::find($period_id);
$result = $period->calculate_payroll(Auth::get_user_id()[1]);

if ($result['success']) {
    echo "Calculados: {$result['calculated_count']} recibos";
    echo "Total neto: \$" . number_format($result['total_net'], 2);
}

// Exportar a Excel
Response::redirect('admin/nomina/export-excel/' . $period_id);

// Timbrar y notificar
$stamper = new Cfdi_Payroll_Stamper($tenant_id);
$notifier = new Payroll_Notifier($tenant_id);

foreach ($receipts as $receipt) {
    // Timbrar
    $stamp_result = $stamper->stamp_receipt($receipt->id);
    
    if ($stamp_result['success']) {
        // Notificar
        $notifier->notify_receipt_available(
            $receipt->id,
            array('email', 'sms')
        );
    }
}
```

---

## 🔧 Troubleshooting

### Error: "Class 'PhpOffice\PhpSpreadsheet\Spreadsheet' not found"

**Solución:**
```bash
composer require phpoffice/phpspreadsheet
composer dump-autoload
```

### Error: "Class 'TCPDF' not found"

**Solución:**
```bash
composer require tecnickcom/tcpdf
composer dump-autoload
```

### Error: "SoapClient not found" (Para CFDI)

**Solución:**
```bash
# En php.ini, habilitar:
extension=soap
extension=openssl

# Reiniciar Apache
```

### Error al enviar emails

**Solución:**
1. Verificar configuración SMTP en `fuel/app/config/email.php`
2. Para Gmail, habilitar "Aplicaciones menos seguras" o usar "Contraseñas de aplicación"
3. Verificar firewall (puerto 587 o 465)

```php
// Test de email
Email::forge()
    ->from('tu-email@empresa.com', 'Test')
    ->to('destino@test.com')
    ->subject('Test')
    ->body('Mensaje de prueba')
    ->send();
```

### Error al timbrar CFDI

**Solución:**
1. Verificar credenciales del PAC en `fuel/app/config/cfdi.php`
2. Verificar que el período esté en estado 'approved'
3. Verificar que el recibo no esté ya timbrado
4. Revisar logs de audit: `SELECT * FROM audit_logs WHERE module = 'nomina_receipt' ORDER BY created_at DESC`

### PDFs generados están en blanco

**Solución:**
```bash
# Habilitar extensión GD
extension=gd

# Verificar
php -m | grep gd
```

### Notificaciones SMS no se envían

**Solución:**
1. Verificar créditos en cuenta Twilio/Nexmo
2. Verificar formato de teléfono: `+521234567890` (con código de país)
3. Para WhatsApp: Verificar que el número esté registrado en WhatsApp Business

```php
// Test de SMS con Twilio
$notifier = new Payroll_Notifier($tenant_id);
$result = $notifier->send_sms_notification(
    $employee,
    array('employee_name' => 'Test', 'period_name' => 'Test', 'net_payment' => '1000.00', 'payment_date' => '01/01/2025')
);
var_dump($result);
```

---

## 📊 Resumen de Funcionalidades

| Funcionalidad | Estado | Archivo | Descripción |
|---------------|--------|---------|-------------|
| Vista Edit | ✅ | `views/admin/nomina/edit.php` | Edición de períodos solo en estado draft |
| Vista Calculate | ✅ | `views/admin/nomina/calculate.php` | Preview con estadísticas antes de calcular |
| Vista Approve | ✅ | `views/admin/nomina/approve.php` | Aprobación con resumen detallado |
| Export Excel | ✅ | `nomina_extensions.php::action_export_excel` | Exportación completa con formato |
| Generate PDF | ✅ | `nomina_extensions.php::action_generate_pdf` | Recibo individual en PDF |
| Stamp CFDI | ✅ | `cfdi/payrollstamper.php` | Timbrado con PAC (Finkok/SW/Ecodex) |
| Notify Email | ✅ | `payroll/notifier.php` | Notificación por email con plantilla HTML |
| Notify SMS | ✅ | `payroll/notifier.php` | SMS via Twilio/Nexmo |
| Notify WhatsApp | ✅ | `payroll/notifier.php` | WhatsApp via Twilio |
| Notify Push | ✅ | `payroll/notifier.php` | Push notifications via FCM |

---

## 🎉 ¡Listo para Producción!

Todas las funcionalidades solicitadas han sido implementadas:

1. ✅ **Vistas Completas** - edit, calculate, approve
2. ✅ **Exportación Excel** - PHPSpreadsheet con formato profesional
3. ✅ **Reportes PDF** - TCPDF con diseño completo
4. ✅ **Timbrado CFDI** - Integración con 3 PACs
5. ✅ **Notificaciones** - 4 canales (Email, SMS, WhatsApp, Push)

### Próximos Pasos Recomendados

1. Instalar dependencias: `composer install`
2. Configurar archivos: `email.php`, `notifications.php`, `cfdi.php`
3. Probar en ambiente de desarrollo
4. Configurar PAC de pruebas (sandbox)
5. Realizar pruebas con empleados de prueba
6. Configurar cron job para notificaciones automáticas
7. Migrar a producción

**Documentos Adicionales:**
- `INFORME_ESTADO_SISTEMA_FINAL.md` - Estado completo del sistema
- `CHANGELOG.md` - Historial de cambios

---

**Fecha de Documento:** 6 de Diciembre 2025  
**Versión:** 2.0  
**Sistema:** ERP Multi-Tenant - Módulo de Nómina
