<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Período de Nómina</title>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-edit"></i> Editar Período de Nómina
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

                        <?php if (!$period->can_edit()): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Advertencia:</strong> Este período no puede ser editado porque ya está en estado 
                                <strong><?php echo htmlspecialchars($period->get_status_label(), ENT_QUOTES, 'UTF-8'); ?></strong>.
                                Solo los períodos en estado "Borrador" pueden ser editados.
                            </div>
                        <?php endif; ?>

                        <?php echo Form::open(array('action' => 'admin/nomina/edit/' . $period->id, 'method' => 'post', 'class' => 'needs-validation', 'novalidate' => true)); ?>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="code" class="form-label required">Código del Período</label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        id="code" 
                                        name="code" 
                                        value="<?php echo htmlspecialchars(Input::post('code', $period->code), ENT_QUOTES, 'UTF-8'); ?>"
                                        required
                                        maxlength="50"
                                        <?php echo !$period->can_edit() ? 'readonly' : ''; ?>
                                        placeholder="Ej: NOM-2025-01">
                                    <div class="invalid-feedback">
                                        Por favor ingrese el código del período.
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label required">Nombre del Período</label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        id="name" 
                                        name="name" 
                                        value="<?php echo htmlspecialchars(Input::post('name', $period->name), ENT_QUOTES, 'UTF-8'); ?>"
                                        required
                                        maxlength="100"
                                        <?php echo !$period->can_edit() ? 'readonly' : ''; ?>
                                        placeholder="Ej: Nómina Enero 2025">
                                    <div class="invalid-feedback">
                                        Por favor ingrese el nombre del período.
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="period_type" class="form-label required">Tipo de Período</label>
                                    <select 
                                        class="form-select" 
                                        id="period_type" 
                                        name="period_type" 
                                        required
                                        <?php echo !$period->can_edit() ? 'disabled' : ''; ?>>
                                        <option value="">Seleccione...</option>
                                        <option value="monthly" <?php echo Input::post('period_type', $period->period_type) == 'monthly' ? 'selected' : ''; ?>>Mensual</option>
                                        <option value="biweekly" <?php echo Input::post('period_type', $period->period_type) == 'biweekly' ? 'selected' : ''; ?>>Quincenal</option>
                                        <option value="weekly" <?php echo Input::post('period_type', $period->period_type) == 'weekly' ? 'selected' : ''; ?>>Semanal</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Por favor seleccione el tipo de período.
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="year" class="form-label required">Año</label>
                                    <input 
                                        type="number" 
                                        class="form-control" 
                                        id="year" 
                                        name="year" 
                                        value="<?php echo htmlspecialchars(Input::post('year', $period->year), ENT_QUOTES, 'UTF-8'); ?>"
                                        required
                                        min="2020"
                                        max="2050"
                                        <?php echo !$period->can_edit() ? 'readonly' : ''; ?>>
                                    <div class="invalid-feedback">
                                        Por favor ingrese un año válido (2020-2050).
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="period_number" class="form-label required">Número de Período</label>
                                    <input 
                                        type="number" 
                                        class="form-control" 
                                        id="period_number" 
                                        name="period_number" 
                                        value="<?php echo htmlspecialchars(Input::post('period_number', $period->period_number), ENT_QUOTES, 'UTF-8'); ?>"
                                        required
                                        min="1"
                                        max="24"
                                        <?php echo !$period->can_edit() ? 'readonly' : ''; ?>>
                                    <div class="invalid-feedback">
                                        Por favor ingrese un número válido (1-24).
                                    </div>
                                    <small class="form-text text-muted">
                                        Mensual: 1-12, Quincenal: 1-24, Semanal: 1-52
                                    </small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="start_date" class="form-label required">Fecha de Inicio</label>
                                    <input 
                                        type="date" 
                                        class="form-control" 
                                        id="start_date" 
                                        name="start_date" 
                                        value="<?php echo htmlspecialchars(Input::post('start_date', date('Y-m-d', $period->start_date)), ENT_QUOTES, 'UTF-8'); ?>"
                                        required
                                        <?php echo !$period->can_edit() ? 'readonly' : ''; ?>>
                                    <div class="invalid-feedback">
                                        Por favor ingrese la fecha de inicio.
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="end_date" class="form-label required">Fecha de Fin</label>
                                    <input 
                                        type="date" 
                                        class="form-control" 
                                        id="end_date" 
                                        name="end_date" 
                                        value="<?php echo htmlspecialchars(Input::post('end_date', date('Y-m-d', $period->end_date)), ENT_QUOTES, 'UTF-8'); ?>"
                                        required
                                        <?php echo !$period->can_edit() ? 'readonly' : ''; ?>>
                                    <div class="invalid-feedback">
                                        Por favor ingrese la fecha de fin.
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="payment_date" class="form-label required">Fecha de Pago</label>
                                    <input 
                                        type="date" 
                                        class="form-control" 
                                        id="payment_date" 
                                        name="payment_date" 
                                        value="<?php echo htmlspecialchars(Input::post('payment_date', date('Y-m-d', $period->payment_date)), ENT_QUOTES, 'UTF-8'); ?>"
                                        required
                                        <?php echo !$period->can_edit() ? 'readonly' : ''; ?>>
                                    <div class="invalid-feedback">
                                        Por favor ingrese la fecha de pago.
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Descripción (Opcional)</label>
                                <textarea 
                                    class="form-control" 
                                    id="description" 
                                    name="description" 
                                    rows="3"
                                    maxlength="500"
                                    <?php echo !$period->can_edit() ? 'readonly' : ''; ?>
                                    placeholder="Descripción o notas adicionales..."><?php echo htmlspecialchars(Input::post('description', $period->description), ENT_QUOTES, 'UTF-8'); ?></textarea>
                                <small class="form-text text-muted">
                                    Máximo 500 caracteres
                                </small>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Estado Actual:</strong> <?php echo $period->get_status_badge(); ?>
                                <br>
                                <strong>Creado:</strong> <?php echo date('d/m/Y H:i', $period->created_at); ?>
                                <?php if ($period->updated_at): ?>
                                    | <strong>Última actualización:</strong> <?php echo date('d/m/Y H:i', $period->updated_at); ?>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="<?php echo Uri::create('admin/nomina/view/' . $period->id); ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Cancelar
                                </a>
                                
                                <?php if ($period->can_edit()): ?>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Guardar Cambios
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-primary" disabled>
                                        <i class="fas fa-lock"></i> No Editable
                                    </button>
                                <?php endif; ?>
                            </div>

                        <?php echo Form::close(); ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Información del Estado -->
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-info-circle"></i> Estado del Período</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Estado:</strong><br>
                            <?php echo $period->get_status_badge(); ?>
                        </div>
                        
                        <?php if ($period->status != 'draft'): ?>
                            <div class="alert alert-warning mb-0">
                                <small>
                                    <i class="fas fa-lock"></i>
                                    Este período ya fue procesado y no puede ser editado.
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Ayuda -->
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0"><i class="fas fa-question-circle"></i> Ayuda</h6>
                    </div>
                    <div class="card-body">
                        <h6>Tipos de Período:</h6>
                        <ul class="small">
                            <li><strong>Mensual:</strong> 1 período por mes (12 al año)</li>
                            <li><strong>Quincenal:</strong> 2 períodos por mes (24 al año)</li>
                            <li><strong>Semanal:</strong> 52 períodos al año</li>
                        </ul>

                        <h6 class="mt-3">Estados de Nómina:</h6>
                        <ul class="small">
                            <li><span class="badge bg-secondary">Borrador</span> - Puede ser editado</li>
                            <li><span class="badge bg-info">Calculada</span> - No editable</li>
                            <li><span class="badge bg-success">Aprobada</span> - No editable</li>
                            <li><span class="badge bg-primary">Pagada</span> - No editable</li>
                            <li><span class="badge bg-dark">Cerrada</span> - No editable</li>
                        </ul>

                        <div class="alert alert-info mt-3 mb-0">
                            <small>
                                <i class="fas fa-lightbulb"></i>
                                <strong>Tip:</strong> Solo los períodos en estado "Borrador" pueden ser editados. 
                                Una vez calculados, no es posible modificar las fechas ni el tipo de período.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-label.required:after {
            content: " *";
            color: red;
        }
    </style>

    <script>
        // Validación de formularios Bootstrap 5
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()

        // Validar que end_date sea mayor que start_date
        document.getElementById('end_date').addEventListener('change', function() {
            var startDate = document.getElementById('start_date').value;
            var endDate = this.value;
            
            if (startDate && endDate && endDate < startDate) {
                this.setCustomValidity('La fecha de fin debe ser posterior a la fecha de inicio');
            } else {
                this.setCustomValidity('');
            }
        });

        // Validar que payment_date sea mayor o igual que end_date
        document.getElementById('payment_date').addEventListener('change', function() {
            var endDate = document.getElementById('end_date').value;
            var paymentDate = this.value;
            
            if (endDate && paymentDate && paymentDate < endDate) {
                this.setCustomValidity('La fecha de pago debe ser posterior o igual a la fecha de fin');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
