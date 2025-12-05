# 🔐 Guía Rápida: Gestión de Acceso Multi-tenant
**Fecha:** 4 de Diciembre 2025

---

## 🎯 ¿Qué se puede hacer ahora?

### **1. Asignar Super Admin a TODOS los Backends**

#### Desde la interfaz:
1. Ve a **Admin → Usuarios**
2. Busca tu usuario super admin (ej: usuario ID 3)
3. Clic en su nombre para ver info
4. Clic en botón **"Gestionar Backends"** (azul)
5. Clic en **"Asignar TODOS los Backends"**
6. ✅ Listo - El usuario ya puede entrar a todos los backends

#### Desde código (si quieres automatizar):
```php
// Asignar usuario 3 (super admin) a todos los tenants
Helper_User_Tenant::assign_all_tenants(
    3,  // ID del usuario
    1   // Tenant por defecto (backend principal)
);
```

#### Desde SQL directo:
```sql
-- Ver el ID de tu usuario super admin
SELECT id, username, email FROM users WHERE group_id = 100;

-- Asignar a todos los tenants (reemplaza 3 con tu user_id)
CALL asignar_super_admin(3);
```

---

### **2. Dar Acceso al Portal a un Proveedor**

#### Desde la interfaz:
1. Ve a **Admin → Proveedores**
2. Busca el proveedor
3. Clic en su nombre para ver info
4. Clic en botón **"Gestionar Acceso"** (azul)
5. Llena el formulario:
   - **Usuario:** Se genera automático como `prov_CODIGO`
   - **Contraseña:** Escribe una temporal (ej: `temporal123`)
   - **Nivel de Acceso:** Selecciona "Solo Lectura" o "Completo"
   - **Backends:** Marca los checkboxes de los backends a los que puede entrar
   - **Backend por Defecto:** Selecciona cuál será su página principal
6. Clic en **"Crear Usuario"**
7. ✅ El proveedor ya puede hacer login con `prov_CODIGO` / `temporal123`

#### Ejemplo de credenciales generadas:
```
Proveedor: PRO-000001 - Distribuidora de Insumos SA
Usuario: prov_pro-000001
Contraseña: temporal123
Backends: localhost (Empresa Principal)
```

---

### **3. Gestionar Backends de un Usuario Existente**

#### Para empleados, admins, o cualquier usuario:
1. Ve a **Admin → Usuarios**
2. Busca el usuario
3. Clic en su nombre para ver info
4. Clic en botón **"Gestionar Backends"** (azul)
5. En el formulario:
   - Marca/desmarca los backends a los que tiene acceso
   - Selecciona cuál es su backend por defecto
6. Clic en **"Guardar Configuración"**

---

## 📋 Casos de Uso Comunes

### **Caso 1: Super Admin que entra a múltiples empresas**

**Problema:** Tienes un usuario que administra 3 empresas diferentes (3 backends).

**Solución:**
1. Ve a **Admin → Usuarios → [Super Admin] → Gestionar Backends**
2. Clic en **"Asignar TODOS los Backends"**
3. Selecciona backend por defecto: "Empresa Principal"
4. ✅ Ya puede cambiar entre empresas desde el sistema

**Código alternativo:**
```php
$super_admin_id = 3;
Helper_User_Tenant::assign_all_tenants($super_admin_id, 1);
```

---

### **Caso 2: Proveedor que solo ve facturas y pagos**

**Escenario:** Proveedor "Distribuidora XYZ" necesita consultar sus facturas pendientes.

**Pasos:**
1. Admin → Proveedores → Buscar "Distribuidora XYZ"
2. Info del proveedor → **"Gestionar Acceso"**
3. Crear Usuario:
   - Usuario: `prov_xyz001` (automático)
   - Contraseña: `DistXYZ2025`
   - Nivel: **Solo Lectura**
   - Backends: ✅ localhost (Empresa Principal)
   - Backend por defecto: localhost
4. **Crear Usuario**
5. Enviar email al proveedor con credenciales

**Email al proveedor:**
```
Estimado proveedor,

Se ha creado su acceso al portal:

URL: http://localhost/admin/login
Usuario: prov_xyz001
Contraseña: DistXYZ2025

Podrá consultar:
- Facturas pendientes
- Historial de pagos
- Órdenes de compra

Por seguridad, cambie su contraseña al primer ingreso.

Saludos
```

---

### **Caso 3: Empleado que trabaja en 2 sucursales (2 backends)**

**Escenario:** Gerente de compras que trabaja en Empresa A y Empresa B.

**Pasos:**
1. Admin → Usuarios → Buscar gerente
2. Info del usuario → **"Gestionar Backends"**
3. Marcar checkboxes:
   - ✅ Empresa A (localhost)
   - ✅ Empresa B (empresa-b.local)
4. Backend por defecto: Empresa A
5. **Guardar Configuración**

**Resultado:**
- Al hacer login, entra a Empresa A
- Puede cambiar a Empresa B desde el menú del sistema

---

### **Caso 4: Revocar acceso de un proveedor**

**Escenario:** Proveedor suspendido, remover su acceso al portal.

**Opción A - Eliminar usuario:**
1. Admin → Proveedores → Info del proveedor
2. **"Gestionar Acceso"**
3. Clic en **"Eliminar Usuario"**
4. Confirmar

**Opción B - Desactivar temporalmente:**
```php
$provider = Model_Provider::find($provider_id);
$user = $provider->get_user();

if ($user) {
    DB::update('users')
        ->set(['is_active' => 0])
        ->where('id', $user->id)
        ->execute();
}
```

