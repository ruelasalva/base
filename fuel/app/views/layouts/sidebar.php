<?php
/**
 * Sidebar Layout Component
 * Menú lateral del sistema - SIEMPRE VISIBLE
 */
?>
<div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar" style="display: block !important; visibility: visible !important;">
    <div class="sidebar-header border-bottom">
        <div class="sidebar-brand d-flex align-items-center justify-content-center">
            <svg class="sidebar-brand-full" width="110" height="32" alt="Logo">
                <text x="0" y="24" fill="currentColor" font-size="20" font-weight="bold">Multi-Tenant ERP</text>
            </svg>
            <svg class="sidebar-brand-narrow" width="32" height="32" alt="Logo">
                <text x="8" y="24" fill="currentColor" font-size="20" font-weight="bold">MT</text>
            </svg>
        </div>
    </div>

    <ul class="sidebar-nav" data-simplebar>
        <!-- Dashboard -->
        <li class="nav-item">
            <a class="nav-link <?php echo (Uri::segment(2) == '' || Uri::segment(2) == 'index') ? 'active' : ''; ?>" href="<?php echo Uri::create('admin'); ?>">
                <i class="nav-icon fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>

        <li class="nav-divider"></li>
        <li class="nav-title">Configuración</li>

        <!-- Gestión de Módulos -->
        <?php if (Helper_Permission::can('modules', 'view')): ?>
        <li class="nav-item">
            <a class="nav-link <?php echo (Uri::segment(2) == 'modules') ? 'active' : ''; ?>" href="<?php echo Uri::create('admin/modules'); ?>">
                <i class="nav-icon fas fa-cubes"></i> Módulos
            </a>
        </li>
        <?php endif; ?>

        <!-- Usuarios -->
        <?php if (Helper_Permission::can('users', 'view')): ?>
        <li class="nav-group">
            <a class="nav-link nav-group-toggle" href="#">
                <i class="nav-icon fas fa-users"></i> Usuarios y Roles
            </a>
            <ul class="nav-group-items compact">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo Uri::create('admin/users'); ?>">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span> Usuarios
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo Uri::create('admin/roles'); ?>">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span> Roles
                    </a>
                </li>
            </ul>
        </li>
        <?php endif; ?>

        <li class="nav-divider"></li>

        <!-- Módulos Activos -->
        <?php
        $tenant_id = Session::get('tenant_id', 1);
        $active_modules = Helper_Module::get_active_modules($tenant_id);
        
        foreach ($active_modules as $mod) {
            if ($mod['category'] != 'core' && isset($mod['menu_items']) && !empty($mod['menu_items'])) {
                ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo Uri::create('admin/'.strtolower($mod['name'])); ?>">
                        <i class="nav-icon <?php echo $mod['icon']; ?>"></i> <?php echo $mod['display_name']; ?>
                    </a>
                </li>
                <?php
            }
        }
        ?>

        <!-- Almacenes (si existe el módulo) -->
        <li class="nav-item">
            <a class="nav-link <?php echo (Uri::segment(2) == 'almacenes') ? 'active' : ''; ?>" href="<?php echo Uri::create('admin/almacenes'); ?>">
                <i class="nav-icon fas fa-warehouse"></i> Almacenes
            </a>
        </li>

        <!-- Proveedores (si existe el módulo) -->
        <?php if (Helper_Permission::can('proveedores', 'view')): ?>
        <li class="nav-item">
            <a class="nav-link <?php echo (Uri::segment(2) == 'proveedores') ? 'active' : ''; ?>" href="<?php echo Uri::create('admin/proveedores'); ?>">
                <i class="nav-icon fas fa-truck"></i> Proveedores
            </a>
        </li>
        <?php endif; ?>

        <li class="nav-divider"></li>

        <!-- Sistema -->
        <li class="nav-title">Sistema</li>
        <?php if (Helper_Permission::can('logs', 'view')): ?>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo Uri::create('admin/logs'); ?>">
                <i class="nav-icon fas fa-file-alt"></i> Logs
            </a>
        </li>
        <?php endif; ?>

        <!-- Cerrar Sesión -->
        <li class="nav-item">
            <a class="nav-link" href="<?php echo Uri::create('admin/logout'); ?>">
                <i class="nav-icon fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </li>
    </ul>
