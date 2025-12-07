<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprobar Nómina - <?php echo htmlspecialchars($period->name, ENT_QUOTES, 'UTF-8'); ?></title>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-check-circle"></i> Aprobar Nómina - <?php echo htmlspecialchars($period->name, ENT_QUOTES, 'UTF-8'); ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (Session::get_flash('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                <?php echo Session::get_flash('error'); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (Session::get_flash('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                <?php echo Session::get_flash('success'); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Información del Período -->
                        <div class="row mb-4">
                            <div class="col-md-2">
                                <div class="card border-primary">
                                    <div class="card-body text-center p-2">
                                        <h6 class="text-muted mb-1 small">Código</h6>
                                        <h5 class="mb-0"><?php echo htmlspecialchars($period->code, ENT_QUOTES, 'UTF-8'); ?></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card border-info">
                                    <div class="card-body text-center p-2">
                                        <h6 class="text-muted mb-1 small">Tipo</h6>
                                        <h5 class="mb-0"><?php echo $period->get_period_type_label(); ?></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-info">
                                    <div class="card-body text-center p-2">
                                        <h6 class="text-muted mb-1 small">Período</h6>
                                        <h6 class="mb-0"><?php echo date('d/m/Y', $period->start_date); ?> - <?php echo date('d/m/Y', $period->end_date); ?></h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card border-success">
                                    <div class="card-body text-center p-2">
                                        <h6 class="text-muted mb-1 small">Fecha Pago</h6>
                                        <h5 class="mb-0"><?php echo date('d/m/Y', $period->payment_date); ?></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-warning">
                                    <div class="card-body text-center p-2">
                                        <h6 class="text-muted mb-1 small">Estado</h6>
                                        <?php echo $period->get_status_badge(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (!$period->can_approve()): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Advertencia:</strong> Este período no puede ser aprobado en este momento.
                                <br>
                                <small>El período debe estar en estado "Calculada" para poder ser aprobado.</small>
                            </div>
                        <?php endif; ?>

                        <!-- Resumen General -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="text-muted mb-1">Total Empleados</h6>
                                                <h3 class="mb-0"><?php echo $summary['total_employees']; ?></h3>
                                            </div>
                                            <div class="text-primary">
                                                <i class="fas fa-users fa-3x opacity-50"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="text-muted mb-1">Total Percepciones</h6>
                                                <h3 class="mb-0 text-success">$<?php echo number_format($summary['total_perceptions'], 2); ?></h3>
                                            </div>
                                            <div class="text-success">
                                                <i class="fas fa-plus-circle fa-3x opacity-50"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="text-muted mb-1">Total Deducciones</h6>
                                                <h3 class="mb-0 text-danger">$<?php echo number_format($summary['total_deductions'], 2); ?></h3>
                                            </div>
                                            <div class="text-danger">
                                                <i class="fas fa-minus-circle fa-3x opacity-50"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">Neto a Pagar</h6>
                                                <h3 class="mb-0">$<?php echo number_format($summary['total_net'], 2); ?></h3>
                                            </div>
                                            <div>
                                                <i class="fas fa-money-bill-wave fa-3x opacity-50"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detalle por Departamento -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-building"></i> Resumen por Departamento</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Departamento</th>
                                                <th class="text-center">Empleados</th>
                                                <th class="text-end">Percepciones</th>
                                                <th class="text-end">Deducciones</th>
                                                <th class="text-end">Neto</th>
                                                <th class="text-end">Promedio</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($by_department as $dept): ?>
                                                <tr>
                                                    <td><strong><?php echo htmlspecialchars($dept['department_name'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary"><?php echo $dept['employees']; ?></span>
                                                    </td>
                                                    <td class="text-end text-success">$<?php echo number_format($dept['perceptions'], 2); ?></td>
                                                    <td class="text-end text-danger">$<?php echo number_format($dept['deductions'], 2); ?></td>
                                                    <td class="text-end"><strong>$<?php echo number_format($dept['net'], 2); ?></strong></td>
                                                    <td class="text-end text-muted">$<?php echo number_format($dept['average'], 2); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th>TOTAL</th>
                                                <th class="text-center"><?php echo $summary['total_employees']; ?></th>
                                                <th class="text-end text-success">$<?php echo number_format($summary['total_perceptions'], 2); ?></th>
                                                <th class="text-end text-danger">$<?php echo number_format($summary['total_deductions'], 2); ?></th>
                                                <th class="text-end"><strong>$<?php echo number_format($summary['total_net'], 2); ?></strong></th>
                                                <th class="text-end text-muted">$<?php echo number_format($summary['total_net'] / $summary['total_employees'], 2); ?></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Detalle de Recibos -->
                        <div class="card mb-4">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-file-invoice-dollar"></i> Detalle de Recibos (<?php echo count($receipts); ?>)</h6>
                                <div>
                                    <input type="text" id="searchReceipt" class="form-control form-control-sm" placeholder="Buscar...">
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                    <table class="table table-hover table-sm mb-0" id="receiptsTable">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th style="width: 50px;">
                                                    <input type="checkbox" id="selectAll" checked>
                                                </th>
                                                <th>Recibo</th>
                                                <th>Empleado</th>
                                                <th>Departamento</th>
                                                <th class="text-end">Base</th>
                                                <th class="text-end">Días</th>
                                                <th class="text-end">Percepciones</th>
                                                <th class="text-end">Deducciones</th>
                                                <th class="text-end">Neto</th>
                                                <th class="text-center">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($receipts as $receipt): ?>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="receipt-checkbox" name="receipts[]" value="<?php echo $receipt->id; ?>" checked>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted"><?php echo htmlspecialchars($receipt->receipt_number, ENT_QUOTES, 'UTF-8'); ?></small>
                                                    </td>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($receipt->employee_code, ENT_QUOTES, 'UTF-8'); ?></strong>
                                                        <br>
                                                        <small><?php echo htmlspecialchars($receipt->employee_name, ENT_QUOTES, 'UTF-8'); ?></small>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($receipt->department_name ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td class="text-end">$<?php echo number_format($receipt->base_salary, 2); ?></td>
                                                    <td class="text-end"><?php echo number_format($receipt->worked_days, 1); ?></td>
                                                    <td class="text-end text-success">$<?php echo number_format($receipt->total_perceptions, 2); ?></td>
                                                    <td class="text-end text-danger">$<?php echo number_format($receipt->total_deductions, 2); ?></td>
                                                    <td class="text-end">
                                                        <strong>$<?php echo number_format($receipt->net_payment, 2); ?></strong>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-info">Calculado</span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="6" class="text-end">Seleccionados:</th>
                                                <th class="text-end text-success" id="totalPerceptions">$0.00</th>
                                                <th class="text-end text-danger" id="totalDeductions">$0.00</th>
                                                <th class="text-end" id="totalNet">$0.00</th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Alertas y Validaciones -->
                        <?php if (!empty($alerts)): ?>
                            <div class="card mb-4">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Alertas y Validaciones</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="mb-0">
                                        <?php foreach ($alerts as $alert): ?>
                                            <li><?php echo htmlspecialchars($alert, ENT_QUOTES, 'UTF-8'); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Comentarios de Aprobación -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-comment"></i> Comentarios de Aprobación</h6>
                            </div>
                            <div class="card-body">
                                <form id="approvalForm" method="post" action="<?php echo Uri::create('admin/nomina/approve/' . $period->id); ?>">
                                    <input type="hidden" name="<?php echo Config::get('security.csrf_token_key'); ?>" value="<?php echo Security::fetch_token(); ?>">
                                    
                                    <div class="mb-3">
                                        <label for="comments" class="form-label">Comentarios u Observaciones</label>
                                        <textarea 
                                            class="form-control" 
                                            id="comments" 
                                            name="comments" 
                                            rows="3"
                                            placeholder="Ingrese cualquier comentario u observación sobre esta nómina..."><?php echo htmlspecialchars(Input::post('comments', ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="confirmReview" required>
                                        <label class="form-check-label" for="confirmReview">
                                            <strong>Confirmo que he revisado todos los recibos y los montos son correctos</strong>
                                        </label>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="confirmAuthorization" required>
                                        <label class="form-check-label" for="confirmAuthorization">
                                            <strong>Autorizo el pago de esta nómina por un total de $<?php echo number_format($summary['total_net'], 2); ?></strong>
                                        </label>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="<?php echo Uri::create('admin/nomina/view/' . $period->id); ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Regresar
                            </a>
                            
                            <div>
                                <?php if ($period->can_approve() && count($receipts) > 0): ?>
                                    <button type="button" class="btn btn-outline-primary me-2" onclick="window.print()">
                                        <i class="fas fa-print"></i> Imprimir Resumen
                                    </button>
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                        <i class="fas fa-check-circle"></i> Aprobar Nómina
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-success" disabled>
                                        <i class="fas fa-lock"></i> No Disponible
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-check-circle"></i> Confirmar Aprobación de Nómina</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <strong>¿Estás seguro de aprobar esta nómina?</strong>
                    </div>
                    
                    <h6>Resumen de Aprobación:</h6>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Período:</strong></td>
                            <td><?php echo htmlspecialchars($period->name, ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Empleados:</strong></td>
                            <td><span id="approveEmployeeCount"><?php echo $summary['total_employees']; ?></span> empleados</td>
                        </tr>
                        <tr>
                            <td><strong>Total Percepciones:</strong></td>
                            <td class="text-success"><strong>$<?php echo number_format($summary['total_perceptions'], 2); ?></strong></td>
                        </tr>
                        <tr>
                            <td><strong>Total Deducciones:</strong></td>
                            <td class="text-danger"><strong>$<?php echo number_format($summary['total_deductions'], 2); ?></strong></td>
                        </tr>
                        <tr class="table-primary">
                            <td><strong>NETO A PAGAR:</strong></td>
                            <td><h5 class="mb-0">$<?php echo number_format($summary['total_net'], 2); ?></h5></td>
                        </tr>
                        <tr>
                            <td><strong>Fecha de Pago:</strong></td>
                            <td><?php echo date('d/m/Y', $period->payment_date); ?></td>
                        </tr>
                    </table>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Importante:</strong> Una vez aprobada la nómina:
                        <ul class="mb-0 mt-2">
                            <li>No se podrán modificar los recibos</li>
                            <li>El período cambiará a estado "Aprobada"</li>
                            <li>Se podrá generar la dispersión bancaria</li>
                            <li>Se podrá proceder al timbrado CFDI</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="confirmApproveBtn" onclick="submitApproval()">
                        <i class="fas fa-check-circle"></i> Sí, Aprobar Nómina
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Búsqueda de recibos
        document.getElementById('searchReceipt').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('#receiptsTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });

        // Seleccionar todos
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.receipt-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateTotals();
        });

        // Actualizar totales
        document.querySelectorAll('.receipt-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateTotals);
        });

        function updateTotals() {
            let totalPerceptions = 0;
            let totalDeductions = 0;
            let totalNet = 0;

            document.querySelectorAll('.receipt-checkbox:checked').forEach(checkbox => {
                const row = checkbox.closest('tr');
                const perceptions = parseFloat(row.cells[6].textContent.replace(/[$,]/g, '')) || 0;
                const deductions = parseFloat(row.cells[7].textContent.replace(/[$,]/g, '')) || 0;
                const net = parseFloat(row.cells[8].textContent.replace(/[$,]/g, '')) || 0;
                
                totalPerceptions += perceptions;
                totalDeductions += deductions;
                totalNet += net;
            });

            document.getElementById('totalPerceptions').textContent = '$' + totalPerceptions.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('totalDeductions').textContent = '$' + totalDeductions.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('totalNet').textContent = '$' + totalNet.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        function submitApproval() {
            const form = document.getElementById('approvalForm');
            const btn = document.getElementById('confirmApproveBtn');
            
            // Validar checkboxes
            if (!document.getElementById('confirmReview').checked || !document.getElementById('confirmAuthorization').checked) {
                alert('Debes confirmar ambas casillas para aprobar la nómina');
                return;
            }
            
            // Deshabilitar botón y mostrar spinner
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Aprobando...';
            
            // Submit del formulario
            form.submit();
        }

        // Calcular totales al cargar
        updateTotals();

        // Estilos para impresión
        const style = document.createElement('style');
        style.textContent = `
            @media print {
                .btn, .modal, .card-header .form-control, .form-check {
                    display: none !important;
                }
                .card {
                    border: 1px solid #dee2e6 !important;
                    page-break-inside: avoid;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
