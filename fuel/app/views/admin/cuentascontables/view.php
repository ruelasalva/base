<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-calculator me-2"></i>
            Detalle de Cuenta Contable
        </h1>
        <div>
            <?php if ($can_edit): ?>
                <a href="<?php echo Uri::create('admin/cuentascontables/edit/' . $account->id); ?>" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> Editar
                </a>
            <?php endif; ?>
            <a href="<?php echo Uri::create('admin/cuentascontables'); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Información Principal -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Información General</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted small">Código de Cuenta</label>
                            <h4 class="mb-0"><?php echo $account->account_code; ?></h4>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Código SAT</label>
                            <h5 class="mb-0"><?php echo $account->sat_code ?: '-'; ?></h5>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Nivel</label>
                            <h5 class="mb-0">
                                <span class="badge bg-secondary"><?php echo $account->level; ?></span>
                            </h5>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="text-muted small">Nombre</label>
                            <h5><?php echo $account->name; ?></h5>
                        </div>
                    </div>

                    <?php if ($parent): ?>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="text-muted small">Cuenta Padre</label>
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-level-up-alt me-2"></i>
                                    <a href="<?php echo Uri::create('admin/cuentascontables/view/' . $parent->id); ?>" class="alert-link">
                                        <?php echo $parent->account_code . ' - ' . $parent->name; ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($account->description): ?>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="text-muted small">Descripción</label>
                                <p class="mb-0"><?php echo nl2br($account->description); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-4">
                            <label class="text-muted small">Tipo de Cuenta</label>
                            <div>
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
                                <span class="badge bg-<?php echo $color; ?> fs-6">
                                    <?php echo ucfirst($account->account_type); ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Naturaleza</label>
                            <div>
                                <span class="badge bg-<?php echo $account->nature == 'deudora' ? 'primary' : 'success'; ?> fs-6">
                                    <?php echo ucfirst($account->nature); ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Subtipo</label>
                            <div>
                                <?php echo $account->account_subtype ?: '-'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subcuentas -->
            <?php if (count($children) > 0): ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-sitemap me-2"></i>
                            Subcuentas (<?php echo count($children); ?>)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Tipo</th>
                                        <th>Naturaleza</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($children as $child): ?>
                                        <tr>
                                            <td><strong><?php echo $child->account_code; ?></strong></td>
                                            <td><?php echo $child->name; ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $type_colors[$child->account_type] ?? 'secondary'; ?>">
                                                    <?php echo ucfirst($child->account_type); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $child->nature == 'deudora' ? 'primary' : 'success'; ?>">
                                                    <?php echo ucfirst($child->nature); ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($child->is_active): ?>
                                                    <span class="badge bg-success">Activa</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactiva</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <a href="<?php echo Uri::create('admin/cuentascontables/view/' . $child->id); ?>" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Estado y Configuración -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">Estado y Configuración</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Estado</label>
                        <div>
                            <?php if ($account->is_active): ?>
                                <span class="badge bg-success fs-6">
                                    <i class="fas fa-check-circle"></i> Activa
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary fs-6">
                                    <i class="fas fa-times-circle"></i> Inactiva
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Permite Movimientos</label>
                        <div>
                            <?php if ($account->allows_movement): ?>
                                <span class="badge bg-success fs-6">
                                    <i class="fas fa-check"></i> Sí
                                </span>
                            <?php else: ?>
                                <span class="badge bg-danger fs-6">
                                    <i class="fas fa-times"></i> No
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <label class="text-muted small">Subcuentas</label>
                        <div>
                            <span class="badge bg-info fs-6">
                                <?php echo count($children); ?> subcuenta(s)
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">Acciones Rápidas</h6>
                </div>
                <div class="card-body">
                    <?php if ($can_edit): ?>
                        <a href="<?php echo Uri::create('admin/cuentascontables/edit/' . $account->id); ?>" 
                           class="btn btn-warning btn-sm w-100 mb-2">
                            <i class="fas fa-edit me-1"></i> Editar Cuenta
                        </a>

                        <a href="<?php echo Uri::create('admin/cuentascontables/toggle_status/' . $account->id); ?>" 
                           class="btn btn-<?php echo $account->is_active ? 'secondary' : 'success'; ?> btn-sm w-100 mb-2"
                           onclick="return confirm('¿Cambiar el estado?');">
                            <i class="fas fa-power-off me-1"></i>
                            <?php echo $account->is_active ? 'Desactivar' : 'Activar'; ?>
                        </a>

                        <a href="<?php echo Uri::create('admin/cuentascontables/create'); ?>?parent_id=<?php echo $account->id; ?>" 
                           class="btn btn-primary btn-sm w-100 mb-2">
                            <i class="fas fa-plus me-1"></i> Crear Subcuenta
                        </a>
                    <?php endif; ?>

                    <?php if ($can_delete && count($children) == 0): ?>
                        <a href="<?php echo Uri::create('admin/cuentascontables/delete/' . $account->id); ?>" 
                           class="btn btn-danger btn-sm w-100"
                           onclick="return confirm('¿Está seguro de eliminar esta cuenta?');">
                            <i class="fas fa-trash me-1"></i> Eliminar Cuenta
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Información de Auditoría -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-clock me-1"></i> Auditoría
                    </h6>
                </div>
                <div class="card-body small">
                    <div class="mb-2">
                        <label class="text-muted">Creado:</label><br>
                        <?php echo date('d/m/Y H:i', strtotime($account->created_at)); ?>
                    </div>
                    <div>
                        <label class="text-muted">Actualizado:</label><br>
                        <?php echo date('d/m/Y H:i', strtotime($account->updated_at)); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
