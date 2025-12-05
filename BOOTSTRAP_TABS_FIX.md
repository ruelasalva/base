# SOLUCIÓN: TABS DE BOOTSTRAP NO FUNCIONAN

## PROBLEMA
Las pestañas (tabs) de Bootstrap 5 no funcionan en algunos módulos aunque el HTML esté correcto.

## CAUSA RAÍZ
El proyecto usa una versión de Bootstrap o la librería JS no está completamente cargada, o hay conflictos entre Bootstrap 4 y Bootstrap 5. El objeto `bootstrap.Tab` no está disponible o no funciona correctamente.

## SÍNTOMAS
- Los tabs se muestran visualmente correctos
- Al hacer clic en las pestañas, no cambia el contenido
- Error en consola: `bootstrap.Tab is not a constructor` o similar
- Los atributos `data-bs-toggle="tab"` no funcionan

## SOLUCIÓN APLICADA

### ❌ CÓDIGO INCORRECTO (No usar)
```javascript
// Esto NO funciona en este proyecto
var triggerTabList = [].slice.call(document.querySelectorAll('#orderTabs button'));
triggerTabList.forEach(function (triggerEl) {
    var tabTrigger = new bootstrap.Tab(triggerEl);
    
    triggerEl.addEventListener('click', function (event) {
        event.preventDefault();
        tabTrigger.show();
    });
});
```

### ✅ CÓDIGO CORRECTO (Usar siempre)
```javascript
// Implementación manual de tabs - Compatible con Bootstrap 4/5
document.addEventListener('DOMContentLoaded', function() {
    var tabButtons = document.querySelectorAll('#orderTabs button[data-bs-target]')
    
    if (tabButtons.length > 0) {
        tabButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault()
                
                // Desactivar todos los tabs
                tabButtons.forEach(function(btn) {
                    btn.classList.remove('active')
                    var targetId = btn.getAttribute('data-bs-target')
                    var targetPane = document.querySelector(targetId)
                    if (targetPane) {
                        targetPane.classList.remove('show', 'active')
                    }
                })
                
                // Activar el tab clickeado
                button.classList.add('active')
                var targetId = button.getAttribute('data-bs-target')
                var targetPane = document.querySelector(targetId)
                if (targetPane) {
                    targetPane.classList.add('show', 'active')
                }
            })
        })
    }
})
```

## ARCHIVOS QUE DEBEN USAR ESTA SOLUCIÓN

### ✅ Ya corregidos:
- `/fuel/app/views/admin/proveedores/form.php`
- `/fuel/app/views/admin/proveedores/view.php`
- `/fuel/app/views/admin/productos/form.php`
- `/fuel/app/views/admin/ordenescompra/form.php` ✅ **Corregido 5-dic-2025**
- `/fuel/app/views/admin/ordenescompra/view.php` ✅ **Corregido 5-dic-2025**

### ℹ️ Archivos sin tabs (no requieren corrección):
- `/fuel/app/views/admin/ordenescompra/index.php` (sin tabs)

## PATRÓN A SEGUIR

Cada vez que crees un módulo con tabs de Bootstrap:

1. **HTML**: Usar atributos `data-bs-target` y `data-bs-toggle="tab"`
2. **JavaScript**: NO usar `new bootstrap.Tab()`, usar la implementación manual
3. **Selector**: Asegurarse de usar el ID correcto del contenedor de tabs
4. **Clases**: Manejar manualmente `active`, `show` en buttons y tab-panes

## VENTAJAS DE ESTA SOLUCIÓN

✅ Compatible con Bootstrap 4 y 5  
✅ No depende de la librería JS de Bootstrap  
✅ Funciona aunque falte bootstrap.bundle.js  
✅ Más control sobre el comportamiento  
✅ Sin dependencias externas  

## EJEMPLO COMPLETO

```html
<!-- HTML -->
<ul class="nav nav-tabs" id="myTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab1" type="button">
            Tab 1
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab2" type="button">
            Tab 2
        </button>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="tab1" role="tabpanel">
        Contenido Tab 1
    </div>
    <div class="tab-pane fade" id="tab2" role="tabpanel">
        Contenido Tab 2
    </div>
</div>

<script>
// JavaScript - Copiar este código exacto
document.addEventListener('DOMContentLoaded', function() {
    var tabButtons = document.querySelectorAll('#myTabs button[data-bs-target]')
    
    if (tabButtons.length > 0) {
        tabButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault()
                
                // Desactivar todos
                tabButtons.forEach(function(btn) {
                    btn.classList.remove('active')
                    var targetId = btn.getAttribute('data-bs-target')
                    var targetPane = document.querySelector(targetId)
                    if (targetPane) {
                        targetPane.classList.remove('show', 'active')
                    }
                })
                
                // Activar el actual
                button.classList.add('active')
                var targetId = button.getAttribute('data-bs-target')
                var targetPane = document.querySelector(targetId)
                if (targetPane) {
                    targetPane.classList.add('show', 'active')
                }
            })
        })
    }
})
</script>
```

## NOTAS IMPORTANTES

⚠️ **Siempre** usar el ID correcto del contenedor de tabs en el selector  
⚠️ **No mezclar** Bootstrap 4 (`data-toggle`) con Bootstrap 5 (`data-bs-toggle`)  
⚠️ **Verificar** que los IDs de `data-bs-target` coincidan con los IDs de los `tab-pane`  

---
Última actualización: 5 de diciembre de 2025
