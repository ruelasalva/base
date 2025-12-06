<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-calculator me-2"></i>
            Catálogo de Cuentas Contables
        </h1>
        <?php if ($can_create): ?>
            <a href="<?php echo Uri::create('admin/cuentascontables/create'); ?>" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Nueva Cuenta
            </a>
        <?php endif; ?>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Cuentas</h6>
                            <h3 class="mb-0"><?php echo number_format($stats['total']); ?></h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-list-ul fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Activas</h6>
                            <h3 class="mb-0 text-success"><?php echo number_format($stats['activas']); ?></h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Con Movimientos</h6>
                            <h3 class="mb-0 text-warning"><?php echo number_format($stats['con_movimientos']); ?></h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-exchange-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-secondary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Inactivas</h6>
                            <h3 class="mb-0 text-secondary"><?php echo number_format($stats['inactivas']); ?></h3>
                        </div>
                        <div class="text-secondary">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen por Tipo -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Resumen por Tipo de Cuenta</h5>
                    <div class="row text-center">
                        <div class="col">
                            <div class="border rounded p-3">
                                <i class="fas fa-wallet text-success fa-2x mb-2"></i>
                                <h4 class="mb-0"><?php echo $stats['activos']; ?></h4>
                                <small class="text-muted">Activos</small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="border rounded p-3">
                                <i class="fas fa-credit-card text-danger fa-2x mb-2"></i>
                                <h4 class="mb-0"><?php echo $stats['pasivos']; ?></h4>
                                <small class="text-muted">Pasivos</small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="border rounded p-3">
                                <i class="fas fa-piggy-bank text-primary fa-2x mb-2"></i>
                                <h4 class="mb-0"><?php echo $stats['capital']; ?></h4>
                                <small class="text-muted">Capital</small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="border rounded p-3">
                                <i class="fas fa-arrow-up text-success fa-2x mb-2"></i>
                                <h4 class="mb-0"><?php echo $stats['ingresos']; ?></h4>
                                <small class="text-muted">Ingresos</small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="border rounded p-3">
                                <i class="fas fa-arrow-down text-warning fa-2x mb-2"></i>
                                <h4 class="mb-0"><?php echo $stats['egresos']; ?></h4>
                                <small class="text-muted">Egresos</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo Uri::create('admin/cuentascontables/index'); ?>" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" class="form-control" placeholder="Código, nombre o código SAT..." value="<?php echo $search; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipo de Cuenta</label>
                    <select name="type" class="form-select">
                        <option value="">Todos</option>
                        <option value="activo" <?php echo $type_filter == 'activo' ? 'selected' : ''; ?>>Activo</option>
                        <option value="pasivo" <?php echo $type_filter == 'pasivo' ? 'selected' : ''; ?>>Pasivo</option>
                        <option value="capital" <?php echo $type_filter == 'capital' ? 'selected' : ''; ?>>Capital</option>
                        <option value="ingresos" <?php echo $type_filter == 'ingresos' ? 'selected' : ''; ?>>Ingresos</option>
                        <option value="egresos" <?php echo $type_filter == 'egresos' ? 'selected' : ''; ?>>Egresos</option>
                        <option value="resultado" <?php echo $type_filter == 'resultado' ? 'selected' : ''; ?>>Resultado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Naturaleza</label>
                    <select name="nature" class="form-select">
                        <option value="">Todas</option>
                        <option value="deudora" <?php echo $nature_filter == 'deudora' ? 'selected' : ''; ?>>Deudora</option>
                        <option value="acreedora" <?php echo $nature_filter == 'acreedora' ? 'selected' : ''; ?>>Acreedora</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                    <a href="<?php echo Uri::create('admin/cuentascontables'); ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Cuentas -->
    <div class="card">
        <div class="card-body">
            <?php if (count($accounts) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Naturaleza</th>
                                <th>Nivel</th>
                                <th class="text-center">Permite Mov.</th>
                                <th class="text-center">Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($accounts as $account): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo $account->account_code; ?></strong>
                                        <?php if ($account->sat_code): ?>
                                            <br><small class="text-muted">SAT: <?php echo $account->sat_code; ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo str_repeat('&nbsp;&nbsp;&nbsp;', $account->level); ?>
                                        <?php if ($account->level > 0): ?>
                                            <i class="fas fa-level-down-alt text-muted me-1"></i>
                                        <?php endif; ?>
                                        <?php echo $account->name; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $type_colors = [
                                            'activo' => 'success',
                                            'pasivo' => 'danger',
                                            'capital' => 'primary',
                                            'ingresos' => 'info',
                                            'egresos' => 'warning',
                                            'resultado' => 'secondary'
                                        ];
                                        $color = $type_colors[$account->account_type] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?php echo $color; ?>">
                                            <?php echo ucfirst($account->account_type); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $account->nature == 'deudora' ? 'primary' : 'success'; ?>">
                                            <?php echo ucfirst($account->nature); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary"><?php echo $account->level; ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($account->allows_movement): ?>
                                            <i class="fas fa-check text-success"></i>
                                        <?php else: ?>
                                            <i class="fas fa-times text-danger"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($account->is_active): ?>
                                            <span class="badge bg-success">Activa</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactiva</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?php echo Uri::create('admin/cuentascontables/view/' . $account->id); ?>" 
                                               class="btn btn-info" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($can_edit): ?>
                                                <a href="<?php echo Uri::create('admin/cuentascontables/edit/' . $account->id); ?>" 
                                                   class="btn btn-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?php echo Uri::create('admin/cuentascontables/toggle_status/' . $account->id); ?>" 
                                                   class="btn btn-<?php echo $account->is_active ? 'secondary' : 'success'; ?>" 
                                                   title="<?php echo $account->is_active ? 'Desactivar' : 'Activar'; ?>"
                                                   onclick="return confirm('¿Cambiar el estado de esta cuenta?');">
                                                    <i class="fas fa-power-off"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($can_delete && !$account->has_children()): ?>
                                                <a href="<?php echo Uri::create('admin/cuentascontables/delete/' . $account->id); ?>" 
                                                   class="btn btn-danger" title="Eliminar"
                                                   onclick="return confirm('¿Está seguro de eliminar esta cuenta?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                    <p class="mb-0">No se encontraron cuentas contables.</p>
                </div>
            <?php endif; ?>
        </div>
        <?php if (count($accounts) > 0): ?>
            <div class="card-footer bg-white">
                <?php echo $pagination->render(); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
