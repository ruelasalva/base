# Arquitectura de Sistema de Usuarios Unificado
**Fecha:** 4 de Diciembre 2025  
**Proyecto:** Base ERP Multi-tenant

---

## 🔍 ANÁLISIS DE SITUACIÓN ACTUAL

### Problemas Identificados

1. **Fragmentación de Entidades:**
   - `users` → Usuarios del sistema (admin, empleados internos)
   - `employees` → Empleados con `user_id` opcional
   - `providers` → Proveedores SIN relación directa a `users`
   - `customers` (presumiblemente existe) → Clientes externos
   - **Resultado:** Duplicación de datos (email, phone, name) y complejidad en permisos

2. **Relaciones Inconsistentes:**
   - `employees.user_id` → Puede ser NULL (empleados sin acceso)
   - `providers.activated_by` → Apunta a `users.id` pero el proveedor NO tiene user_id
   - **No existe:** Forma de que un proveedor tenga acceso al portal
   - **No existe:** Forma de que un cliente tenga acceso al portal

3. **Departamentos Sin Sistema:**
   - `employees_departments` existe (tabla simple: id, name)
   - `providers_departments` NO existe (la trajiste de sajor)
   - **Falta:** Sistema para asignar proveedores a departamentos que surten

4. **Multi-tenant Sin Aprovechar:**
   - `users.tenant_id` existe
   - `providers.tenant_id` existe
   - **Pero:** No hay estrategia clara de permisos cross-tenant para super-admins

---

## 💡 PROPUESTAS DE SOLUCIÓN

### **OPCIÓN 1: Sistema Unificado con Tabla Pivot** ⭐ RECOMENDADA

**Concepto:** Un usuario puede tener múltiples "identidades" (roles externos) mediante relaciones polimórficas.

#### Estructura:

```
users (tabla central - YA EXISTE, mejorar)
├── id
├── tenant_id
├── username
├── email
├── password
├── group_id (rol principal: admin, user, etc)
├── is_active
└── ... campos existentes

user_identities (NUEVA - tabla pivot polimórfica)
├── id
├── user_id → users.id
├── identity_type → 'employee' | 'provider' | 'customer' | 'partner'
├── identity_id → ID de la tabla específica
├── is_primary (boolean) → identidad principal del usuario
├── can_login (boolean) → puede acceder con esta identidad
├── access_level → 'full' | 'readonly' | 'limited'
├── created_at
└── updated_at

employees (mantener, SIN user_id)
├── id
├── codigo
├── name
├── last_name
├── department_id → employees_departments.id
├── email
├── phone
└── ... campos de empleado

providers (mantener, SIN cambios mayores)
├── id (ya existe)
├── tenant_id
├── company_name
├── contact_name
├── email
└── ... campos existentes

customers (mantener/crear)
├── id
├── tenant_id
├── company_name
├── contact_name
├── email
└── ... campos de cliente

provider_departments (NUEVA - relación N:N)
├── id
├── provider_id → providers.id
├── department_id → employees_departments.id
├── is_primary (boolean)
├── notes
├── created_at
└── deleted
```

#### Ventajas:
✅ **UN usuario** puede ser empleado Y proveedor (ej: freelancer externo)  
✅ **Separación clara** entre datos de negocio (provider) y acceso (user)  
✅ **Escalable:** Agregar "partner", "distributor", etc es solo un nuevo identity_type  
✅ **Auditoría:** Sabes qué identidad usó el usuario para cada acción  
✅ **Multi-tenant:** Super-admin puede tener identities en múltiples tenants  
✅ **Sin duplicación:** email/phone se mantiene solo en la tabla específica  

#### Implementación:

**Paso 1:** Crear `user_identities`
```sql
CREATE TABLE `user_identities` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `identity_type` ENUM('employee', 'provider', 'customer', 'partner') NOT NULL,
  `identity_id` INT UNSIGNED NOT NULL,
  `is_primary` TINYINT(1) DEFAULT 0,
  `can_login` TINYINT(1) DEFAULT 1,
  `access_level` ENUM('full', 'readonly', 'limited') DEFAULT 'full',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_identity` (`identity_type`, `identity_id`),
  KEY `idx_user_id` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Paso 2:** Crear `provider_departments`