---

## 🗃️ Estructura de Datos

### Tabla `user_tenants`
```sql
SELECT 
    u.username,
    t.company_name as backend,
    ut.is_default as es_default,
    ut.is_active as activo
FROM user_tenants ut
JOIN users u ON ut.user_id = u.id
JOIN tenants t ON ut.tenant_id = t.id
WHERE u.id = 3;  -- Tu super admin
```

**Resultado ejemplo:**
```
username    | backend              | es_default | activo
------------|----------------------|------------|--------
admin_super | Empresa Principal    | 1          | 1
admin_super | Sucursal Norte       | 0          | 1
admin_super | Sucursal Sur         | 0          | 1
```

### Tabla `user_identities`
```sql
SELECT 
    u.username,
    ui.identity_type,
    CASE 
        WHEN ui.identity_type = 'employee' THEN e.name
        WHEN ui.identity_type = 'provider' THEN p.company_name
    END as entity_name,
    ui.can_login,
    ui.access_level
FROM user_identities ui
JOIN users u ON ui.user_id = u.id
LEFT JOIN employees e ON ui.identity_type = 'employee' AND ui.identity_id = e.id
LEFT JOIN providers p ON ui.identity_type = 'provider' AND ui.identity_id = p.id;
```

---

## 🔧 Helper Disponible: `Helper_User_Tenant`

### Métodos principales:

```php
// Asignar un usuario a un backend
Helper_User_Tenant::assign($user_id, $tenant_id, $is_default);

// Remover acceso
Helper_User_Tenant::unassign($user_id, $tenant_id);

// Obtener backends de un usuario
$tenants = Helper_User_Tenant::get_user_tenants($user_id);

// Verificar si tiene acceso a un backend específico
if (Helper_User_Tenant::has_access($user_id, $tenant_id)) {
    echo "Tiene acceso";
}

// Asignar a TODOS los backends (super admin)
Helper_User_Tenant::assign_all_tenants($user_id, $default_tenant_id);

// Sincronizar super admin con nuevos backends creados
Helper_User_Tenant::sync_super_admin($user_id);
```

---

## 🚀 Rutas Creadas

### Para proveedores:
- `/admin/proveedores/manage_access/{id}` - Gestionar acceso del proveedor
- `/admin/proveedores/create_user` (POST) - Crear usuario para proveedor
- `/admin/proveedores/update_tenants` (POST) - Actualizar backends del proveedor
- `/admin/proveedores/delete_user` (POST) - Eliminar usuario del proveedor

### **Para usuarios:**
- `/admin/users/manage_tenants/{id}` - Gestionar backends del usuario
- `/admin/users/update_user_tenants` (POST) - Actualizar backends
- `/admin/users/assign_all_tenants` (POST) - Asignar todos los backends (super admin)

---

## ✅ Checklist de Configuración Inicial

Para configurar tu sistema por primera vez:

- [ ] 1. Verificar que tengas al menos 1 tenant activo
  ```sql
  SELECT * FROM tenants WHERE is_active = 1;
  ```

- [ ] 2. Identificar tu usuario super admin
  ```sql
  SELECT id, username FROM users WHERE group_id = 100;
  ```

- [ ] 3. Asignar super admin a todos los backends
  - Opción A: Interfaz → Usuarios → Gestionar Backends → "Asignar TODOS"
  - Opción B: `Helper_User_Tenant::assign_all_tenants(3, 1);`

- [ ] 4. Verificar asignación
  ```sql
  SELECT * FROM user_tenants WHERE user_id = 3;
  ```

- [ ] 5. Probar acceso de proveedor (crear usuario de prueba)
  - Proveedores → Info → Gestionar Acceso → Crear Usuario

- [ ] 6. Documentar credenciales de proveedores creados

---

## 🆘 Solución de Problemas

### Problema: "Usuario no tiene acceso a ningún backend"
**Solución:** Asignar al menos un tenant:
```php
Helper_User_Tenant::assign($user_id, 1, true);
```

### Problema: "Proveedor no puede hacer login"
**Verificar:**
1. Usuario existe: `SELECT * FROM users WHERE username = 'prov_codigo'`
2. Usuario activo: `is_active = 1`
3. Tiene identity: `SELECT * FROM user_identities WHERE user_id = X`
4. Tiene tenant asignado: `SELECT * FROM user_tenants WHERE user_id = X`

### Problema: "Super admin no ve todos los backends"
**Solución:**
```php
Helper_User_Tenant::sync_super_admin($user_id);
```

---

## 📦 Archivos Creados

1. ✅ `fuel/app/classes/helper/user/tenant.php` - Helper para gestión multi-tenant
2. ✅ `fuel/app/classes/controller/admin/proveedores.php` - Acciones agregadas
3. ✅ `fuel/app/classes/controller/admin/usuarios.php` - Acciones agregadas
4. ✅ `fuel/app/views/admin/proveedores/manage_access.php` - Vista gestión proveedor
5. ✅ `fuel/app/views/admin/usuarios/manage_tenants.php` - Vista gestión usuario

---

## 🎓 Resumen Rápido

**Para Super Admin:**
```
Admin → Users → Tu Usuario → "Gestionar Backends" → "Asignar TODOS"
```

**Para Proveedor:**
```
Admin → Proveedores → Proveedor → "Gestionar Acceso" → "Crear Usuario"
```

**Para Empleado:**
```
Admin → Users → Empleado → "Gestionar Backends" → Marcar checkboxes
```

---

**Estado:** ✅ Sistema completamente funcional  
**Fecha:** 4 de Diciembre 2025
