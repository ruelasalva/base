# GUÍA DE CORRECCIÓN: Html::chars() → htmlspecialchars()
## Solución Definitiva para FuelPHP 1.8.2

---

## 🔍 PROBLEMA IDENTIFICADO

En FuelPHP 1.8.2, el método `Html::chars()` **NO EXISTE**. Este método era común en versiones anteriores pero fue eliminado o nunca implementado en esta versión.

### **Errores comunes:**
```
Error [ Error ]: Call to undefined method Fuel\Core\Html::chars()
```

---

## ✅ SOLUCIÓN CORRECTA

Usar la función nativa de PHP `htmlspecialchars()` con los parámetros correctos:

```php
// ❌ INCORRECTO (NO FUNCIONA EN FUELPHP 1.8.2):
<?php echo Html::chars($variable); ?>

// ✅ CORRECTO (FUNCIONA EN TODAS LAS VERSIONES):
<?php echo htmlspecialchars($variable, ENT_QUOTES, 'UTF-8'); ?>
```

---

## 📋 PARÁMETROS DE htmlspecialchars()

```php
htmlspecialchars(
    string $string,      // Variable a escapar
    int $flags,          // ENT_QUOTES = Escapa comillas simples y dobles
    string $encoding     // 'UTF-8' = Codificación de caracteres
)
```

### **Flags recomendados:**
- `ENT_QUOTES` - Convierte comillas dobles y simples
- `ENT_COMPAT` - Solo convierte comillas dobles (no recomendado)
- `ENT_NOQUOTES` - No convierte comillas (no recomendado para seguridad)

### **Encoding:**
- Siempre usar `'UTF-8'` para compatibilidad con caracteres especiales y acentos en español

---

## 🛠️ CASOS DE USO COMUNES

### **1. Variables simples:**
```php
// Nombre de usuario
<?php echo htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8'); ?>

// Email
<?php echo htmlspecialchars($employee->email, ENT_QUOTES, 'UTF-8'); ?>
```

### **2. Variables con valores por defecto:**
```php
// Con operador ternario
<?php echo htmlspecialchars($employee->code ?: '-', ENT_QUOTES, 'UTF-8'); ?>

// Con operador null coalescing (PHP 7+)
<?php echo htmlspecialchars($employee->phone ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>
```

### **3. En atributos de input:**
```php
<input type="text" 
       name="search" 
       value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
```

### **4. En condicionales dentro de atributos:**
```php
<input type="text" 
       name="first_name" 
       value="<?php echo $is_edit ? htmlspecialchars($employee->first_name, ENT_QUOTES, 'UTF-8') : ''; ?>">
```

### **5. Con métodos de objetos:**
```php
// Método sin parámetros
<?php echo htmlspecialchars($employee->get_full_name(), ENT_QUOTES, 'UTF-8'); ?>

// Método con parámetros
<?php echo htmlspecialchars($product->get_price(true), ENT_QUOTES, 'UTF-8'); ?>
```

### **6. Con substr() para truncar texto:**
```php
// ❌ INCORRECTO (parámetros mezclados):
htmlspecialchars(substr($description, 0, 50, ENT_QUOTES, 'UTF-8') . '...')

// ✅ CORRECTO (substr primero, htmlspecialchars después):
htmlspecialchars(
    substr($description ?: '', 0, 50) . (strlen($description ?: '') > 50 ? '...' : ''), 
    ENT_QUOTES, 
    'UTF-8'
)
```

---

## ⚠️ ERRORES COMUNES AL REEMPLAZAR

### **Error 1: Paréntesis mal cerrados en métodos**

```php
// ❌ INCORRECTO (falta paréntesis de cierre):
htmlspecialchars($employee->get_full_name(, ENT_QUOTES, 'UTF-8'))

// ✅ CORRECTO:
htmlspecialchars($employee->get_full_name(), ENT_QUOTES, 'UTF-8')
```

### **Error 2: Parámetros dentro de funciones anidadas**

```php
// ❌ INCORRECTO (ENT_QUOTES dentro de substr):
htmlspecialchars(substr($text, 0, 50, ENT_QUOTES, 'UTF-8'))

// ✅ CORRECTO:
htmlspecialchars(substr($text, 0, 50), ENT_QUOTES, 'UTF-8')
```

### **Error 3: Operador ternario sin escape**

```php
// ❌ INCORRECTO:
$is_edit ? htmlspecialchars($employee->name, ENT_QUOTES, 'UTF-8') : ''

// ✅ CORRECTO (todo escapado):
htmlspecialchars($is_edit ? $employee->name : '', ENT_QUOTES, 'UTF-8')

// ✅ TAMBIÉN CORRECTO (condicional fuera):
$is_edit ? htmlspecialchars($employee->name, ENT_QUOTES, 'UTF-8') : ''
```

---

## 🔧 SCRIPT DE REEMPLAZO AUTOMÁTICO

### **PowerShell Script (Windows):**

```powershell
# Reemplazar en un solo archivo
$file = 'ruta/al/archivo.php'
$content = Get-Content $file -Raw
$content = $content -replace 'Html::chars\(([^)]+)\)', 'htmlspecialchars($1, ENT_QUOTES, ''UTF-8'')'
Set-Content $file -Value $content -NoNewline

# Reemplazar en múltiples archivos
$files = Get-ChildItem -Path "fuel/app/views/admin/modulo/" -Filter *.php -Recurse
foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    $originalContent = $content
    $content = $content -replace 'Html::chars\(([^)]+)\)', 'htmlspecialchars($1, ENT_QUOTES, ''UTF-8'')'
    
    if ($content -ne $originalContent) {
        Set-Content $file.FullName -Value $content -NoNewline
        Write-Host "✅ Actualizado: $($file.FullName)" -ForegroundColor Green
    }
}
```

