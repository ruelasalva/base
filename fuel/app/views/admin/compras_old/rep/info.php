<!-- HEADER Y BREADCRUMB -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-8 col-7">
                    <h4 class="text-white mb-0 font-weight-bold">
                        Detalle del REP
                    </h4>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block mt-1">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item"><?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?></li>
                            <li class="breadcrumb-item"><?php echo Html::anchor('admin/compras', 'Compras'); ?></li>
                            <li class="breadcrumb-item"><?php echo Html::anchor('admin/compras/rep', 'REP'); ?></li>
                            <li class="breadcrumb-item active" aria-current="page">Detalle REP</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-4 col-5 text-right">
                    <?php echo Html::anchor('admin/compras/rep', '<i class="fa fa-arrow-left"></i> Volver', ['class' => 'btn btn-secondary']); ?>
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
                        <h3 class="mb-0 font-weight-bold text-primary">
                            Información general del REP
                        </h3>
                    </div>
                    <div class="card-body">

                        <!-- DATOS GENERALES DEL REP -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">UUID REP:</label>
                                <div class="form-control-plaintext"><?= $rep->uuid; ?></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Fecha de pago:</label>
                                <div class="form-control-plaintext"><?= $rep->payment_date ? date('d/m/Y', strtotime($rep->payment_date)) : '-'; ?></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Monto pagado:</label>
                                <div class="form-control-plaintext">$<?= number_format($rep->amount_paid,2); ?></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Usuario que subió:</label>
                                <div class="form-control-plaintext"><?= $rep->user ? ($rep->user->username ?? $rep->user->email ?? '-') : '-'; ?></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Fecha de registro:</label>
                                <div class="form-control-plaintext"><?= $created_at ? date('d/m/Y H:i', $created_at) : '-'; ?></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Última actualización:</label>
                                <div class="form-control-plaintext"><?= $updated_at ? date('d/m/Y H:i', $updated_at) : '-'; ?></div>
                            </div>
                            <?php if ($rep->xml_file): ?>
                                <div class="col-md-6 mb-3">
                                    <label class="font-weight-bold">Archivo XML:</label>
                                    <a href="<?= $rep->xml_file; ?>" target="_blank" class="btn btn-link">Descargar XML</a>
                                </div>
                            <?php endif; ?>
                            <?php if ($rep->pdf_file): ?>
                                <div class="col-md-6 mb-3">
                                    <label class="font-weight-bold">Archivo PDF:</label>
                                    <a href="<?= $rep->pdf_file; ?>" target="_blank" class="btn btn-link">Descargar PDF</a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <hr>
                        <!-- FACTURAS ASOCIADAS AL REP -->
                        <h4 class="mb-2 font-weight-bold text-primary">Facturas asociadas a este REP</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="thead-light">
                                    <tr>
                                        <th>UUID Factura</th>
                                        <th>Proveedor</th>
                                        <th>Monto Factura</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($facturas_asociadas as $fact): ?>
                                        <tr>
                                            <td><?= $fact->provider_bill ? $fact->provider_bill->uuid : '-'; ?></td>
                                            <td><?= $fact->provider_bill && $fact->provider_bill->provider ? $fact->provider_bill->provider->company_name : '-'; ?></td>
                                            <td><?= $fact->provider_bill ? '$' . number_format($fact->provider_bill->total, 2) : '-'; ?></td>
                                            <td><?= $fact->provider_bill && $fact->provider_bill->created_at ? date('d/m/Y', strtotime($fact->provider_bill->created_at)) : '-'; ?></td>
                                            <td>
                                                <?php
                                                    if ($fact->provider_bill && $fact->provider_bill->status == 1) {
                                                        echo '<span class="badge badge-success">Pagada</span>';
                                                    } else {
                                                        echo '<span class="badge badge-warning">Pendiente</span>';
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($facturas_asociadas)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No hay facturas asociadas a este REP.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
