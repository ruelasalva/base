<!-- HEADER y BREADCRUMB igual que siempre --> 
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Compras</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/compras/rep', 'REP'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Agregar</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col">
            <div class="card-wrapper">
                <div class="card">
                    <div class="card-header bg-light">
                        <h3 class="mb-0 font-weight-bold text-primary">Agregar REP (Recibo Electrónico de Pago)</h3>
                    </div>
                    <div class="card-body">

                        <!-- ERRORES PROFESIONALES -->
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $e): ?>
                                        <li><i class="fa fa-exclamation-triangle"></i> <?php echo is_object($e) ? $e->get_message() : $e; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <!-- SI LLEGÓ DESDE FACTURA: muestra datos fijos de la factura y bloque para asociar más -->
                        <?php if (isset($bill) && $bill): ?>
                            <!-- CARD AZUL -->
                            <div class="alert mb-4" style="background:#28dbf3;color:#fff;padding:16px 20px;border-radius:8px;">
                                <b>Factura relacionada:</b> <?= $bill->uuid; ?><br>
                                <b>Proveedor:</b> <?= $bill->provider->name; ?><br>
                                <b>Monto:</b> $<?= number_format($bill->total, 2); ?><br>
                                <b>Estatus:</b> <?= ($bill->status == 1 ? "Pagada" : "Pendiente"); ?>
                            </div>
                            <?php
                            // OTRAS FACTURAS PENDIENTES DE ESE PROVEEDOR
                            $facturas_pendientes = isset($facturas_pendientes) ? $facturas_pendientes : [];
                            ?>
                            <?php if (!empty($facturas_pendientes)): ?>
                                <div class="card mb-4 shadow-sm border border-info" style="background:#e7faff;">
                                    <div class="card-header py-2" style="background:#d4f3fc;">
                                        <i class="fa fa-plus-circle text-info"></i>
                                        <b>Otras facturas pendientes de este proveedor:</b>
                                        <span class="small ml-1" style="color:#17607a;">(Selecciona las que quieras asociar al REP)</span>
                                    </div>
                                    <div class="card-body py-2">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover mb-0">
                                                <thead>
                                                    <tr class="bg-light">
                                                        <th></th>
                                                        <th>UUID</th>
                                                        <th>Monto</th>
                                                        <th>Fecha</th>
                                                        <th>Estatus</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($facturas_pendientes as $f): ?>
                                                    <tr>
                                                        <td class="align-middle">
                                                            <input type="checkbox" name="bills[]" value="<?= $f->id; ?>">
                                                        </td>
                                                        <td class="align-middle text-monospace"><?= $f->uuid; ?></td>
                                                        <td class="align-middle">$<?= number_format($f->total,2); ?></td>
                                                        <td class="align-middle"><?= date('d/m/Y', strtotime($f->created_at)); ?></td>
                                                        <td class="align-middle">
                                                            <span class="badge badge-warning px-2 py-1" style="font-size:90%;">Pendiente</span>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <small class="form-text text-muted mt-2">
                                            <i class="fa fa-info-circle"></i>
                                            Solo puedes asociar facturas pendientes de este proveedor. El REP debe contener los UUID de todas las facturas seleccionadas.
                                        </small>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- FORMULARIO -->
                        <?php echo Form::open(['enctype' => 'multipart/form-data']); ?>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label>Archivo XML REP <span class="text-danger">*</span></label>
                                <?php echo Form::file('xml_file', ['class' => 'form-control']); ?>
                                <small class="form-text text-muted">
                                    El sistema leerá el XML y mostrará las facturas relacionadas y el detalle del pago.
                                </small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Archivo PDF REP (opcional)</label>
                                <?php echo Form::file('pdf_file', ['class' => 'form-control']); ?>
                            </div>
                        </div>

                        <!-- DESPUÉS DE PROCESAR EL XML -->
                        <?php if (isset($rep_xml) && $rep_xml): ?>
                            <!-- Serializa el contenido del REP para guardarlo en el POST de "guardar" -->
                            <?php echo Form::hidden('rep_xml', base64_encode(serialize($rep_xml))); ?>
                            <div class="alert" style="background:#40d68b;color:#fff;padding:12px 20px;border-radius:8px;">
                                <b>REP UUID:</b> <span style="color:#fff;font-weight:bold"><?= $rep_xml['uuid_rep']; ?></span><br>
                                <b>Fecha de pago:</b> <span style="color:#fff;font-weight:bold"><?= $rep_xml['payment_date']; ?></span><br>
                                <b>Monto del REP:</b> <span style="color:#fff;font-weight:bold">$<?= number_format($rep_xml['rep_total'],2); ?></span>
                            </div>
                            <!-- TABLA DE FACTURAS DEL REP (SELECCIONABLES) -->
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered table-sm">
                                    <thead class="thead-light">
                                        <tr>
                                            <th></th>
                                            <th>UUID Factura</th>
                                            <th>Proveedor</th>
                                            <th>Monto Factura</th>
                                            <th>Monto Pagado (REP)</th>
                                            <th>Diferencia</th>
                                            <th>Estado de pago</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $total_facturas = 0;
                                        $total_pago_rep = $rep_xml['rep_total'];
                                        foreach ($rep_xml['facturas'] as $fact) {
                                            $total_facturas += $fact['monto_factura'] ?? 0;
                                            $diferencia = ($fact['monto_rep'] ?? 0) - ($fact['monto_factura'] ?? 0);
                                            $badge = '';
                                            if ($diferencia < 0)
                                                $badge = '<span class="badge badge-dark">Incompleto</span>';
                                            elseif ($diferencia > 0)
                                                $badge = '<span class="badge badge-danger">Excedente</span>';
                                            else
                                                $badge = '<span class="badge badge-success">Exacto</span>';
                                            ?>
                                            <tr>
                                                <td class="align-middle">
                                                    <?php if ($fact['existe']): ?>
                                                        <input type="checkbox" name="bills[]" value="<?= $fact['id']; ?>" checked>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="align-middle"><?php echo $fact['uuid']; ?></td>
                                                <td class="align-middle"><?php echo $fact['proveedor']; ?></td>
                                                <td class="align-middle">$<?php echo number_format($fact['monto_factura'] ?? 0,2); ?></td>
                                                <td class="align-middle">$<?php echo number_format($fact['monto_rep'] ?? 0,2); ?></td>
                                                <td class="align-middle">
                                                    <?php
                                                    if ($diferencia > 0) echo '<b style="color:#d0021b">+'.number_format($diferencia,2).'</b>';
                                                    elseif ($diferencia < 0) echo '<b>'.number_format($diferencia,2).'</b>';
                                                    else echo '0.00';
                                                    ?>
                                                </td>
                                                <td class="align-middle"><?php echo $badge; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    <tfoot class="font-weight-bold">
                                        <tr>
                                            <td colspan="3" class="text-right">Total facturas:</td>
                                            <td>$<?php echo number_format($total_facturas, 2); ?></td>
                                            <td>$<?php echo number_format($total_pago_rep, 2); ?></td>
                                            <td>
                                                <?php
                                                $diferencia_total = $total_pago_rep - $total_facturas;
                                                if ($diferencia_total > 0)
                                                    echo '<b style="color:#d0021b">+'.number_format($diferencia_total,2).'</b>';
                                                elseif ($diferencia_total < 0)
                                                    echo '<b>'.number_format($diferencia_total,2).'</b>';
                                                else
                                                    echo '0.00';
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($diferencia_total < 0)
                                                    echo '<span class="badge badge-dark">Pago menor</span>';
                                                elseif ($diferencia_total > 0)
                                                    echo '<span class="badge badge-danger">Pago excedente</span>';
                                                else
                                                    echo '<span class="badge badge-success">Pago exacto</span>';
                                                ?>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="form-row mt-4">
                                <div class="col">
                                    <button type="submit" name="guardar_rep" value="1" class="btn btn-success">
                                        Guardar REP</button>
                                    <?php echo Html::anchor('admin/compras/rep', 'Cancelar', ['class'=>'btn btn-secondary ml-2']); ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="form-row mt-4">
                                <div class="col">
                                    <button type="submit" class="btn btn-success">Procesar XML</button>
                                    <?php echo Html::anchor('admin/compras/rep', 'Cancelar', ['class'=>'btn btn-secondary ml-2']); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php echo Form::close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
