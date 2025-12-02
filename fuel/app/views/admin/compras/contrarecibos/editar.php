<!-- ENCABEZADO VISUAL ESTILO SAJOR -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-8 col-7">
                    <h4 class="text-white mb-0 font-weight-bold">
                        Editar Contrarecibo: <?php echo $contrarecibo->receipt_number; ?> 
                        <span class="text-white-50 small font-weight-normal">(Proveedor: <?php echo $contrarecibo->provider->name; ?>)</span>
                    </h4>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block mt-1">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item"><?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?></li>
                            <li class="breadcrumb-item"><?php echo Html::anchor('admin/compras/contrarecibos', 'Contrarecibos'); ?></li>
                            <li class="breadcrumb-item"><?php echo Html::anchor('admin/compras/contrarecibos/info/'.$contrarecibo->id, $contrarecibo->receipt_number); ?></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-4 col-5 text-right">
                    <?php echo Html::anchor('admin/compras/contrarecibos/info/'.$contrarecibo->id, '<i class="fas fa-arrow-left"></i> Volver', array('class' => 'btn btn-neutral btn-sm shadow-sm')); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FORMULARIO DE EDICIÓN -->
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col-xl-8 order-xl-1">
            <div class="card shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Información del Contrarecibo</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php echo Form::open(array('action' => 'admin/compras/contrarecibos/editar/'.$contrarecibo->id, 'method' => 'post')); ?>
                        <?php if (isset($errores)): ?>
                            <div class="alert alert-danger" role="alert">
                                <strong>¡Error!</strong> Por favor, corrige los siguientes problemas:
                                <ul>
                                    <?php foreach ($errores as $campo => $mensaje): ?>
                                        <li><?php echo $mensaje; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <h6 class="heading-small text-muted mb-4">Datos Generales</h6>
                        <div class="pl-lg-4">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <?php echo Form::label('Folio del Contrarecibo', 'receipt_number'); ?>
                                        <?php echo Form::input('receipt_number', $contrarecibo->receipt_number, array('class' => 'form-control', 'disabled' => 'disabled')); ?>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <?php echo Form::label('Proveedor', 'provider_name'); ?>
                                        <?php echo Form::input('provider_name', $contrarecibo->provider->name, array('class' => 'form-control', 'disabled' => 'disabled')); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <?php echo Form::label('Total del Contrarecibo', 'total'); ?>
                                        <?php echo Form::input('total', '$' . number_format($contrarecibo->total, 2), array('class' => 'form-control', 'disabled' => 'disabled')); ?>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <?php echo Form::label('Fecha de Recibo', 'receipt_date'); ?>
                                        <?php echo Form::input('receipt_date', date('Y-m-d', $contrarecibo->receipt_date), array('class' => 'form-control', 'disabled' => 'disabled')); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <?php echo Form::label('Estatus', 'status'); ?>
                                        <?php echo Form::select('status', Input::post('status', $contrarecibo->status), $status_options, array('class' => 'form-control', 'id' => 'form_status')); ?>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <?php echo Form::label('Fecha de Pago (Real)', 'payment_date'); ?>
                                        <?php echo Form::input('payment_date', Input::post('payment_date', $contrarecibo->payment_date_actual ? date('Y-m-d', $contrarecibo->payment_date_actual) : ''), array('class' => 'form-control datepicker', 'placeholder' => 'YYYY-MM-DD', 'id' => 'form_payment_date')); ?>
                                        <small class="form-text text-muted">Solo si el estatus es "Pagado".</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?php echo Form::label('Notas', 'notes'); ?>
                                        <?php echo Form::textarea('notes', Input::post('notes', $contrarecibo->notes), array('class' => 'form-control', 'rows' => '3', 'placeholder' => 'Notas adicionales sobre el contrarecibo o el pago...')); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4" />
                        <h6 class="heading-small text-muted mb-4">Facturas Asociadas</h6>
                        <div class="pl-lg-4">
                            <div class="table-responsive">
                                <table class="table align-items-center table-flush">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>UUID</th>
                                            <th>Total</th>
                                            <th>OC Asociada</th>
                                            <th>Estatus Actual</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($contrarecibo->details)): ?>
                                            <?php foreach ($contrarecibo->details as $detail): ?>
                                                <?php if ($bill = $detail->bill): ?>
                                                    <tr>
                                                        <td><?php echo $bill->uuid; ?></td>
                                                        <td>$<?php echo number_format($bill->total, 2); ?></td>
                                                        <td><?php echo $bill->order ? $bill->order->code_order : 'N/A'; ?></td>
                                                        <td>
                                                            <?php
                                                            $status_text_bill = '';
                                                            $badge_class_bill = 'secondary';
                                                            switch ($bill->status) {
                                                                case 0: $status_text_bill = 'Pendiente'; $badge_class_bill = 'secondary'; break;
                                                                case 1: $status_text_bill = 'En revisión'; $badge_class_bill = 'warning'; break;
                                                                case 2: $status_text_bill = 'Pagada'; $badge_class_bill = 'success'; break;
                                                                case 3: $status_text_bill = 'Cancelada'; $badge_class_bill = 'danger'; break;
                                                                default: $status_text_bill = 'Desconocido'; break;
                                                            }
                                                            ?>
                                                            <span class="badge badge-<?php echo $badge_class_bill; ?>"><?php echo $status_text_bill; ?></span>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="4" class="text-center">No hay facturas asociadas a este contrarecibo.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <hr class="my-4" />
                        <div class="text-right">
                            <?php echo Form::submit('submit', 'Guardar Cambios', array('class' => 'btn btn-primary')); ?>
                        </div>
                    <?php echo Form::close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Script para inicializar el datepicker (si usas uno como jQuery UI o Bootstrap Datepicker)
    // Asegúrate de que la librería de datepicker esté cargada en tu plantilla principal.
    $(document).ready(function() {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            language: 'es' // Si tienes localización para el datepicker
        });

        // Habilitar/deshabilitar campo de fecha de pago según el estado seleccionado
        $('#form_status').on('change', function() {
            if ($(this).val() == '2') { // Si el estado es "Pagado" (2)
                $('#form_payment_date').prop('disabled', false);
            } else {
                $('#form_payment_date').prop('disabled', true);
                $('#form_payment_date').val(''); // Limpiar la fecha si no es pagado
            }
        }).trigger('change'); // Disparar al cargar para establecer el estado inicial
    });
</script>