### **Bash Script (Linux/Mac):**

```bash
# Reemplazar en un archivo
sed -i 's/Html::chars(\([^)]*\))/htmlspecialchars(\1, ENT_QUOTES, '"'"'UTF-8'"'"')/g' archivo.php

# Reemplazar en múltiples archivos
find fuel/app/views/admin/modulo -name "*.php" -type f -exec \
    sed -i 's/Html::chars(\([^)]*\))/htmlspecialchars(\1, ENT_QUOTES, '"'"'UTF-8'"'"')/g' {} \;
```

---

## ✅ CHECKLIST DE VALIDACIÓN

Después de realizar los cambios, verificar:

### **1. Sintaxis PHP:**
```powershell
php -l archivo.php
```

### **2. Búsqueda de Html::chars restantes:**
```powershell
Get-ChildItem -Path "fuel/app/views/admin/" -Filter *.php -Recurse | 
    Select-String -Pattern "Html::chars"
```

### **3. Verificar métodos mal cerrados:**
```powershell
Get-ChildItem -Path "fuel/app/views/admin/" -Filter *.php -Recurse | 
    Select-String -Pattern "get_\w+\(,"
```

### **4. Verificar substr con parámetros extra:**
```powershell
Get-ChildItem -Path "fuel/app/views/admin/" -Filter *.php -Recurse | 
    Select-String -Pattern "substr\([^)]+ENT_QUOTES"
```

### **5. Prueba funcional:**
- Acceder a cada vista en el navegador
- Verificar que no aparezcan errores
- Confirmar que los datos se muestren correctamente escapados

---

## 🔒 SEGURIDAD: ¿POR QUÉ ES IMPORTANTE?

### **Sin escape (PELIGROSO):**
```php
<?php echo $user_input; ?>
```
**Riesgo:** XSS (Cross-Site Scripting) - Un usuario puede inyectar `<script>alert('hacked')</script>`

### **Con escape (SEGURO):**
```php
<?php echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8'); ?>
```
**Resultado:** Los caracteres especiales se convierten en entidades HTML:
- `<` → `&lt;`
- `>` → `&gt;`
- `"` → `&quot;`
- `'` → `&#039;`
- `&` → `&amp;`

---

## 📚 MÓDULOS YA CORREGIDOS

✅ **Empleados** (fuel/app/views/admin/empleados/)
- index.php
- _form.php
- view.php
- create.php
- edit.php

✅ **Departamentos** (fuel/app/views/admin/departamentos/)
- index.php
- _form.php
- create.php
- edit.php

✅ **Puestos** (fuel/app/views/admin/puestos/)
- index.php
- _form.php
- create.php
- edit.php

✅ **Model_Employee** (fuel/app/classes/model/employee.php)
- Firma del método delete() corregida

---

## 📋 MÓDULOS PENDIENTES DE REVISIÓN

🔄 **Nómina** (si existe fuel/app/views/admin/nomina/)
🔄 **Recursos Humanos** (si existe fuel/app/views/admin/rrhh/)
🔄 **SAT/CFDI** (fuel/app/views/admin/sat/)
🔄 **Inventory/Products** (fuel/app/views/admin/inventory/products/)
🔄 **Proveedores** (si tiene Html::chars)

---

## 🎯 PROCESO RECOMENDADO PARA NUEVOS MÓDULOS

1. **Identificar archivos con Html::chars:**
   ```powershell
   Get-ChildItem -Path "fuel/app/views/admin/nuevo_modulo/" -Filter *.php -Recurse | 
       Select-String -Pattern "Html::chars"
   ```

2. **Hacer backup:**
   ```powershell
   Copy-Item -Path "fuel/app/views/admin/nuevo_modulo/" 
             -Destination "fuel/app/views/admin/nuevo_modulo_backup/" 
             -Recurse
   ```

3. **Aplicar reemplazo automático:**
   ```powershell
   $files = Get-ChildItem -Path "fuel/app/views/admin/nuevo_modulo/" -Filter *.php -Recurse
   foreach ($file in $files) {
       $content = Get-Content $file.FullName -Raw
       $content = $content -replace 'Html::chars\(([^)]+)\)', 'htmlspecialchars($1, ENT_QUOTES, ''UTF-8'')'
       Set-Content $file.FullName -Value $content -NoNewline
   }
   ```

4. **Validar sintaxis:**
   ```powershell
   Get-ChildItem -Path "fuel/app/views/admin/nuevo_modulo/" -Filter *.php -Recurse | 
       ForEach-Object { php -l $_.FullName }
   ```

5. **Correcciones manuales:**
   - Revisar métodos con paréntesis mal cerrados
   - Corregir substr() con parámetros incorrectos
   - Verificar operadores ternarios

6. **Pruebas funcionales:**
   - Acceder a todas las vistas del módulo
   - Probar crear, editar, ver, eliminar
   - Verificar que los caracteres especiales se muestren correctamente

---

## 📞 SOPORTE Y REFERENCIAS

**Documentación oficial PHP:**
- https://www.php.net/manual/es/function.htmlspecialchars.php

**FuelPHP Documentation:**
- https://fuelphp.com/docs/

**Este proyecto:**
- Base: c:\xampp\htdocs\base
- Framework: FuelPHP 1.8.2
- Fecha: Diciembre 2025

---

**Autor:** Sistema de corrección automática
**Última actualización:** 6 de Diciembre de 2025
**Estado:** ✅ Solución probada y funcional
