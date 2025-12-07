<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo isset($title) ? $title : 'Admin'; ?> - Sistema Multi-Tenant</title>

    <!-- Favicon -->
    <link rel="icon" href="<?php echo Uri::base(false).'assets/img/admin/favicon.png'; ?>" type="image/png">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Argon Dashboard CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">

    <style>
        :root {
            --argon-primary: #5e72e4;
            --argon-secondary: #8965e0;
            --argon-success: #2dce89;
            --argon-info: #11cdef;
            --argon-warning: #fb6340;
            --argon-danger: #f5365c;
            --argon-dark: #172b4d;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            background: linear-gradient(87deg, #11cdef 0, #1171ef 100%);
            min-height: 100vh;
        }

        .sidenav {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background: linear-gradient(87deg, var(--argon-primary) 0, var(--argon-secondary) 100%);
            box-shadow: 0 0 2rem 0 rgba(136, 152, 170, .15);
            z-index: 1000;
            overflow-y: auto;
        }

        .sidenav .navbar-brand {
            padding: 1.5rem 1rem;
            text-align: center;
            color: white;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .sidenav .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
            transition: all 0.15s ease;
            border-radius: 0.375rem;
            margin: 0.25rem 0.75rem;
        }

        .sidenav .nav-link:hover,
        .sidenav .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .sidenav .nav-link i {
            margin-right: 0.75rem;
            font-size: 1rem;
        }

        .sidenav .nav-divider {
            height: 1px;
            margin: 1rem 1.5rem;
            background: rgba(255, 255, 255, 0.1);
        }

        .sidenav .nav-header {
            padding: 0.75rem 1.5rem 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .main-content {
            margin-left: 250px;
            min-height: 100vh;
        }

        .navbar-top {
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
            padding: 1rem 1.5rem;
        }

        .page-header {
            background: linear-gradient(87deg, var(--argon-primary) 0, var(--argon-secondary) 100%);
            padding: 2rem 0 3rem;
            margin-bottom: -2rem;
            color: white;
        }

        .container-fluid {
            padding: 2rem 1.5rem;
        }

        .card {
            border: 0;
            box-shadow: 0 0 2rem 0 rgba(136, 152, 170, .15);
            border-radius: 0.375rem;
            margin-bottom: 1.5rem;
        }

        .card-header {
            background: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.25rem 1.5rem;
        }

        .btn {
            font-size: 0.875rem;
            padding: 0.625rem 1.25rem;
            border-radius: 0.375rem;
            font-weight: 600;
            letter-spacing: 0.025em;
            text-transform: uppercase;
        }

        .alert {
            border: 0;
            border-radius: 0.375rem;
            box-shadow: 0 0 2rem 0 rgba(136, 152, 170, .15);
        }

        @media (max-width: 991.98px) {
            .sidenav {
                transform: translateX(-250px);
                transition: transform 0.3s ease;
            }
            .sidenav.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    
    <!-- Sidenav -->
    <nav class="sidenav" id="sidenav-main">
        <div class="navbar-brand">
            <i class="fas fa-layer-group"></i> <strong>Multi-Tenant</strong>
        </div>

        <ul class="nav flex-column">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link <?php echo (Uri::segment(2) == '' || Uri::segment(2) == 'index') ? 'active' : ''; ?>" 
                   href="<?php echo Uri::create('admin'); ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>

            <div class="nav-divider"></div>
            <div class="nav-header">Configuración</div>

            <!-- Gestión de Módulos -->
            <?php if (Helper_Permission::can('modules', 'view')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo (Uri::segment(2) == 'modules') ? 'active' : ''; ?>" 
                   href="<?php echo Uri::create('admin/modules'); ?>">
                    <i class="fas fa-cubes"></i> Gestión de Módulos
                </a>
            </li>
            <?php endif; ?>

            <!-- Configuración del Sitio -->
            <?php if (Helper_Permission::can('config', 'view')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo (Uri::segment(2) == 'configuracion') ? 'active' : ''; ?>" 
                   href="<?php echo Uri::create('admin/configuracion'); ?>">
                    <i class="fas fa-cog"></i> Configuración Sitio
                </a>
            </li>
            <?php endif; ?>

            <!-- Usuarios y Roles -->
            <?php if (Helper_Permission::can('users', 'view') || Helper_Permission::can('roles', 'view')): ?>
            <div class="nav-divider"></div>
            <div class="nav-header">Usuarios y Roles</div>
            
            <?php if (Helper_Permission::can('users', 'view')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo Uri::create('admin/users'); ?>">
                    <i class="fas fa-users"></i> Usuarios
                </a>
            </li>
            <?php endif; ?>

            <?php if (Helper_Permission::can('roles', 'view')): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo Uri::create('admin/roles'); ?>">
                    <i class="fas fa-user-tag"></i> Roles y Permisos
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
                'contabilidad' => ['name' => 'Contabilidad', 'icon' => 'fa-calculator'],
                'finanzas' => ['name' => 'Finanzas', 'icon' => 'fa-dollar-sign'],
                'compras' => ['name' => 'Compras', 'icon' => 'fa-truck'],
                'inventario' => ['name' => 'Inventario', 'icon' => 'fa-boxes'],
                'sales' => ['name' => 'Ventas', 'icon' => 'fa-shopping-cart'],
                'rrhh' => ['name' => 'Recursos Humanos', 'icon' => 'fa-users-cog'],
                'marketing' => ['name' => 'Marketing', 'icon' => 'fa-bullhorn'],
                'backend' => ['name' => 'Backend & Portales', 'icon' => 'fa-server'],
                'integraciones' => ['name' => 'Integraciones', 'icon' => 'fa-plug'],
                'system' => ['name' => 'Sistema', 'icon' => 'fa-gears']
            ];
            
            // Mostrar módulos por categoría
            foreach ($categories as $cat_key => $cat_info):
                if (isset($modules_by_cat[$cat_key]) && count($modules_by_cat[$cat_key]) > 0):
            ?>
            <div class="nav-divider"></div>
            <div class="nav-header"><?php echo $cat_info['name']; ?></div>
            <?php
                foreach ($modules_by_cat[$cat_key] as $module):
                    $route = 'admin/' . $module['name'];
            ?>
            <li class="nav-item">
                <a class="nav-link <?php echo (Uri::segment(2) == $module['name']) ? 'active' : ''; ?>" 
                   href="<?php echo Uri::create($route); ?>">
                    <i class="fas <?php echo $module['icon']; ?>"></i> <?php echo $module['display_name']; ?>
                </a>
            </li>
            <?php
                endforeach;
                endif;
            endforeach;
            ?>
        </ul>
    </nav>

    <!-- Main content -->
    <div class="main-content">
        
        <!-- Top navbar -->
        <nav class="navbar navbar-top navbar-expand">
            <div class="container-fluid">
                <button class="btn btn-link d-md-none" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>

                <ul class="navbar-nav ms-auto align-items-center">
                    <!-- Tenant -->
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="#" role="button">
                            <i class="fas fa-building"></i>
                            <span class="d-none d-md-inline-block ms-2">Tenant #<?php echo Session::get('tenant_id', 1); ?></span>
                        </a>
                    </li>

                    <!-- User -->
                    <li class="nav-item dropdown">
                        <a class="nav-link pe-0" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user-circle fa-2x"></i>
                                <div class="ms-2 d-none d-md-block">
                                    <span class="mb-0 text-sm fw-bold"><?php echo Auth::get('username'); ?></span>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <div class="dropdown-header">
                                <h6 class="mb-0"><?php echo Auth::get('email'); ?></h6>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user me-2"></i> Perfil
                            </a>
                            <a class="dropdown-item" href="<?php echo Uri::create('admin/configuracion/templates'); ?>">
                                <i class="fas fa-palette me-2"></i> Cambiar Template
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?php echo Uri::create('admin/logout'); ?>">
                                <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Header -->
        <div class="page-header">
            <div class="container-fluid">
                <h2 class="text-white mb-0"><?php echo isset($title) ? $title : 'Dashboard'; ?></h2>
            </div>
        </div>

        <!-- Page content -->
        <div class="container-fluid">
            
            <!-- Flash Messages -->
            <?php if(Session::get_flash('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <span class="alert-icon"><i class="fas fa-check-circle"></i></span>
                <span class="alert-text"><strong>¡Éxito!</strong> <?php echo Session::get_flash('success'); ?></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <?php if(Session::get_flash('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <span class="alert-icon"><i class="fas fa-exclamation-circle"></i></span>
                <span class="alert-text"><strong>¡Error!</strong> <?php echo Session::get_flash('error'); ?></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <!-- Content -->
            <?php echo isset($content) ? $content : ''; ?>

        </div>

        <!-- Footer -->
        <footer class="py-4">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="copyright text-center text-lg-start text-muted">
                            &copy; <?php echo date('Y'); ?> <a href="#" class="fw-bold">Multi-Tenant ERP</a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <ul class="nav nav-footer justify-content-center justify-content-lg-end">
                            <li class="nav-item">
                                <span class="nav-link text-muted">Powered by FuelPHP</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidenav-main').classList.toggle('show');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidenav-main');
            const toggle = document.getElementById('sidebarToggle');
            
            if (window.innerWidth < 992 && 
                sidebar && sidebar.classList.contains('show') && 
                !sidebar.contains(e.target) && 
                !toggle.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });
    </script>

</body>
</html>
