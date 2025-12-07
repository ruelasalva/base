<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calcular Nómina - <?php echo htmlspecialchars($period->name, ENT_QUOTES, 'UTF-8'); ?></title>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-calculator"></i> Calcular Nómina - <?php echo htmlspecialchars($period->name, ENT_QUOTES, 'UTF-8'); ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (Session::get_flash('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                <?php echo Session::get_flash('error'); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Información del Período -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card border-primary">
                                    <div class="card-body text-center">
                                        <h6 class="text-muted mb-1">Período</h6>
                                        <h4 class="mb-0"><?php echo htmlspecialchars($period->code, ENT_QUOTES, 'UTF-8'); ?></h4>
                                        <small><?php echo $period->get_period_type_label(); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-info">
                                    <div class="card-body text-center">
                                        <h6 class="text-muted mb-1">Fechas</h6>
                                        <h6 class="mb-0"><?php echo date('d/m/Y', $period->start_date); ?></h6>
                                        <small>al <?php echo date('d/m/Y', $period->end_date); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-success">
                                    <div class="card-body text-center">
                                        <h6 class="text-muted mb-1">Fecha de Pago</h6>
                                        <h4 class="mb-0"><?php echo date('d/m/Y', $period->payment_date); ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-warning">
                                    <div class="card-body text-center">
                                        <h6 class="text-muted mb-1">Estado</h6>
                                        <?php echo $period->get_status_badge(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (!$period->can_calculate()): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Advertencia:</strong> Este período no puede ser calculado en este momento.
                                <br>
                                <small>El período debe estar en estado "Borrador" para poder calcular la nómina.</small>
                            </div>
                        <?php endif; ?>

                        <!-- Resumen de Empleados -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-users"></i> Empleados a Procesar</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>Total de Empleados Activos:</strong></td>
                                                <td class="text-end">
                                                    <span class="badge bg-primary"><?php echo count($active_employees); ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Con Departamento:</strong></td>
                                                <td class="text-end">
                                                    <span class="badge bg-success"><?php echo $stats['with_department']; ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Sin Departamento:</strong></td>
                                                <td class="text-end">
                                                    <span class="badge bg-warning"><?php echo $stats['without_department']; ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Con Salario Configurado:</strong></td>
                                                <td class="text-end">
                                                    <span class="badge bg-success"><?php echo $stats['with_salary']; ?></span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>Salario Total Mensual:</strong></td>
                                                <td class="text-end">
                                                    <strong>$<?php echo number_format($stats['total_salary'], 2); ?></strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Salario Promedio:</strong></td>
                                                <td class="text-end">
                                                    $<?php echo number_format($stats['avg_salary'], 2); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Salario Máximo:</strong></td>
                                                <td class="text-end">
                                                    $<?php echo number_format($stats['max_salary'], 2); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Salario Mínimo:</strong></td>
                                                <td class="text-end">
                                                    $<?php echo number_format($stats['min_salary'], 2); ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <?php if ($stats['without_department'] > 0 || $stats['without_salary'] > 0): ?>
                                    <div class="alert alert-warning mb-0">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Advertencias:</strong>
                                        <ul class="mb-0 mt-2">
                                            <?php if ($stats['without_department'] > 0): ?>
                                                <li><?php echo $stats['without_department']; ?> empleado(s) sin departamento asignado</li>
                                            <?php endif; ?>
                                            <?php if ($stats['without_salary'] > 0): ?>
                                                <li><?php echo $stats['without_salary']; ?> empleado(s) sin salario configurado</li>
                                            <?php endif; ?>
                                        </ul>
                                        <small class="d-block mt-2">
                                            Estos empleados serán procesados pero pueden tener cálculos incorrectos.
                                            Se recomienda completar su información antes de calcular.
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Preview de Empleados -->
                        <div class="card mb-4">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-list"></i> Vista Previa de Empleados</h6>
                                <div>
                                    <input type="text" id="searchEmployee" class="form-control form-control-sm" placeholder="Buscar empleado...">
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-hover table-sm mb-0" id="employeesTable">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th style="width: 50px;">
                                                    <input type="checkbox" id="selectAll" checked>
                                                </th>
                                                <th>Código</th>
                                                <th>Nombre</th>
                                                <th>Departamento</th>
                                                <th>Puesto</th>
                                                <th class="text-end">Salario Base</th>
                                                <th class="text-end">Días</th>
                                                <th class="text-end">Estimado Neto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($active_employees as $employee): ?>
                                                <tr data-employee-id="<?php echo $employee->id; ?>">
                                                    <td>
                                                        <input type="checkbox" class="employee-checkbox" name="employees[]" value="<?php echo $employee->id; ?>" checked>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($employee->employee_code, ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td>
                                                        <?php echo htmlspecialchars($employee->first_name . ' ' . $employee->last_name, ENT_QUOTES, 'UTF-8'); ?>
                                                        <?php if (!$employee->department_id): ?>
                                                            <i class="fas fa-exclamation-triangle text-warning" title="Sin departamento"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($employee->department->name ?? 'Sin asignar', ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?php echo htmlspecialchars($employee->position->name ?? 'Sin asignar', ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td class="text-end">
                                                        <?php if ($employee->base_salary > 0): ?>
                                                            $<?php echo number_format($employee->base_salary, 2); ?>
                                                        <?php else: ?>
                                                            <span class="text-danger">$0.00</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-end">
                                                        <?php 
                                                            $days = ($period->end_date - $period->start_date) / 86400 + 1;
                                                            echo number_format($days, 0);
                                                        ?>
                                                    </td>
                                                    <td class="text-end">
                                                        <?php 
                                                            // Cálculo estimado simple (85% después deducciones)
                                                            $estimated = $employee->base_salary * 0.85;
                                                            echo '$' . number_format($estimated, 2);
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="5" class="text-end">Total Seleccionados:</th>
                                                <th class="text-end" id="totalSalary">$0.00</th>
                                                <th></th>
                                                <th class="text-end" id="totalEstimated">$0.00</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Conceptos que se Aplicarán -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-list-alt"></i> Conceptos de Nómina Activos</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-success"><i class="fas fa-plus-circle"></i> Percepciones (<?php echo count($perceptions); ?>)</h6>
                                        <ul class="list-group list-group-flush">
                                            <?php foreach ($perceptions as $concept): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span>
                                                        <small class="text-muted"><?php echo htmlspecialchars($concept->code, ENT_QUOTES, 'UTF-8'); ?></small>
                                                        <?php echo htmlspecialchars($concept->name, ENT_QUOTES, 'UTF-8'); ?>
                                                    </span>
                                                    <span class="badge bg-success"><?php echo $concept->calculation_type == 'percentage' ? $concept->percentage . '%' : '$' . number_format($concept->fixed_amount, 2); ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-danger"><i class="fas fa-minus-circle"></i> Deducciones (<?php echo count($deductions); ?>)</h6>
                                        <ul class="list-group list-group-flush">
                                            <?php foreach ($deductions as $concept): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span>
                                                        <small class="text-muted"><?php echo htmlspecialchars($concept->code, ENT_QUOTES, 'UTF-8'); ?></small>
                                                        <?php echo htmlspecialchars($concept->name, ENT_QUOTES, 'UTF-8'); ?>
                                                    </span>
                                                    <span class="badge bg-danger"><?php echo $concept->calculation_type == 'percentage' ? $concept->percentage . '%' : '$' . number_format($concept->fixed_amount, 2); ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="<?php echo Uri::create('admin/nomina/view/' . $period->id); ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            
                            <?php if ($period->can_calculate() && count($active_employees) > 0): ?>
                                <div>
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#calculateModal">
                                        <i class="fas fa-calculator"></i> Calcular Nómina
                                    </button>
                                </div>
                            <?php else: ?>
                                <button type="button" class="btn btn-info" disabled>
                                    <i class="fas fa-lock"></i> No Disponible
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div class="modal fade" id="calculateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-calculator"></i> Confirmar Cálculo de Nómina</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>¿Estás seguro de calcular la nómina?</strong>
                    </div>
                    
                    <p>Se calcularán los recibos de nómina para:</p>
                    <ul>
                        <li><strong><span id="selectedCount">0</span></strong> empleados seleccionados</li>
                        <li>Período: <strong><?php echo htmlspecialchars($period->name, ENT_QUOTES, 'UTF-8'); ?></strong></li>
                        <li>Del <?php echo date('d/m/Y', $period->start_date); ?> al <?php echo date('d/m/Y', $period->end_date); ?></li>
                    </ul>

                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle"></i>
                        <small>
                            <strong>Importante:</strong> Una vez calculada la nómina, el período cambiará a estado "Calculada" 
                            y no podrás modificar las fechas ni los datos del período.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form method="post" action="<?php echo Uri::create('admin/nomina/calculate/' . $period->id); ?>" id="calculateForm">
                        <input type="hidden" name="<?php echo Config::get('security.csrf_token_key'); ?>" value="<?php echo Security::fetch_token(); ?>">
                        <input type="hidden" name="selected_employees" id="selectedEmployeesInput" value="">
                        <button type="submit" class="btn btn-primary" id="confirmCalculateBtn">
                            <i class="fas fa-calculator"></i> Sí, Calcular Nómina
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Búsqueda de empleados
        document.getElementById('searchEmployee').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('#employeesTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });

        // Seleccionar todos
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.employee-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateTotals();
        });

        // Actualizar totales cuando cambian los checkboxes
        document.querySelectorAll('.employee-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateTotals);
        });

        function updateTotals() {
            let totalSalary = 0;
            let totalEstimated = 0;
            let count = 0;

            document.querySelectorAll('.employee-checkbox:checked').forEach(checkbox => {
                const row = checkbox.closest('tr');
                const salaryText = row.cells[5].textContent.replace(/[$,]/g, '');
                const estimatedText = row.cells[7].textContent.replace(/[$,]/g, '');
                
                totalSalary += parseFloat(salaryText) || 0;
                totalEstimated += parseFloat(estimatedText) || 0;
                count++;
            });

            document.getElementById('totalSalary').textContent = '$' + totalSalary.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('totalEstimated').textContent = '$' + totalEstimated.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('selectedCount').textContent = count;
        }

        // Al abrir el modal, preparar los IDs de empleados seleccionados
        document.getElementById('calculateModal').addEventListener('show.bs.modal', function() {
            const selectedIds = [];
            document.querySelectorAll('.employee-checkbox:checked').forEach(checkbox => {
                selectedIds.push(checkbox.value);
            });
            document.getElementById('selectedEmployeesInput').value = selectedIds.join(',');
            
            // Deshabilitar botón mientras se procesa
            const form = document.getElementById('calculateForm');
            const btn = document.getElementById('confirmCalculateBtn');
            
            form.addEventListener('submit', function() {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Calculando...';
            });
        });

        // Calcular totales al cargar
        updateTotals();
    </script>
</body>
</html>
