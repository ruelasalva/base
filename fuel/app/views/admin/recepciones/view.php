<div class="container-fluid p-4">
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-warehouse text-primary"></i> Recepción de Mercancía
            </h1>
            <p class="text-muted mb-0">
                Código: <strong><?php echo $receipt->code; ?></strong>
                <?php echo $receipt->get_status_badge(); ?>
                <?php if ($receipt->has_discrepancy): ?>
                    <span class="badge bg-danger ms-1">
                        <i class="fas fa-exclamation-triangle"></i> Con Discrepancias
                    </span>
                <?php endif; ?>
            </p>
        </div>
        <div class="btn-group">
            <?php if (Helper_Permission::can('recepciones', 'edit') && $receipt->can_edit()): ?>
                <a href="<?php echo Uri::create('admin/recepciones/edit/' . $receipt->id); ?>" 
                   class="btn btn-primary">
                    <i class="fas fa-edit"></i> Editar
                </a>
            <?php endif; ?>
            
            <?php if (Helper_Permission::can('recepciones', 'verify') && $receipt->status == 'received'): ?>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#verifyModal">
                    <i class="fas fa-check-circle"></i> Verificar
                </button>
            <?php endif; ?>
            
            <?php if (Helper_Permission::can('recepciones', 'cancel') && $receipt->status != 'verified' && $receipt->status != 'cancelled'): ?>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                    <i class="fas fa-ban"></i> Cancelar
                </button>
            <?php endif; ?>
            
            <a href="<?php echo Uri::create('admin/recepciones'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <!-- Tarjeta principal -->
    <div class="card border-0 shadow-sm">
        <!-- Tabs (Manual JavaScript) -->
        <div class="card-header bg-white border-bottom">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" type="button" data-tab="general">
                        <i class="fas fa-info-circle"></i> Información General
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" type="button" data-tab="productos">
                        <i class="fas fa-boxes"></i> Productos Recibidos
                        <span class="badge bg-primary ms-1"><?php echo $receipt->total_items; ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" type="button" data-tab="detalles">
                        <i class="fas fa-clipboard-list"></i> Detalles Adicionales
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <!-- Tab: Información General -->
            <div class="tab-content active" data-tab-content="general">
                <div class="row g-4">
                    <!-- Información de la recepción -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-warehouse"></i> Datos de Recepción
                        </h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" style="width: 40%;">Código:</td>
                                <td><strong><?php echo $receipt->code; ?></strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Almacén Destino:</td>
                                <td>
                                    <i class="fas fa-warehouse text-muted me-1"></i>
                                    <?php echo $receipt->almacen_name; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Fecha Programada:</td>
                                <td><?php echo date('d/m/Y', strtotime($receipt->receipt_date)); ?></td>
                            </tr>
                            <?php if ($receipt->received_date): ?>
                            <tr>
                                <td class="text-muted">Fecha Real:</td>
                                <td>
                                    <i class="fas fa-calendar-check text-success me-1"></i>
                                    <?php echo date('d/m/Y H:i', strtotime($receipt->received_date)); ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td class="text-muted">Estado:</td>
                                <td><?php echo $receipt->get_status_badge(); ?></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Información de orden y proveedor -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-file-invoice"></i> Orden y Proveedor
                        </h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" style="width: 40%;">Orden de Compra:</td>
                                <td>
                                    <?php if ($receipt->purchase_order): ?>
                                        <a href="<?php echo Uri::create('admin/ordenescompra/view/' . $receipt->purchase_order->id); ?>">
                                            <?php echo $receipt->purchase_order->code; ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Proveedor:</td>
                                <td>
                                    <?php if ($receipt->provider): ?>
                                        <a href="<?php echo Uri::create('admin/proveedores/view/' . $receipt->provider->id); ?>">
                                            <?php echo $receipt->provider->company_name; ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php if ($receipt->receiver): ?>
                            <tr>
                                <td class="text-muted">Recibido por:</td>
                                <td>
                                    <i class="fas fa-user text-muted me-1"></i>
                                    <?php echo $receipt->receiver->username; ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($receipt->verifier): ?>
                            <tr>
                                <td class="text-muted">Verificado por:</td>
                                <td>
                                    <i class="fas fa-user-check text-success me-1"></i>
                                    <?php echo $receipt->verifier->username; ?>
                                    <small class="text-muted">
                                        (<?php echo date('d/m/Y', strtotime($receipt->verified_date)); ?>)
                                    </small>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>

                    <!-- Totales -->
                    <div class="col-12">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <small class="text-muted d-block">Total Items</small>
                                        <h4 class="mb-0"><?php echo $receipt->total_items; ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <small class="text-muted d-block">Cantidad Esperada</small>
                                        <h4 class="mb-0"><?php echo number_format($receipt->total_quantity_expected, 2); ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <small class="text-muted d-block">Cantidad Recibida</small>
                                        <h4 class="mb-0 <?php echo $receipt->is_complete() ? 'text-success' : 'text-warning'; ?>">
                                            <?php echo number_format($receipt->total_quantity_received, 2); ?>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <small class="d-block opacity-75">Valor Total</small>
                                        <h4 class="mb-0">$<?php echo number_format($receipt->total_amount, 2); ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progreso -->
                    <div class="col-12">
                        <label class="form-label">Progreso de Recepción</label>
                        <?php $percentage = $receipt->get_completion_percentage(); ?>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar <?php 
                                echo $percentage >= 100 ? 'bg-success' : 
                                    ($percentage > 0 ? 'bg-warning' : 'bg-secondary'); 
                            ?>" role="progressbar" 
                                 style="width: <?php echo min($percentage, 100); ?>%"
                                 aria-valuenow="<?php echo $percentage; ?>" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                <?php echo number_format($percentage, 1); ?>%
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Productos -->
            <div class="tab-content" data-tab-content="productos" style="display: none;">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cant. Ordenada</th>
                                <th class="text-center">Cant. Recibida</th>
                                <th class="text-center">Diferencia</th>
                                <th class="text-end">Costo Unit.</th>
                                <th class="text-end">Subtotal</th>
                                <th class="text-center">Condición</th>
                                <th>Ubicación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($receipt->items as $item): ?>
                                <tr>
                                    <td>
                                        <?php if ($item->product): ?>
                                            <strong><?php echo $item->product->code; ?></strong><br>
                                            <small class="text-muted"><?php echo $item->product->name; ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">Producto eliminado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo number_format($item->quantity_ordered, 2); ?>
                                    </td>
                                    <td class="text-center">
                                        <strong><?php echo number_format($item->quantity_received, 2); ?></strong>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                        $diff = $item->get_quantity_difference();
                                        $color = $diff == 0 ? 'success' : ($diff > 0 ? 'info' : 'warning');
                                        ?>
                                        <span class="badge bg-<?php echo $color; ?>">
                                            <?php echo $diff > 0 ? '+' : ''; ?><?php echo number_format($diff, 2); ?>
                                        </span>
                                    </td>
                                    <td class="text-end">$<?php echo number_format($item->unit_cost, 2); ?></td>
                                    <td class="text-end">
                                        <strong>$<?php echo number_format($item->get_subtotal(), 2); ?></strong>
                                    </td>
                                    <td class="text-center"><?php echo $item->get_condition_badge(); ?></td>
                                    <td>
                                        <?php if ($item->location): ?>
                                            <i class="fas fa-map-marker-alt text-primary me-1"></i>
                                            <?php echo $item->location; ?>
                                        <?php else: ?>
                                            <span class="text-muted">Sin asignar</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                
                                <?php if ($item->batch_number || $item->expiry_date || $item->notes): ?>
                                    <tr class="table-light">
                                        <td colspan="8" class="py-2">
                                            <small class="text-muted">
                                                <?php if ($item->batch_number): ?>
                                                    <i class="fas fa-barcode me-1"></i>
                                                    <strong>Lote:</strong> <?php echo $item->batch_number; ?>
                                                <?php endif; ?>
                                                
                                                <?php if ($item->expiry_date): ?>
                                                    <i class="fas fa-calendar-times ms-3 me-1"></i>
                                                    <strong>Caducidad:</strong> 
                                                    <?php echo date('d/m/Y', strtotime($item->expiry_date)); ?>
                                                <?php endif; ?>
                                                
                                                <?php if ($item->notes): ?>
                                                    <br>
                                                    <i class="fas fa-sticky-note me-1"></i>
                                                    <?php echo $item->notes; ?>
                                                <?php endif; ?>
                                            </small>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="5" class="text-end"><strong>TOTAL:</strong></td>
                                <td class="text-end">
                                    <strong>$<?php echo number_format($receipt->total_amount, 2); ?></strong>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <?php if ($receipt->has_discrepancy): ?>
                    <div class="alert alert-warning mt-3">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-triangle"></i> Discrepancias Detectadas
                        </h6>
                        <?php if ($receipt->discrepancy_notes): ?>
                            <p class="mb-0"><?php echo nl2br(e($receipt->discrepancy_notes)); ?></p>
                        <?php else: ?>
                            <p class="mb-0">Se detectaron diferencias en las cantidades recibidas o en la condición de los productos.</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tab: Detalles Adicionales -->
            <div class="tab-content" data-tab-content="detalles" style="display: none;">
                <div class="row g-4">
                    <!-- Notas -->
                    <?php if ($receipt->notes): ?>
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-sticky-note"></i> Notas Generales
                            </h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <?php echo nl2br(e($receipt->notes)); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Auditoría -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-history"></i> Información de Auditoría
                        </h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" style="width: 40%;">Creado por:</td>
                                <td>
                                    <?php echo $receipt->creator ? $receipt->creator->username : 'Sistema'; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Fecha de creación:</td>
                                <td><?php echo date('d/m/Y H:i', strtotime($receipt->created_at)); ?></td>
                            </tr>
                            <?php if ($receipt->updated_at != $receipt->created_at): ?>
                            <tr>
                                <td class="text-muted">Última actualización:</td>
                                <td><?php echo date('d/m/Y H:i', strtotime($receipt->updated_at)); ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>

                    <!-- Enlaces relacionados -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-link"></i> Documentos Relacionados
                        </h6>
                        <div class="list-group">
                            <?php if ($receipt->purchase_order): ?>
                                <a href="<?php echo Uri::create('admin/ordenescompra/view/' . $receipt->purchase_order->id); ?>" 
                                   class="list-group-item list-group-item-action">
                                    <i class="fas fa-file-alt text-primary me-2"></i>
                                    Orden de Compra: <?php echo $receipt->purchase_order->code; ?>
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($receipt->provider): ?>
                                <a href="<?php echo Uri::create('admin/proveedores/view/' . $receipt->provider->id); ?>" 
                                   class="list-group-item list-group-item-action">
                                    <i class="fas fa-building text-info me-2"></i>
                                    Proveedor: <?php echo $receipt->provider->company_name; ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para verificar -->