```sql
CREATE TABLE `provider_departments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `provider_id` INT UNSIGNED NOT NULL,
  `department_id` INT UNSIGNED NOT NULL,
  `is_primary` TINYINT(1) DEFAULT 0,
  `notes` TEXT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME ON UPDATE CURRENT_TIMESTAMP,
  `deleted` TINYINT(1) DEFAULT 0,
  KEY `idx_provider` (`provider_id`),
  KEY `idx_department` (`department_id`),
  FOREIGN KEY (`provider_id`) REFERENCES `providers`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`department_id`) REFERENCES `employees_departments`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Paso 3:** Migrar datos existentes (si hay employees con user_id)
```sql
INSERT INTO user_identities (user_id, identity_type, identity_id, is_primary, can_login)
SELECT user_id, 'employee', id, 1, 1
FROM employees
WHERE user_id IS NOT NULL;
```

**Paso 4:** Crear modelos FuelPHP

```php
// Model_User_Identity
class Model_User_Identity extends \Orm\Model {
    protected static $_table_name = 'user_identities';
    
    protected static $_belongs_to = [
        'user' => [
            'model_to' => 'Model_User',
            'key_from' => 'user_id',
            'key_to' => 'id'
        ]
    ];
    
    // Relación polimórfica
    public function get_identity() {
        switch($this->identity_type) {
            case 'employee':
                return Model_Employee::find($this->identity_id);
            case 'provider':
                return Model_Provider::find($this->identity_id);
            case 'customer':
                return Model_Customer::find($this->identity_id);
            default:
                return null;
        }
    }
}

// Model_Provider_Department
class Model_Provider_Department extends \Orm\Model {
    protected static $_table_name = 'provider_departments';
    
    protected static $_belongs_to = [
        'provider' => [
            'model_to' => 'Model_Provider',
            'key_from' => 'provider_id',
            'key_to' => 'id'
        ],
        'department' => [
            'model_to' => 'Model_Employees_Department',
            'key_from' => 'department_id',
            'key_to' => 'id'
        ]
    ];
}
```

**Paso 5:** Actualizar Model_Provider
```php
protected static $_has_many = [
    'identities' => [
        'model_to' => 'Model_User_Identity',
        'key_from' => 'id',
        'key_to' => 'identity_id',
        'conditions' => ['where' => [['identity_type', '=', 'provider']]]
    ],
    'departments' => [
        'model_to' => 'Model_Provider_Department',
        'key_from' => 'id',
        'key_to' => 'provider_id'
    ]
];

// Método helper
public function get_user() {
    $identity = Model_User_Identity::query()
        ->related('user')
        ->where('identity_type', 'provider')
        ->where('identity_id', $this->id)
        ->get_one();
    
    return $identity ? $identity->user : null;
}
```

#### Casos de Uso:

**1. Super Admin Cross-Tenant:**
```php
// Usuario admin_super tiene identidades en múltiples tenants
$user = Auth::get_user();
$identities = Model_User_Identity::query()
    ->where('user_id', $user->id)
    ->get();
    
// Cambiar de tenant
Session::set('active_identity', $identity->id);
```

**2. Proveedor con Acceso al Portal:**
```php
// Crear usuario para proveedor
$user = Model_User::forge([
    'username' => 'prov_' . $provider->code,
    'email' => $provider->email,
    'password' => Auth::hash_password('temporal123'),
    'group_id' => 50, // grupo "proveedores"
    'tenant_id' => $provider->tenant_id
]);
$user->save();

// Vincular identidad
Model_User_Identity::forge([
    'user_id' => $user->id,
    'identity_type' => 'provider',
    'identity_id' => $provider->id,
    'is_primary' => 1,
    'can_login' => 1
])->save();
```

