<!-- Dashboard de Proveedores V1.0 -->
<div class="row">
    <div class="col-12">
        <h1 class="page-header">
            <i class="fa fa-truck"></i> Dashboard - Proveedores
            <small>Métricas y actividad reciente</small>
        </h1>
    </div>
</div>

<!-- Cards de estadísticas principales -->
<div class="row mt-4">
    <!-- Proveedores pendientes de validación -->
    <div class="col-lg-3 col-md-6">
        <div class="card border-warning text-white bg-warning mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-3">
                        <i class="fa fa-user-clock fa-3x"></i>
                    </div>
                    <div class="col-9 text-right">
                        <div class="huge"><?php echo $pending_validation; ?></div>
                        <div>Pendientes Validación</div>
                    </div>
                </div>
            </div>
            <a href="/admin/proveedores/index" class="card-footer text-white small">
                Ver lista <i class="fa fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- Facturas pendientes -->
    <div class="col-lg-3 col-md-6">
        <div class="card border-info text-white bg-info mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-3">
                        <i class="fa fa-file-invoice fa-3x"></i>
                    </div>
                    <div class="col-9 text-right">
                        <div class="huge"><?php echo $bills_pending['count']; ?></div>
                        <div>$<?php echo number_format($bills_pending['total'], 2); ?></div>
                    </div>
                </div>
            </div>
            <a href="/admin/facturas/pendientes" class="card-footer text-white small">
                Ver facturas <i class="fa fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- Facturas aceptadas este mes -->
    <div class="col-lg-3 col-md-6">
        <div class="card border-success text-white bg-success mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-3">
                        <i class="fa fa-check-circle fa-3x"></i>
                    </div>
                    <div class="col-9 text-right">
                        <div class="huge"><?php echo $bills_accepted['count']; ?></div>
                        <div>$<?php echo number_format($bills_accepted['total'], 2); ?></div>
                    </div>
                </div>
            </div>
            <a href="/admin/facturas/aceptadas" class="card-footer text-white small">
                Ver aceptadas <i class="fa fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- Contrarecibos pendientes -->
    <div class="col-lg-3 col-md-6">
        <div class="card border-danger text-white bg-danger mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-3">
                        <i class="fa fa-receipt fa-3x"></i>
                    </div>
                    <div class="col-9 text-right">
                        <div class="huge"><?php echo $receipts_pending['count']; ?></div>
                        <div>$<?php echo number_format($receipts_pending['total'], 2); ?></div>
                    </div>
                </div>
            </div>
            <a href="/admin/contrarecibos/pendientes" class="card-footer text-white small">
                Ver pendientes <i class="fa fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Segunda fila de cards -->
<div class="row">
    <!-- Facturas rechazadas este mes -->
    <div class="col-lg-4 col-md-6">
        <div class="card border-dark mb-3">
            <div class="card-header bg-dark text-white">
                <i class="fa fa-times-circle"></i> Facturas Rechazadas
            </div>
            <div class="card-body text-center">
                <h2 class="text-danger"><?php echo $bills_rejected; ?></h2>
                <p class="text-muted">Este mes</p>
            </div>
        </div>
    </div>

    <!-- Contrarecibos vencidos -->
    <div class="col-lg-4 col-md-6">
        <div class="card border-danger mb-3">
            <div class="card-header bg-danger text-white">
                <i class="fa fa-exclamation-triangle"></i> Contrarecibos Vencidos
            </div>
            <div class="card-body text-center">
                <h2 class="text-danger"><?php echo $receipts_overdue['count']; ?></h2>
                <p class="text-muted">$<?php echo number_format($receipts_overdue['total'], 2); ?></p>
            </div>
        </div>
    </div>

    <!-- Configuración rápida -->
    <div class="col-lg-4 col-md-6">
        <div class="card border-secondary mb-3">
            <div class="card-header bg-secondary text-white">
                <i class="fa fa-cog"></i> Acciones Rápidas
            </div>
            <div class="card-body">
                <a href="/admin/proveedores/config" class="btn btn-primary btn-sm btn-block">
                    <i class="fa fa-sliders-h"></i> Configuración Facturación
                </a>
                <a href="/admin/proveedores/agregar" class="btn btn-success btn-sm btn-block mt-2">
                    <i class="fa fa-plus"></i> Nuevo Proveedor
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Top 5 Proveedores y Actividad Reciente -->
<div class="row mt-4">
    <!-- Top 5 Proveedores -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-trophy"></i> Top 5 Proveedores del Mes
                <small class="text-muted">(Por monto facturado)</small>
            </div>
            <div class="card-body">
                <?php if (!empty($top_providers)): ?>
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Proveedor</th>
                                <th class="text-center">Facturas</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($top_providers as $index => $provider): ?>
                                <tr>
                                    <td>
                                        <?php if ($index === 0): ?>
                                            <i class="fa fa-trophy text-warning"></i>
                                        <?php else: ?>
                                            <?php echo $index + 1; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="/admin/proveedores/info/<?php echo $provider['id']; ?>">
                                            <?php echo e($provider['company_name']); ?>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info"><?php echo $provider['bill_count']; ?></span>
                                    </td>
                                    <td class="text-right">
                                        <strong>$<?php echo number_format($provider['total_amount'], 2); ?></strong>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted text-center py-4">
                        <i class="fa fa-inbox fa-2x"></i><br>
                        No hay datos este mes
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Actividad Reciente -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-history"></i> Actividad Reciente
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                <?php if (!empty($recent_activity)): ?>
                    <div class="list-group">
                        <?php foreach ($recent_activity as $log): ?>
                            <div class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">
                                        <?php
                                        $icons = [
                                            'register' => 'fa-user-plus text-success',
                                            'login_success' => 'fa-sign-in-alt text-success',
                                            'login_failed' => 'fa-times-circle text-danger',
                                            'upload' => 'fa-cloud-upload-alt text-info',
                                            'validate_sat' => 'fa-check-circle text-primary',
                                            'approve' => 'fa-check text-success',
                                            'reject' => 'fa-ban text-danger',
                                            'suspend' => 'fa-user-lock text-warning',
                                            'activate' => 'fa-user-check text-success',
                                            'generate_auto' => 'fa-receipt text-info'
                                        ];
                                        $icon = isset($icons[$log['action']]) ? $icons[$log['action']] : 'fa-info-circle';
                                        ?>
                                        <i class="fa <?php echo $icon; ?>"></i>
                                        <?php echo e($log['description']); ?>
                                    </h6>
                                    <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($log['created_at'])); ?></small>
                                </div>
                                <?php if ($log['provider_name']): ?>
                                    <p class="mb-1 small">
                                        <strong>Proveedor:</strong> 
                                        <a href="/admin/proveedores/info/<?php echo $log['provider_id']; ?>">
                                            <?php echo e($log['provider_name']); ?>
                                        </a>
                                    </p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-4">
                        <i class="fa fa-inbox fa-2x"></i><br>
                        No hay actividad reciente
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Estilos adicionales -->
<style>
.huge {
    font-size: 40px;
    font-weight: bold;
}
.card-footer {
    display: block;
    text-decoration: none;
}
.card-footer:hover {
    background-color: rgba(0,0,0,0.1);
}
</style>
