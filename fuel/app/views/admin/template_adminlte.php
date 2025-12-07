<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo isset($title) ? $title : 'Admin'; ?> - Sistema Multi-Tenant</title>

    <!-- Favicon -->
    <link rel="icon" href="<?php echo Uri::base(false).'assets/img/admin/favicon.png'; ?>" type="image/png">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    
    <!-- Chart.js -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">

    <style>
        .brand-link {
            border-bottom: 1px solid #4b545c;
        }
        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link.active {
            background-color: #007bff;
            color: #fff;
        }
        .nav-sidebar .nav-link {
            border-radius: 0.25rem;
            margin-bottom: 0.25rem;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- Tenant Info -->
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-building"></i>
                    <span class="d-none d-md-inline-block">Tenant #<?php echo Session::get('tenant_id', 1); ?></span>
                </a>
            </li>

            <!-- User Dropdown Menu -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="fas fa-user-circle fa-lg"></i>
                    <span class="d-none d-md-inline-block ml-1"><?php echo Auth::get('username'); ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item dropdown-header">
                        <?php echo Auth::get('email'); ?>
                    </span>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-user mr-2"></i> Perfil
                    </a>
                    <a href="<?php echo Uri::create('admin/configuracion/templates'); ?>" class="dropdown-item">
                        <i class="fas fa-palette mr-2"></i> Cambiar Template
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="<?php echo Uri::create('admin/logout'); ?>" class="dropdown-item">
                        <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                    </a>
                </div>
            </li>
        </ul>
    </nav>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="<?php echo Uri::create('admin'); ?>" class="brand-link">
            <span class="brand-text font-weight-light"><strong>Multi-Tenant</strong> ERP</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a href="<?php echo Uri::create('admin'); ?>" 
                           class="nav-link <?php echo (Uri::segment(2) == '' || Uri::segment(2) == 'index') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-header">CONFIGURACIÓN</li>

                    <!-- Gestión de Módulos -->
                    <?php if (Helper_Permission::can('modules', 'view')): ?>
                    <li class="nav-item">
                        <a href="<?php echo Uri::create('admin/modules'); ?>" 
                           class="nav-link <?php echo (Uri::segment(2) == 'modules') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-cubes"></i>
                            <p>Gestión de Módulos</p>
                        </a>
                    </li>
                    <?php endif; ?>

                    <!-- Configuración del Sitio -->
                    <?php if (Helper_Permission::can('config', 'view')): ?>
                    <li class="nav-item">
                        <a href="<?php echo Uri::create('admin/configuracion'); ?>" 
                           class="nav-link <?php echo (Uri::segment(2) == 'configuracion') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Configuración Sitio</p>
                        </a>
                    </li>
                    <?php endif; ?>

                    <!-- Usuarios y Roles -->
                    <?php if (Helper_Permission::can('users', 'view') || Helper_Permission::can('roles', 'view')): ?>
                    <li class="nav-header">USUARIOS Y ROLES</li>
                    
                    <?php if (Helper_Permission::can('users', 'view')): ?>
                    <li class="nav-item">
                        <a href="<?php echo Uri::create('admin/users'); ?>" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Usuarios</p>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if (Helper_Permission::can('roles', 'view')): ?>
                    <li class="nav-item">
                        <a href="<?php echo Uri::create('admin/roles'); ?>" class="nav-link">
                            <i class="nav-icon fas fa-user-tag"></i>
                            <p>Roles y Permisos</p>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php endif; ?>

                    <?php
                    // CARGAR MÓDULOS ACTIVOS DINÁMICAMENTE POR CATEGORÍA
                    $tenant_id = Session::get('tenant_id', 1);
                    $active_modules = Helper_Module::get_active_modules($tenant_id);
                    
                    // Agrupar por categoría (excluyendo módulos core)
                    $modules_by_cat = [];
                    foreach ($active_modules as $mod) {
                        $cat = $mod['category'];
                        // Excluir categoría 'core' del menú dinámico
                        if ($cat !== 'core') {
                            if (!isset($modules_by_cat[$cat])) {
                                $modules_by_cat[$cat] = [];
                            }
                            $modules_by_cat[$cat][] = $mod;
                        }
                    }
                    
                    // Nombres y orden de categorías
                    $categories = [
                        'contabilidad' => ['name' => 'Contabilidad', 'icon' => 'fa-calculator', 'color' => 'primary'],
                        'finanzas' => ['name' => 'Finanzas', 'icon' => 'fa-dollar-sign', 'color' => 'success'],
                        'compras' => ['name' => 'Compras', 'icon' => 'fa-truck', 'color' => 'warning'],
                        'inventario' => ['name' => 'Inventario', 'icon' => 'fa-boxes', 'color' => 'info'],
                        'sales' => ['name' => 'Ventas', 'icon' => 'fa-shopping-cart', 'color' => 'danger'],
                        'rrhh' => ['name' => 'Recursos Humanos', 'icon' => 'fa-users-cog', 'color' => 'secondary'],
                        'marketing' => ['name' => 'Marketing', 'icon' => 'fa-bullhorn', 'color' => 'purple'],
                        'backend' => ['name' => 'Backend & Portales', 'icon' => 'fa-server', 'color' => 'dark'],
                        'integraciones' => ['name' => 'Integraciones', 'icon' => 'fa-plug', 'color' => 'info'],
                        'system' => ['name' => 'Sistema', 'icon' => 'fa-gears', 'color' => 'secondary']
                    ];
                    
                    // Mostrar módulos por categoría
                    foreach ($categories as $cat_key => $cat_info):
                        if (isset($modules_by_cat[$cat_key]) && count($modules_by_cat[$cat_key]) > 0):
                    ?>
                    <li class="nav-header"><?php echo strtoupper($cat_info['name']); ?></li>
                    <?php
                        foreach ($modules_by_cat[$cat_key] as $module):
                            $route = 'admin/' . $module['name'];
                    ?>
                    <li class="nav-item">
                        <a href="<?php echo Uri::create($route); ?>" 
                           class="nav-link <?php echo (Uri::segment(2) == $module['name']) ? 'active' : ''; ?>">
                            <i class="nav-icon fas <?php echo $module['icon']; ?> text-<?php echo $cat_info['color']; ?>"></i>
                            <p><?php echo $module['display_name']; ?></p>
                        </a>
                    </li>
                    <?php
                        endforeach;
                        endif;
                    endforeach;
                    ?>

                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <!-- Flash Messages -->
                <?php if(Session::get_flash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-check"></i> ¡Éxito!</h5>
                    <?php echo Session::get_flash('success'); ?>
                </div>
                <?php endif; ?>

                <?php if(Session::get_flash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-ban"></i> ¡Error!</h5>
                    <?php echo Session::get_flash('error'); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <?php echo isset($content) ? $content : ''; ?>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">Multi-Tenant ERP</a>.</strong>
        Todos los derechos reservados.
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 1.0.0
        </div>
    </footer>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);

    // Initialize AdminLTE components
    $(document).ready(function() {
        // TreeView menu
        $('[data-widget="treeview"]').Treeview('init');
    });
</script>

</body>
</html>