**3. Empleado Interno (ya existente):**
```php
// Mantener como está, pero agregar identity si tiene user_id
if ($employee->user_id) {
    Model_User_Identity::forge([
        'user_id' => $employee->user_id,
        'identity_type' => 'employee',
        'identity_id' => $employee->id,
        'is_primary' => 1
    ])->save();
}
```

**4. Asignar Departamentos a Proveedor:**
```php
// Proveedor surte a Compras y Almacén
Model_Provider_Department::forge([
    'provider_id' => 1,
    'department_id' => 3, // Compras
    'is_primary' => 1
])->save();

Model_Provider_Department::forge([
    'provider_id' => 1,
    'department_id' => 5, // Almacén
    'is_primary' => 0
])->save();
```

---

### **OPCIÓN 2: Tabla de Usuarios Extendida (Menos Flexible)**

Agregar campos a `users`:
- `entity_type` → 'internal' | 'provider' | 'customer'
- `entity_id` → ID de la tabla relacionada

**Desventajas:**
❌ Un usuario solo puede ser UNA cosa (no puede ser empleado Y proveedor)  
❌ Menos escalable para nuevos tipos  
❌ Complica las consultas JOIN  

---

### **OPCIÓN 3: Mantener Separado + Portal Independiente**

- `users` → Solo personal interno
- `providers_users` → Nueva tabla para acceso de proveedores
- `customers_users` → Nueva tabla para acceso de clientes

**Desventajas:**
❌ Multiplicación de tablas de autenticación  
❌ Lógica de login duplicada  
❌ Difícil gestionar super-admins  

---

## 📊 ANÁLISIS DE TRABAJO

### Opción 1 (Recomendada):

**Esfuerzo Estimado:** 8-12 horas

**Tareas:**
1. ✅ Crear migración para `user_identities` (30 min)
2. ✅ Crear migración para `provider_departments` (20 min)
3. ✅ Crear Model_User_Identity con lógica polimórfica (1 hora)
4. ✅ Crear Model_Provider_Department (30 min)
5. ✅ Actualizar Model_Provider con relaciones (1 hora)
6. ✅ Actualizar Model_Employee con relaciones (30 min)
7. ✅ Migrar datos existentes de employees.user_id (1 hora)
8. ✅ Crear Helper_Identity para gestión de identidades (2 horas)
9. ✅ Actualizar Auth checks en controllers (2 horas)
10. ✅ Crear vistas de gestión de identidades (2 horas)
11. ✅ Testing y ajustes (2 horas)

**Beneficios:**
- Sistema escalable para 5+ años
- Soporte natural para multi-tenant
- Auditoría completa de acciones por identidad
- Reduce duplicación de código
- Permite casos complejos (freelancer = empleado + proveedor)

---

## 🎯 RECOMENDACIÓN FINAL

**Implementar OPCIÓN 1** por las siguientes razones:

1. **Escalabilidad:** Puedes agregar "partner", "distributor", "auditor" sin tocar estructura
2. **Multi-tenant Real:** Super-admins pueden tener identidades en múltiples empresas
3. **Auditoría:** Cada acción se registra con qué identidad se usó
4. **Flexibilidad:** Un mismo email puede tener roles en diferentes contextos
5. **No Destructivo:** No requiere eliminar tablas existentes
6. **FuelPHP Compatible:** ORM soporta bien relaciones polimórficas con métodos custom

---

## 📝 PRÓXIMOS PASOS SI ACEPTAS

1. Crear migración para `user_identities`
2. Crear migración para `provider_departments`
3. Crear modelos ORM
4. Migrar datos de `employees.user_id`
5. Actualizar controller de proveedores para usar departamentos
6. Crear helper de permisos basado en identidades

---

## ❓ PREGUNTAS PARA TI

1. ¿Tienes ya tabla `customers`? ¿Qué estructura tiene?
2. ¿Quieres que proveedores puedan ver sus facturas/pagos en un portal?
3. ¿Los clientes necesitarán portal para ver pedidos/facturas?
4. ¿Hay más "tipos de usuario externo" que debamos considerar?
5. ¿Aprobamos implementar la Opción 1?

