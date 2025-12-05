<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2>
                <i class="fas fa-file-invoice"></i> Orden de Compra: <?php echo htmlspecialchars($order->code); ?>
            </h2>
            <div>
                <?php if ($order->can_edit()): ?>
                    <a href="<?php echo Uri::create('admin/ordenescompra/edit/' . $order->id); ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                <?php endif; ?>
                <?php if ($order->can_approve()): ?>
                    <button type="button" class="btn btn-success" onclick="approveOrder()">
                        <i class="fas fa-check"></i> Aprobar
                    </button>
                <?php endif; ?>
                <?php if ($order->can_reject()): ?>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="fas fa-times"></i> Rechazar
                    </button>
                <?php endif; ?>
                <?php if ($order->can_receive()): ?>
                    <button type="button" class="btn btn-primary" onclick="receiveOrder()">
                        <i class="fas fa-box-open"></i> Recibir
                    </button>
                <?php endif; ?>
                <a href="<?php echo Uri::create('admin/ordenescompra'); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Tabs de Información -->
<div class="card">
    <div class="card-body">
        <ul class="nav nav-tabs mb-3" id="viewTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button">
                    <i class="fas fa-info-circle"></i> Información General
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="items-tab" data-bs-toggle="tab" data-bs-target="#items" type="button">
                    <i class="fas fa-list"></i> Partidas (<?php echo count($order->items); ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button">
                    <i class="fas fa-history"></i> Historial
                </button>
            </li>
        </ul>
        
        <div class="tab-content" id="viewTabsContent">
            <!-- Tab 1: Información General -->
            <div class="tab-pane fade show active" id="info" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3">Datos de la Orden</h5>
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Código:</th>
                                <td><strong><?php echo htmlspecialchars($order->code); ?></strong></td>
                            </tr>
                            <tr>
                                <th>Proveedor:</th>
                                <td>
                                    <a href="<?php echo Uri::create('admin/proveedores/view/' . $order->provider->id); ?>">
                                        <?php echo htmlspecialchars($order->provider->company_name); ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Fecha de Orden:</th>
                                <td><?php echo date('d/m/Y', strtotime($order->order_date)); ?></td>
                            </tr>
                            <tr>
                                <th>Fecha de Entrega:</th>
                                <td><?php echo $order->delivery_date ? date('d/m/Y', strtotime($order->delivery_date)) : '-'; ?></td>
                            </tr>
                            <tr>
                                <th>Tipo:</th>
                                <td><?php echo $order->get_type_badge(); ?></td>
                            </tr>
                            <tr>
                                <th>Estado:</th>
                                <td><?php echo $order->get_status_badge(); ?></td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h5 class="mb-3">Totales</h5>
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Subtotal:</th>
                                <td class="text-end"><strong>$<?php echo number_format($order->subtotal, 2); ?></strong></td>
                            </tr>
                            <tr>
                                <th>IVA:</th>
                                <td class="text-end"><strong>$<?php echo number_format($order->tax, 2); ?></strong></td>
                            </tr>
                            <tr class="table-primary">
                                <th>Total:</th>
                                <td class="text-end"><strong class="fs-5">$<?php echo number_format($order->total, 2); ?></strong></td>
                            </tr>
                        </table>
                        
                        <?php if ($order->notes): ?>
                            <h5 class="mb-3 mt-4">Notas</h5>
                            <div class="alert alert-info">
                                <?php echo nl2br(htmlspecialchars($order->notes)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Tab 2: Partidas -->
            <div class="tab-pane fade" id="items" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="10%">Tipo</th>
                                <th width="35%">Descripción</th>
                                <th width="10%" class="text-center">Cantidad</th>
                                <th width="12%" class="text-end">Precio Unit.</th>
                                <th width="8%" class="text-center">IVA %</th>
                                <th width="12%" class="text-end">Subtotal</th>
                                <th width="12%" class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $index = 1; foreach ($order->items as $item): ?>
                                <tr>
                                    <td><?php echo $index++; ?></td>
                                    <td>
                                        <?php if ($item->item_type == 'product'): ?>
                                            <span class="badge bg-primary">Producto</span>
                                        <?php elseif ($item->item_type == 'service'): ?>
                                            <span class="badge bg-info">Servicio</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Personalizado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($item->description); ?>
                                        <?php if ($item->product): ?>
                                            <br><small class="text-muted">SKU: <?php echo htmlspecialchars($item->product->sku); ?></small>
                                        <?php endif; ?>
                                        <?php if ($item->notes): ?>
                                            <br><small class="text-info"><i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($item->notes); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?php echo number_format($item->quantity, 2); ?></td>
                                    <td class="text-end">$<?php echo number_format($item->unit_price, 2); ?></td>
                                    <td class="text-center"><?php echo number_format($item->tax_rate, 2); ?>%</td>
                                    <td class="text-end">$<?php echo number_format($item->subtotal, 2); ?></td>
                                    <td class="text-end"><strong>$<?php echo number_format($item->total, 2); ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="6" class="text-end"><strong>Subtotal:</strong></td>
                                <td colspan="2" class="text-end"><strong>$<?php echo number_format($order->subtotal, 2); ?></strong></td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-end"><strong>IVA:</strong></td>
                                <td colspan="2" class="text-end"><strong>$<?php echo number_format($order->tax, 2); ?></strong></td>
                            </tr>
                            <tr class="table-primary">
                                <td colspan="6" class="text-end"><strong>Total:</strong></td>
                                <td colspan="2" class="text-end"><strong class="fs-5">$<?php echo number_format($order->total, 2); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
            <!-- Tab 3: Historial -->
            <div class="tab-pane fade" id="history" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Registro de Cambios</h5>
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Creado Por:</th>
                                <td><?php echo $order->creator ? htmlspecialchars($order->creator->username) : '-'; ?></td>
                            </tr>
                            <tr>
                                <th>Fecha de Creación:</th>
                                <td><?php echo $order->created_at ? date('d/m/Y H:i', strtotime($order->created_at)) : '-'; ?></td>
                            </tr>
                            <tr>
                                <th>Última Actualización:</th>
                                <td><?php echo $order->updated_at ? date('d/m/Y H:i', strtotime($order->updated_at)) : '-'; ?></td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <?php if ($order->status == 'approved' || $order->status == 'received'): ?>
                            <h5>Aprobación</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Aprobado Por:</th>
                                    <td><?php echo $order->approver ? htmlspecialchars($order->approver->username) : '-'; ?></td>
                                </tr>
                                <tr>
                                    <th>Fecha de Aprobación:</th>
                                    <td><?php echo $order->approved_at ? date('d/m/Y H:i', strtotime($order->approved_at)) : '-'; ?></td>
                                </tr>
                            </table>
                        <?php endif; ?>
                        
                        <?php if ($order->status == 'rejected'): ?>
                            <h5>Rechazo</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Rechazado Por:</th>
                                    <td><?php echo $order->rejecter ? htmlspecialchars($order->rejecter->username) : '-'; ?></td>
                                </tr>
                                <tr>
                                    <th>Fecha de Rechazo:</th>
                                    <td><?php echo $order->rejected_at ? date('d/m/Y H:i', strtotime($order->rejected_at)) : '-'; ?></td>
                                </tr>
                                <tr>
                                    <th>Razón:</th>
                                    <td><?php echo htmlspecialchars($order->rejection_reason); ?></td>
                                </tr>
                            </table>
                        <?php endif; ?>
                        
                        <?php if ($order->status == 'received'): ?>
                            <h5 class="mt-3">Recepción</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Recibido Por:</th>
                                    <td><?php echo $order->receiver ? htmlspecialchars($order->receiver->username) : '-'; ?></td>
                                </tr>
                                <tr>
                                    <th>Fecha de Recepción:</th>
                                    <td><?php echo $order->received_at ? date('d/m/Y H:i', strtotime($order->received_at)) : '-'; ?></td>
                                </tr>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Rechazo -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?php echo Uri::create('admin/ordenescompra/reject/' . $order->id); ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Rechazar Orden de Compra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Razón del Rechazo <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Rechazar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function approveOrder() {
    if (confirm('¿Está seguro que desea aprobar esta orden de compra?')) {
        window.location.href = '<?php echo Uri::create("admin/ordenescompra/approve/" . $order->id); ?>';
    }
}

function receiveOrder() {
    if (confirm('¿Confirma que ha recibido esta orden de compra?')) {
        window.location.href = '<?php echo Uri::create("admin/ordenescompra/receive/" . $order->id); ?>';
    }
}

// Inicializar tabs - Compatible Bootstrap 4/5
document.addEventListener('DOMContentLoaded', function() {
    var tabButtons = document.querySelectorAll('#viewTabs button[data-bs-target]')
    
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
});
</script>
