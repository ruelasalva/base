<!-- Page Header -->
<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="mb-0">Dashboard</h2>
        <p class="text-body-secondary"><?php echo isset($date) ? $date : ''; ?></p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-primary">
            <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                <div>
                    <div class="fs-4 fw-semibold"><?php echo isset($stats['users']) ? $stats['users'] : 0; ?></div>
                    <div>Usuarios</div>
                </div>
                <div class="fs-2"><i class="fas fa-users"></i></div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-info">
            <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                <div>
                    <div class="fs-4 fw-semibold"><?php echo isset($stats['products']) ? $stats['products'] : 0; ?></div>
                    <div>Productos</div>
                </div>
                <div class="fs-2"><i class="fas fa-box"></i></div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-warning">
            <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                <div>
                    <div class="fs-4 fw-semibold"><?php echo isset($stats['sales']) ? $stats['sales'] : 0; ?></div>
                    <div>Ventas</div>
                </div>
                <div class="fs-2"><i class="fas fa-shopping-cart"></i></div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-success">
            <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                <div>
                    <div class="fs-4 fw-semibold"><?php echo isset($stats['customers']) ? $stats['customers'] : 0; ?></div>
                    <div>Clientes</div>
                </div>
                <div class="fs-2"><i class="fas fa-user-friends"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- User Info & System Status -->
<div class="row g-4">
    <!-- User Info Card -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user-circle me-2"></i>
                <strong>Información del Usuario</strong>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr>
                            <th width="40%">Usuario:</th>
                            <td><?php echo htmlspecialchars($username); ?></td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td><?php echo htmlspecialchars($email); ?></td>
                        </tr>
                        <tr>
                            <th>Tenant:</th>
                            <td><span class="badge bg-info">#<?php echo $tenant_id; ?></span></td>
                        </tr>
                        <tr>
                            <th>Rol:</th>
                            <td>
                                <?php if ($is_super_admin): ?>
                                    <span class="badge bg-danger">Super Administrador</span>
                                <?php elseif ($is_admin): ?>
                                    <span class="badge bg-warning">Administrador</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Usuario</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- System Status Card -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-shield-alt me-2"></i>
                <strong>Estado del Sistema</strong>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-body-secondary mb-2">Sistema RBAC</h6>
                    <ul class="list-unstyled mb-0">
                        <li><i class="fas fa-check-circle text-success me-2"></i>Permisos configurados</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Roles asignados</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Multi-tenant activo</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Autenticación segura</li>
                    </ul>
                </div>
                
                <?php if ($is_super_admin): ?>
                <div class="mt-3">
                    <a href="<?php echo Uri::create('admin/configuracion'); ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-cog me-1"></i>Configuración del Sistema
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions (for future modules) -->
<div class="row g-4 mt-2">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-bolt me-2"></i>
                <strong>Accesos Rápidos</strong>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <?php if (Helper_Permission::can('users', 'view')): ?>
                    <div class="col-md-3 col-6 mb-3">
                        <a href="<?php echo Uri::create('admin/users'); ?>" class="btn btn-outline-primary w-100 py-3">
                            <i class="fas fa-users fa-2x d-block mb-2"></i>
                            <span>Usuarios</span>
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if (Helper_Permission::can('products', 'view')): ?>
                    <div class="col-md-3 col-6 mb-3">
                        <a href="<?php echo Uri::create('admin/products'); ?>" class="btn btn-outline-info w-100 py-3">
                            <i class="fas fa-box fa-2x d-block mb-2"></i>
                            <span>Productos</span>
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if (Helper_Permission::can('sales', 'view')): ?>
                    <div class="col-md-3 col-6 mb-3">
                        <a href="<?php echo Uri::create('admin/sales'); ?>" class="btn btn-outline-warning w-100 py-3">
                            <i class="fas fa-shopping-cart fa-2x d-block mb-2"></i>
                            <span>Ventas</span>
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if (Helper_Permission::can('reports', 'view')): ?>
                    <div class="col-md-3 col-6 mb-3">
                        <a href="<?php echo Uri::create('admin/reports'); ?>" class="btn btn-outline-success w-100 py-3">
                            <i class="fas fa-chart-bar fa-2x d-block mb-2"></i>
                            <span>Reportes</span>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
