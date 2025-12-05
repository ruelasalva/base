# 📚 Guía de Uso: Sistema de Identidades Unificado
**Fecha:** 4 de Diciembre 2025  
**Estado:** ✅ Implementado y Funcional

---

## 🎯 ¿Qué se implementó?

### **1. Tablas Creadas**

#### `user_identities`
Tabla pivot polimórfica que conecta `users` con diferentes entidades (empleados, proveedores, clientes).

```sql
CREATE TABLE user_identities (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT → users.id (FK)
  identity_type ENUM('employee', 'provider', 'customer', 'partner')
  identity_id INT → ID de la entidad específica
  is_primary TINYINT(1) → 1 si es la identidad principal del usuario
  can_login TINYINT(1) → 1 si puede acceder al sistema con esta identidad
  access_level ENUM('full', 'readonly', 'limited')
  created_at DATETIME
  updated_at DATETIME
)
```

#### `provider_departments`
Tabla N:N que relaciona proveedores con departamentos que surten.

```sql
CREATE TABLE provider_departments (
  id INT PRIMARY KEY AUTO_INCREMENT,
  provider_id INT → providers.id (FK)
  department_id INT → employees_departments.id (FK)
  is_primary TINYINT(1) → 1 si es el departamento principal
  notes TEXT → Notas adicionales
  deleted TINYINT(1) → Soft delete
  created_at DATETIME
  updated_at DATETIME
)
```

---

## 🔧 Modelos ORM Creados

### `Model_User_Identity`
**Ubicación:** `fuel/app/classes/model/user/identity.php`

**Métodos principales:**
```php
// Obtener la entidad específica (employee, provider, customer)
$identity = Model_User_Identity::find(1);
$employee = $identity->get_identity(); // Devuelve Model_Employee

// Obtener todas las identidades de un usuario
$identities = Model_User_Identity::get_user_identities($user_id);

// Obtener identidad principal
$primary = Model_User_Identity::get_primary_identity($user_id);

// Verificar si existe una identidad
$has = Model_User_Identity::has_identity($user_id, 'provider', $provider_id);

// Crear nueva identidad
Model_User_Identity::create_identity(
    $user_id, 
    'provider', 
    $provider_id, 
    $is_primary = false,
    $can_login = true,
    $access_level = 'readonly'
);
```

### `Model_Provider_Department`
**Ubicación:** `fuel/app/classes/model/provider/department.php`

**Métodos principales:**
```php
// Obtener departamento principal de un proveedor
$primary_dept = Model_Provider_Department::get_primary($provider_id);
echo $primary_dept->department->name; // "Compras"

// Obtener todos los departamentos activos
$departments = Model_Provider_Department::get_active_departments($provider_id);

// Asignar departamento a proveedor
Model_Provider_Department::assign(
    $provider_id, 
    $department_id, 
    $is_primary = true,
    $notes = 'Proveedor exclusivo de este departamento'
);

// Desasignar departamento (soft delete)
Model_Provider_Department::unassign($provider_id, $department_id);
```

---

## 📝 Modelos Actualizados

### `Model_Provider`
**Nuevas relaciones:**
```php
// Relación con identidades de usuario
$provider->identities; // Todas las identidades de este proveedor

// Relación con departamentos
$provider->departments; // Departamentos que surte
```

**Métodos helper:**
```php
// Obtener usuario asociado (si existe)
$user = $provider->get_user();
if ($user) {
    echo "Email: " . $user->email;
}

// Verificar si tiene acceso al portal
if ($provider->has_portal_access()) {
    echo "Puede entrar al portal";
}

// Obtener departamento principal
$dept = $provider->get_primary_department();
echo $dept->department->name;

// Obtener todos los departamentos activos
$depts = $provider->get_active_departments();
```

### `Model_Employee`
**Nueva relación:**
```php
$employee->identities; // Identidades de usuario de este empleado
```

**Métodos helper:**
```php
// Obtener usuario desde identities (reemplaza el uso de user_id)
$user = $employee->get_identity_user();

// Verificar si tiene acceso al sistema
if ($employee->has_system_access()) {
    echo "Tiene usuario de acceso";
}
```

---

## 🚀 Casos de Uso Comunes

### **1. Dar Acceso al Portal a un Proveedor**