<div class="modal fade" id="verifyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo Uri::create('admin/recepciones/mark_verified/' . $receipt->id); ?>">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle text-success"></i> Verificar Recepción
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Confirma que desea marcar esta recepción como <strong>verificada</strong>?</p>
                    <div class="mb-3">
                        <label class="form-label">Notas de verificación (opcional)</label>
                        <textarea name="verification_notes" class="form-control" rows="3" 
                                  placeholder="Observaciones de la verificación..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Verificar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para cancelar -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo Uri::create('admin/recepciones/cancel/' . $receipt->id); ?>">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-ban text-danger"></i> Cancelar Recepción
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de que desea cancelar esta recepción?</p>
                    <div class="mb-3">
                        <label class="form-label required">Motivo de cancelación</label>
                        <textarea name="cancellation_reason" class="form-control" rows="3" 
                                  placeholder="Especifique el motivo..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-ban"></i> Cancelar Recepción
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========== TABS MANUALES (PATRÓN ESTÁNDAR) ==========
    const tabButtons = document.querySelectorAll('[data-tab]');
    const tabContents = document.querySelectorAll('[data-tab-content]');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const targetTab = this.getAttribute('data-tab');
            
            // Remover active de todos
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.style.display = 'none');
            
            // Activar seleccionado
            this.classList.add('active');
            document.querySelector(`[data-tab-content="${targetTab}"]`).style.display = 'block';
        });
    });
});
</script>

<style>
.nav-tabs .nav-link {
    color: #6c757d;
    border: none;
    border-bottom: 3px solid transparent;
}

.nav-tabs .nav-link:hover {
    border-bottom-color: #dee2e6;
}

.nav-tabs .nav-link.active {
    color: #0d6efd;
    border-bottom-color: #0d6efd;
    background: none;
}

.required::after {
    content: ' *';
    color: #dc3545;
}
</style>