</div>

<style>
/* ==========================================
   FORZAR SIDEBAR SIEMPRE VISIBLE Y EXPANDIDO
   ========================================== */
#sidebar,
.sidebar {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    position: fixed !important;
    top: 0;
    left: 0;
    bottom: 0;
    width: 256px !important;
    min-width: 256px !important;
    max-width: 256px !important;
    z-index: 1000;
    background-color: #2c3e50 !important;
    transform: translateX(0) !important;
}

/* Quitar clases que colapsan el sidebar */
.sidebar.sidebar-narrow,
.sidebar.sidebar-narrow-unfoldable,
.sidebar.sidebar-minimized {
    width: 256px !important;
    min-width: 256px !important;
}

.sidebar .sidebar-brand-narrow {
    display: none !important;
}

.sidebar .sidebar-brand-full {
    display: block !important;
}

/* Asegurar que el wrapper tenga padding para el sidebar */
.wrapper {
    padding-left: 256px !important;
    transition: padding-left 0.15s;
}

@media (max-width: 991.98px) {
    .wrapper {
        padding-left: 0 !important;
    }
    #sidebar {
        transform: translateX(-100%) !important;
    }
    #sidebar.show {
        transform: translateX(0) !important;
    }
}

/* ==========================================
   JERARQUÍA VISUAL: CATEGORÍAS VS MÓDULOS
   ========================================== */

/* CATEGORÍAS - Fondo más oscuro, borde izquierdo verde, bold, uppercase */
.sidebar-nav .nav-title {
    padding: 0.875rem 1rem !important;
    margin: 0.5rem 0 0.25rem 0 !important;
    font-size: 0.75rem !important;
    font-weight: 700 !important;
    color: rgba(255, 255, 255, 0.6) !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
    background-color: rgba(0, 0, 0, 0.25) !important;
    border-left: 3px solid #4CAF50 !important;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* DIVISORES - Línea sutil */
.nav-divider {
    height: 1px;
    margin: 0.75rem 1rem;
    background: linear-gradient(90deg, 
        rgba(255, 255, 255, 0) 0%, 
        rgba(255, 255, 255, 0.15) 50%, 
        rgba(255, 255, 255, 0) 100%
    );
}

/* MÓDULOS - Fondo transparente, indent, color más claro */
.sidebar-nav .nav-item {
    margin: 0;
}

.sidebar-nav .nav-item .nav-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem 0.75rem 1.5rem !important;
    color: rgba(255, 255, 255, 0.8) !important;
    background-color: transparent !important;
    text-decoration: none;
    transition: all 0.25s ease;
    border-left: 3px solid transparent;
}

/* MÓDULOS - Hover State */
.sidebar-nav .nav-item .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1) !important;
    color: #ffffff !important;
    border-left-color: #2196F3 !important;
    padding-left: 1.75rem !important;
}

/* MÓDULOS - Active State */
.sidebar-nav .nav-item .nav-link.active {
    background-color: rgba(76, 175, 80, 0.2) !important;
    color: #ffffff !important;
    border-left-color: #4CAF50 !important;
    font-weight: 600 !important;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.15);
}

/* Iconos de navegación */
.nav-icon {
    width: 2rem;
    margin-right: 0.75rem;
    text-align: center;
    font-size: 1rem;
}

/* Grupos de navegación (submenús) */
.nav-group .nav-group-toggle {
    padding: 0.75rem 1rem !important;
    color: rgba(255, 255, 255, 0.8) !important;
    background-color: rgba(255, 255, 255, 0.05) !important;
    border-left: 3px solid transparent;
}