```php
// Crear usuario para el proveedor
$provider = Model_Provider::find($provider_id);

$user = Model_User::forge([
    'username' => 'prov_' . $provider->code,
    'email' => $provider->email,
    'password' => Auth::hash_password('temporal123'),
    'group_id' => 50, // ID del grupo "Proveedores"
    'tenant_id' => $provider->tenant_id,
    'is_active' => 1
]);
$user->save();

// Crear identidad
Model_User_Identity::create_identity(
    $user->id,
    'provider',
    $provider->id,
    $is_primary = true,
    $can_login = true,
    $access_level = 'readonly'
);

// Enviar email con credenciales
Session::set_flash('success', 'Acceso al portal creado. Usuario: prov_' . $provider->code);
```

### **2. Asignar Departamentos a un Proveedor**

```php
$provider_id = 5;

// Asignar Compras como departamento principal
Model_Provider_Department::assign(
    $provider_id,
    3, // ID departamento "Compras"
    true, // Es principal
    'Proveedor principal de materia prima'
);

// Asignar Almacén como secundario
Model_Provider_Department::assign(
    $provider_id,
    5, // ID departamento "Almacén"
    false,
    'También surte almacén ocasionalmente'
);
```

### **3. Listar Proveedores con Departamentos**

```php
public function action_index()
{
    $providers = Model_Provider::query()
        ->related('departments')
        ->where('is_active', 1)
        ->get();

    foreach ($providers as $provider) {
        echo $provider->company_name . "<br>";
        
        foreach ($provider->departments as $pd) {
            if ($pd->deleted == 0 && $pd->department) {
                $label = $pd->is_primary ? " (Principal)" : "";
                echo " - " . $pd->department->name . $label . "<br>";
            }
        }
    }
}
```

### **4. Verificar Permisos por Identidad**

```php
// En el login o middleware
$user = Auth::get_user();
$identity = Model_User_Identity::get_primary_identity($user->id);

switch ($identity->identity_type) {
    case 'employee':
        // Redirigir a backend completo
        Response::redirect('admin/dashboard');
        break;
    
    case 'provider':
        // Redirigir a portal de proveedores
        Response::redirect('portal/provider/dashboard');
        break;
    
    case 'customer':
        // Redirigir a portal de clientes
        Response::redirect('portal/customer/orders');
        break;
}
```

### **5. Usuario Multi-identidad (Empleado + Proveedor)**

```php
// Un freelancer puede ser empleado interno Y proveedor externo
$user_id = 10;

// Identidad como empleado
Model_User_Identity::create_identity(
    $user_id,
    'employee',
    15, // ID del empleado
    true, // Identidad principal
    true,
    'full'
);

// Identidad como proveedor
Model_User_Identity::create_identity(
    $user_id,
    'provider',
    20, // ID del proveedor
    false, // No es principal
    true,
    'readonly'
);

// Obtener todas sus identidades
$identities = Model_User_Identity::get_user_identities($user_id);
echo "Este usuario tiene " . count($identities) . " identidades";
```

### **6. Super Admin Multi-tenant**

```php
// Un super admin puede tener identidades en múltiples tenants
$superadmin_user_id = 1;

// Tenant 1 - Empresa A
Model_User_Identity::create_identity(
    $superadmin_user_id,
    'employee',
    1, // Admin de Empresa A
    false,
    true,
    'full'
);

// Tenant 2 - Empresa B
Model_User_Identity::create_identity(
    $superadmin_user_id,
    'employee',
    2, // Admin de Empresa B
    false,
    true,
    'full'
);

// Cambiar entre tenants
Session::set('active_identity_id', $identity_id);
```

---

## 🔍 Consultas Útiles

### Ver todas las identidades de un usuario
```sql
SELECT 
    ui.identity_type,
    CASE 
        WHEN ui.identity_type = 'employee' THEN e.name
        WHEN ui.identity_type = 'provider' THEN p.company_name
        WHEN ui.identity_type = 'customer' THEN c.company_name
    END as entity_name,
    ui.is_primary,
    ui.can_login,
    ui.access_level
FROM user_identities ui
LEFT JOIN employees e ON ui.identity_type = 'employee' AND ui.identity_id = e.id
LEFT JOIN providers p ON ui.identity_type = 'provider' AND ui.identity_id = p.id
LEFT JOIN customers c ON ui.identity_type = 'customer' AND ui.identity_id = c.id
WHERE ui.user_id = 10;
```

