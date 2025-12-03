<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo isset($title) ? $title : 'Admin'; ?> - Sistema Multi-Tenant</title>

    <!-- Favicon -->
    <link rel="icon" href="<?php echo Uri::base(false).'assets/img/admin/favicon.png'; ?>" type="image/png">

    <!-- CoreUI CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@coreui/coreui@5.1.0/dist/css/coreui.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Chart.js -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">

    <style>
        :root {
            --cui-sidebar-width: 256px;
        }
        .sidebar {
            width: var(--cui-sidebar-width);
        }
        .wrapper {
            padding-left: var(--cui-sidebar-width);
        }
        @media (max-width: 991.98px) {
            .wrapper {
                padding-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar sidebar-dark sidebar-fixed sidebar-narrow-unfoldable border-end" id="sidebar">
        <div class="sidebar-header border-bottom">
            <div class="sidebar-brand d-flex align-items-center justify-content-center">
                <svg class="sidebar-brand-full" width="110" height="32" alt="Logo">
                    <text x="0" y="24" fill="currentColor" font-size="20" font-weight="bold">Multi-Tenant</text>
                </svg>
                <svg class="sidebar-brand-narrow" width="32" height="32" alt="Logo">
                    <text x="8" y="24" fill="currentColor" font-size="20" font-weight="bold">MT</text>
                </svg>
            </div>
            <button class="btn-close d-lg-none" type="button" data-coreui-dismiss="sidebar" aria-label="Close"></button>
        </div>

        <ul class="sidebar-nav" data-coreui="navigation" data-simplebar>
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
                    <i class="nav-icon fas fa-cubes"></i> Gestión de Módulos
                </a>
            </li>
            <?php endif; ?>

            <!-- Configuración del Sitio -->
            <?php if (Helper_Permission::can('config', 'view')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo (Uri::segment(2) == 'configuracion') ? 'active' : ''; ?>" href="<?php echo Uri::create('admin/configuracion'); ?>">
                    <i class="nav-icon fas fa-cog"></i> Configuración Sitio
                </a>
            </li>
            <?php endif; ?>

            <!-- Usuarios y Roles -->
            <?php if (Helper_Permission::can('users', 'view') || Helper_Permission::can('roles', 'view')): ?>
            <li class="nav-group">
                <a class="nav-link nav-group-toggle" href="#">
                    <i class="nav-icon fas fa-users"></i> Usuarios y Roles
                </a>
                <ul class="nav-group-items compact">
                    <?php if (Helper_Permission::can('users', 'view')): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo Uri::create('admin/users'); ?>">
                            <span class="nav-icon"><span class="nav-icon-bullet"></span></span> Usuarios
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (Helper_Permission::can('roles', 'view')): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo Uri::create('admin/roles'); ?>">
                            <span class="nav-icon"><span class="nav-icon-bullet"></span></span> Roles
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo Uri::create('admin/permissions'); ?>">
                            <span class="nav-icon"><span class="nav-icon-bullet"></span></span> Permisos
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>

            <li class="nav-divider"></li>

            <?php
            // CARGAR MÓDULOS ACTIVOS DINÁMICAMENTE POR CATEGORÍA
            $tenant_id = Session::get('tenant_id', 1);
            $active_modules = Helper_Module::get_active_modules($tenant_id);
            
            // Agrupar por categoría
            $modules_by_cat = [];
            foreach ($active_modules as $mod) {
                if (!$mod['is_core']) { // Excluir módulos core
                    $cat = $mod['category'];
                    if (!isset($modules_by_cat[$cat])) {
                        $modules_by_cat[$cat] = [];
                    }
                    $modules_by_cat[$cat][] = $mod;
                }
            }
            
            // Nombres y orden de categorías
            $categories = [
                'business' => 'Módulos de Negocio',
                'sales' => 'Ventas y CRM',
                'marketing' => 'Marketing',
                'backend' => 'Backends y APIs',
                'system' => 'Sistema'
            ];
            
            // Mostrar módulos por categoría
            foreach ($categories as $cat_key => $cat_name):
                if (isset($modules_by_cat[$cat_key]) && count($modules_by_cat[$cat_key]) > 0):
            ?>
            <li class="nav-title"><?php echo $cat_name; ?></li>
            <?php
                foreach ($modules_by_cat[$cat_key] as $module):
                    $permission_key = $module['name'];
                    $route = !empty($module['route']) ? $module['route'] : 'admin/' . $module['name'];
                    
                    if (Helper_Permission::can($permission_key, 'view')):
            ?>
            <li class="nav-item">
                <a class="nav-link <?php echo (Uri::segment(2) == $module['name']) ? 'active' : ''; ?>" href="<?php echo Uri::create($route); ?>">
                    <i class="nav-icon fas <?php echo $module['icon']; ?>"></i> <?php echo $module['display_name']; ?>
                </a>
            </li>
            <?php
                    endif;
                endforeach;
                endif;
            endforeach;
            ?>
        </ul>

        <div class="sidebar-footer border-top d-none d-md-flex">
            <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
        </div>
    </div>

    <div class="wrapper d-flex flex-column min-vh-100">
        <!-- Header -->
        <header class="header header-sticky p-0 mb-4">
            <div class="container-fluid border-bottom px-4">
                <button class="header-toggler" type="button" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()">
                    <i class="fas fa-bars"></i>
                </button>
                <ul class="header-nav ms-auto">
                    <!-- Tenant Selector (si tiene acceso a múltiples tenants) -->
                    <li class="nav-item dropdown">
                        <a class="nav-link py-0" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-building"></i>
                            <span class="d-none d-md-inline-block ms-2">Tenant #<?php echo Session::get('tenant_id', 1); ?></span>
                        </a>
                    </li>

                    <!-- User Menu -->
                    <li class="nav-item py-1 dropdown">
                        <a class="nav-link py-0 pe-0" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            <div class="avatar avatar-md">
                                <i class="fas fa-user-circle fa-2x"></i>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end pt-0">
                            <div class="dropdown-header bg-body-tertiary text-body-secondary fw-semibold rounded-top mb-2">
                                Cuenta
                            </div>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user me-2"></i> Perfil
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-cog me-2"></i> Configuración
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?php echo Uri::create('admin/logout'); ?>">
                                <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </header>

        <!-- Content -->
        <div class="body flex-grow-1">
            <div class="container-lg px-4">
                <!-- Flash Messages -->
                <?php if(Session::get_flash('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>¡Éxito!</strong> <?php echo Session::get_flash('success'); ?>
                        <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if(Session::get_flash('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>¡Error!</strong> <?php echo Session::get_flash('error'); ?>
                        <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Page Content -->
                <?php echo isset($content) ? $content : ''; ?>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer px-4 mt-auto">
            <div>
                <a href="https://coreui.io" target="_blank">Multi-Tenant ERP</a>
                <span> &copy; <?php echo date('Y'); ?> Sistema Multi-Tenant.</span>
            </div>
            <div class="ms-auto">
                Powered by <a href="https://fuelphp.com" target="_blank">FuelPHP</a>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@coreui/coreui@5.1.0/dist/js/coreui.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                var bsAlert = new coreui.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-coreui-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new coreui.Tooltip(tooltipTriggerEl)
        });
    </script>
</body>
</html>