.nav-group .nav-group-toggle:hover {
    background-color: rgba(255, 255, 255, 0.1) !important;
    color: #ffffff !important;
    border-left-color: #FF9800 !important;
}

.nav-group-toggle::after {
    content: '\f078';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    margin-left: auto;
    transition: transform 0.3s;
    font-size: 0.75rem;
}

.nav-group.show .nav-group-toggle::after {
    transform: rotate(180deg);
}

/* Submenú items */
.nav-group-items {
    display: none;
    background-color: rgba(0, 0, 0, 0.15);
    padding: 0.25rem 0;
}

.nav-group.show .nav-group-items {
    display: block;
}

.nav-group-items .nav-item .nav-link {
    padding-left: 3rem !important;
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.7) !important;
}

.nav-group-items .nav-item .nav-link:hover {
    padding-left: 3.25rem !important;
    background-color: rgba(255, 255, 255, 0.08) !important;
}

/* Header del sidebar */
.sidebar-header {
    padding: 1rem;
    background-color: rgba(0, 0, 0, 0.2);
}

.sidebar-brand {
    padding: 0.5rem 0;
}

/* Scroll del sidebar */
.sidebar-nav[data-simplebar] {
    max-height: calc(100vh - 80px);
}
</style>

<script>
/* ==========================================
   FORZAR SIDEBAR EXPANDIDO DESPUÉS DE COREUI
   ========================================== */
document.addEventListener('DOMContentLoaded', function() {
    // Función para forzar sidebar expandido
    function forceExpandSidebar() {
        const sidebar = document.getElementById('sidebar');
        const wrapper = document.querySelector('.wrapper');
        
        if (sidebar) {
            // Remover clases que colapsan el sidebar
            sidebar.classList.remove('sidebar-narrow');
            sidebar.classList.remove('sidebar-narrow-unfoldable');
            sidebar.classList.remove('sidebar-minimized');
            sidebar.classList.add('sidebar-show');
            
            // Forzar estilos inline (mayor prioridad que JavaScript de CoreUI)
            sidebar.style.cssText = `
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                width: 256px !important;
                min-width: 256px !important;
                transform: translateX(0) !important;
            `;
            
            // Ajustar wrapper
            if (wrapper) {
                wrapper.style.paddingLeft = '256px';
            }
            
            console.log('✓ Sidebar forzado a estar expandido');
        }
    }
    
    // Ejecutar inmediatamente
    forceExpandSidebar();
    
    // Ejecutar después de 100ms (cuando CoreUI se inicializa)
    setTimeout(forceExpandSidebar, 100);
    
    // Ejecutar después de 500ms (para asegurar)
    setTimeout(forceExpandSidebar, 500);
    
    // Ejecutar después de 1000ms (por si hay carga lenta)
    setTimeout(forceExpandSidebar, 1000);
    
    // Observar cambios en el sidebar y revertirlos
    const sidebar = document.getElementById('sidebar');
    if (sidebar && window.MutationObserver) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    const target = mutation.target;
                    if (target.classList.contains('sidebar-narrow') || 
                        target.classList.contains('sidebar-minimized')) {
                        console.log('⚠ Detectado intento de colapsar sidebar, revirtiendo...');
                        forceExpandSidebar();
                    }
                }
            });
        });
        
        observer.observe(sidebar, {
            attributes: true,
            attributeFilter: ['class', 'style']
        });
    }
    
    // Toggle de grupos de navegación
    const groupToggles = document.querySelectorAll('.nav-group-toggle');
    groupToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const group = this.closest('.nav-group');
            group.classList.toggle('show');
        });
    });
});

// También ejecutar cuando la ventana cargue completamente
window.addEventListener('load', function() {
    setTimeout(function() {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            sidebar.style.cssText = `
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                width: 256px !important;
                transform: translateX(0) !important;
            `;
        }
    }, 100);
});
</script>