### Ver departamentos de un proveedor
```sql
SELECT 
    p.company_name,
    ed.name as department_name,
    pd.is_primary,
    pd.notes
FROM provider_departments pd
INNER JOIN providers p ON pd.provider_id = p.id
INNER JOIN employees_departments ed ON pd.department_id = ed.id
WHERE pd.deleted = 0 
  AND pd.provider_id = 5
ORDER BY pd.is_primary DESC, ed.name;
```

### Proveedores sin departamento asignado
```sql
SELECT 
    p.id,
    p.code,
    p.company_name
FROM providers p
LEFT JOIN provider_departments pd ON p.id = pd.provider_id AND pd.deleted = 0
WHERE p.is_active = 1
  AND pd.id IS NULL;
```

---

## ⚠️ Notas Importantes

### **Migración de Datos Legacy**
- Si tienes empleados con `user_id` en la tabla `employees`, ejecuta:
  ```bash
  mysql -u root base < sql/migrate_identities.sql
  ```
- El script está en: `fuel/app/migrations/` (archivos 001 y 002)

### **Relaciones Antiguas**
- `employees.user_id` → **No eliminar**, mantener por compatibilidad
- Usar `$employee->get_identity_user()` en código nuevo
- La relación `$employee->user` sigue funcionando para código legacy

### **Soft Deletes**
- `provider_departments.deleted` → Soft delete (0 = activo, 1 = eliminado)
- Siempre filtrar por `deleted = 0` en consultas

### **Índices y Performance**
- Ambas tablas tienen índices en claves foráneas
- `user_identities` tiene índice UNIQUE en `(identity_type, identity_id)`
- Esto garantiza que una entidad solo puede tener UN usuario asociado

---

## 🎨 Próximos Pasos Sugeridos

1. **Crear vista de gestión de departamentos** en el módulo de proveedores
2. **Implementar portal de proveedores** donde puedan:
   - Ver sus facturas pendientes
   - Subir documentos
   - Ver historial de pagos
3. **Crear middleware** para verificar `identity_type` y redirigir automáticamente
4. **Agregar logs de auditoría** que registren qué identidad realizó cada acción
5. **Implementar cambio de identidad** si un usuario tiene múltiples (switcher en el header)

---

## 📞 Preguntas Frecuentes

**P: ¿Un proveedor DEBE tener usuario para funcionar?**  
R: No, es opcional. Solo si quieres darle acceso al portal.

**P: ¿Un empleado puede ser proveedor al mismo tiempo?**  
R: Sí, con identidades múltiples. Útil para freelancers o consultores externos.

**P: ¿Cómo elimino el acceso de un proveedor?**  
R: Establece `can_login = 0` en su identity, o desactiva el usuario: `$user->is_active = 0`.

**P: ¿Puedo asignar un proveedor a múltiples departamentos?**  
R: Sí, con `provider_departments`. Uno debe ser `is_primary = 1`.

**P: ¿Qué pasa si elimino un proveedor?**  
R: Por el `CASCADE DELETE`, sus registros en `provider_departments` y `user_identities` se eliminan automáticamente.

---

## ✅ Resumen de Archivos Creados/Modificados

### Creados:
- ✅ `fuel/app/migrations/001_create_user_identities.php`
- ✅ `fuel/app/migrations/002_create_provider_departments.php`
- ✅ `fuel/app/classes/model/user/identity.php`
- ✅ `fuel/app/classes/model/provider/department.php`
- ✅ `sql/migrate_identities.sql`
- ✅ `ARQUITECTURA_USUARIOS.md` (documento de diseño)
- ✅ Este archivo: `GUIA_SISTEMA_IDENTIDADES.md`

### Modificados:
- ✅ `fuel/app/classes/model/provider.php` (+ relaciones y métodos)
- ✅ `fuel/app/classes/model/employee.php` (+ relaciones y métodos)
- ✅ `fuel/app/classes/controller/admin/proveedores.php` (action_info actualizado)

**Estado:** 🟢 Sistema funcional y listo para usar
