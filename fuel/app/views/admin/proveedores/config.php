<!-- Configuración de Facturación y Pago -->
<div class="row">
    <div class="col-12">
        <h1 class="page-header">
            <i class="fa fa-cog"></i> Configuración de Facturación y Pago
            <small>Parámetros para cálculo automático de contrarecibos</small>
        </h1>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-8 offset-lg-2">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fa fa-sliders-h"></i> Parámetros de Configuración
            </div>
            <div class="card-body">
                <?php echo Form::open(['action' => 'admin/proveedores/config', 'method' => 'post']); ?>

                <!-- Días válidos para recibir facturas -->
                <div class="form-group">
                    <label class="font-weight-bold">
                        <i class="fa fa-calendar-check"></i> Días Válidos para Recibir Facturas
                    </label>
                    <small class="form-text text-muted mb-2">
                        Seleccione los días de la semana en que se pueden recibir facturas
                    </small>
                    <div class="row">
                        <?php
                        $days = [
                            '1' => 'Lunes',
                            '2' => 'Martes',
                            '3' => 'Miércoles',
                            '4' => 'Jueves',
                            '5' => 'Viernes',
                            '6' => 'Sábado',
                            '7' => 'Domingo'
                        ];
                        $selected_days = !empty($config['invoice_receive_days']) 
                            ? explode(',', $config['invoice_receive_days']) 
                            : ['1','2','3','4','5'];
                        
                        foreach ($days as $value => $label):
                        ?>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           name="invoice_receive_days[]" 
                                           value="<?php echo $value; ?>" 
                                           id="day_<?php echo $value; ?>"
                                           <?php echo in_array($value, $selected_days) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="day_<?php echo $value; ?>">
                                        <?php echo $label; ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <hr>

                <!-- Hora límite de recepción -->
                <div class="form-group">
                    <label class="font-weight-bold" for="invoice_receive_limit_time">
                        <i class="fa fa-clock"></i> Hora Límite de Recepción
                    </label>
                    <small class="form-text text-muted mb-2">
                        Facturas subidas después de esta hora se considerarán del siguiente día hábil
                    </small>
                    <input type="time" 
                           class="form-control" 
                           id="invoice_receive_limit_time" 
                           name="invoice_receive_limit_time"
                           value="<?php echo $config['invoice_receive_limit_time'] ?? '14:00:00'; ?>"
                           required>
                </div>

                <hr>

                <!-- Términos de pago -->
                <div class="form-group">
                    <label class="font-weight-bold" for="payment_terms_days">
                        <i class="fa fa-calendar-plus"></i> Términos de Pago (Días de Crédito)
                    </label>
                    <small class="form-text text-muted mb-2">
                        Número de días hábiles para pagar desde la fecha de recepción oficial
                    </small>
                    <input type="number" 
                           class="form-control" 
                           id="payment_terms_days" 
                           name="payment_terms_days"
                           value="<?php echo $config['payment_terms_days'] ?? 30; ?>"
                           min="1" 
                           max="180"
                           required>
                </div>

                <hr>

                <!-- Días de pago permitidos -->
                <div class="form-group">
                    <label class="font-weight-bold">
                        <i class="fa fa-money-check-alt"></i> Días de Pago Permitidos
                    </label>
                    <small class="form-text text-muted mb-2">
                        Seleccione los días en que se pueden realizar pagos (fecha programada se ajustará)
                    </small>
                    <div class="row">
                        <?php
                        $selected_payment_days = !empty($config['payment_days']) 
                            ? explode(',', $config['payment_days']) 
                            : ['5'];
                        
                        foreach ($days as $value => $label):
                        ?>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           name="payment_days[]" 
                                           value="<?php echo $value; ?>" 
                                           id="pday_<?php echo $value; ?>"
                                           <?php echo in_array($value, $selected_payment_days) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="pday_<?php echo $value; ?>">
                                        <?php echo $label; ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <hr>

                <!-- Días festivos -->
                <div class="form-group">
                    <label class="font-weight-bold" for="holidays">
                        <i class="fa fa-umbrella-beach"></i> Días Festivos
                    </label>
                    <small class="form-text text-muted mb-2">
                        Ingrese las fechas festivas (una por línea, formato: YYYY-MM-DD)
                    </small>
                    <textarea class="form-control" 
                              id="holidays" 
                              name="holidays" 
                              rows="8"
                              placeholder="2024-12-25&#10;2025-01-01&#10;2025-05-01&#10;2025-09-16&#10;2025-12-25"><?php echo $config['holidays_text'] ?? ''; ?></textarea>
                    <small class="form-text text-info">
                        <i class="fa fa-info-circle"></i> Ejemplo: Navidad (2024-12-25), Año Nuevo (2025-01-01)
                    </small>
                </div>

                <hr>

                <!-- Opciones adicionales -->
                <div class="form-group">
                    <label class="font-weight-bold">
                        <i class="fa fa-toggle-on"></i> Opciones Adicionales
                    </label>
                    
                    <div class="custom-control custom-switch mb-2">
                        <input type="hidden" name="auto_generate_receipt" value="0">
                        <input type="checkbox" 
                               class="custom-control-input" 
                               id="auto_generate_receipt" 
                               name="auto_generate_receipt"
                               value="1"
                               <?php echo (isset($config['auto_generate_receipt']) && $config['auto_generate_receipt']) ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="auto_generate_receipt">
                            <strong>Generar Contrarecibo Automáticamente</strong>
                            <br><small class="text-muted">Al aprobar una factura, crear contrarecibo con fechas calculadas</small>
                        </label>
                    </div>

                    <div class="custom-control custom-switch mb-2">
                        <input type="hidden" name="require_purchase_order" value="0">
                        <input type="checkbox" 
                               class="custom-control-input" 
                               id="require_purchase_order" 
                               name="require_purchase_order"
                               value="1"
                               <?php echo (isset($config['require_purchase_order']) && $config['require_purchase_order']) ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="require_purchase_order">
                            <strong>Requerir Orden de Compra</strong>
                            <br><small class="text-muted">Validar que exista OC antes de aceptar factura</small>
                        </label>
                    </div>
                </div>

                <!-- Monto máximo sin OC -->
                <div class="form-group">
                    <label class="font-weight-bold" for="max_amount_without_po">
                        <i class="fa fa-dollar-sign"></i> Monto Máximo sin Orden de Compra
                    </label>
                    <small class="form-text text-muted mb-2">
                        Facturas con monto menor pueden procesarse sin OC
                    </small>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">$</span>
                        </div>
                        <input type="number" 
                               class="form-control" 
                               id="max_amount_without_po" 
                               name="max_amount_without_po"
                               value="<?php echo $config['max_amount_without_po'] ?? 5000; ?>"
                               min="0" 
                               step="0.01"
                               required>
                        <div class="input-group-append">
                            <span class="input-group-text">MXN</span>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Botones de acción -->
                <div class="form-group text-right">
                    <a href="/admin/proveedores" class="btn btn-secondary">
                        <i class="fa fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Guardar Configuración
                    </button>
                </div>

                <?php echo Form::close(); ?>
            </div>
        </div>

        <!-- Card de información -->
        <div class="card mt-3">
            <div class="card-header bg-info text-white">
                <i class="fa fa-info-circle"></i> Cómo Funciona el Cálculo Automático
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    <li class="mb-2">
                        <strong>Fecha de Recepción Oficial:</strong> Se calcula desde la fecha de subida del XML, 
                        considerando la hora límite y excluyendo días no válidos y festivos.
                    </li>
                    <li class="mb-2">
                        <strong>Fecha Programada de Pago:</strong> Se suman los días de crédito a la fecha de recepción 
                        (solo días hábiles), luego se ajusta al día de pago permitido más cercano.
                    </li>
                    <li class="mb-2">
                        <strong>Ejemplo:</strong> Si una factura se sube el Viernes a las 15:00 (después de las 14:00), 
                        se considera recibida el Lunes. Con 30 días de crédito y pago solo Viernes, la fecha programada 
                        será el Viernes 30 días hábiles después del Lunes.
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>

<style>
.custom-control-label {
    padding-top: 0.25rem;
}
</style>
