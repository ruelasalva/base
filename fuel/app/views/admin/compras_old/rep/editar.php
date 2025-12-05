<!-- HEADER Y BREADCRUMB -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Compras</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item"><?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?></li>
                            <li class="breadcrumb-item"><?php echo Html::anchor('admin/compras/rep', 'REP'); ?></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar REP</li>
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
                        <h3 class="mb-0 font-weight-bold text-primary">Editar REP (Recibo Electrónico de Pago)</h3>
                    </div>
                    <div class="card-body">

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $e): ?>
                                        <li><i class="fa fa-exclamation-triangle"></i> <?php echo $e; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <!-- INFO DE REP Y FACTURA -->
                        <div class="alert alert-info mb-4">
                            <b>UUID REP:</b> <?= $rep->uuid; ?><br>
                            <b>Factura:</b> <?= $bill ? $bill->uuid : '-'; ?><br>
                            <b>Proveedor:</b> <?= $provider ? $provider->name : '-'; ?>
                        </div>

                        <?php echo Form::open(['enctype' => 'multipart/form-data']); ?>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label>Fecha de pago <span class="text-danger">*</span></label>
                                <?php echo Form::input('payment_date', Input::post('payment_date', $rep->payment_date), ['class' => 'form-control', 'type' => 'date']); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Monto pagado <span class="text-danger">*</span></label>
                                <?php echo Form::input('amount_paid', Input::post('amount_paid', $rep->amount_paid), ['class' => 'form-control']); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Archivo PDF REP (opcional, reemplaza el existente)</label>
                                <?php echo Form::file('pdf_file', ['class' => 'form-control']); ?>
                                <?php if ($rep->pdf_file): ?>
                                    <small class="form-text">
                                        <a href="<?= Uri::base(false).'assets/rep_pdf/'.$rep->pdf_file ?>" target="_blank"><i class="fa fa-file-pdf"></i> Ver PDF actual</a>
                                    </small>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Archivo XML REP (opcional, reemplaza el existente)</label>
                                <?php echo Form::file('xml_file', ['class' => 'form-control']); ?>
                                <?php if ($rep->xml_file): ?>
                                    <small class="form-text">
                                        <a href="<?= Uri::base(false).'assets/rep_xml/'.$rep->xml_file ?>" target="_blank"><i class="fa fa-file-code"></i> Ver XML actual</a>
                                    </small>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Estatus</label>
                                <select name="status" class="form-control">
                                    <option value="1" <?= $rep->status == 1 ? 'selected' : ''; ?>>Activo</option>
                                    <option value="0" <?= $rep->status == 0 ? 'selected' : ''; ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row mt-4">
                            <div class="col">
                                <button type="submit" class="btn btn-success">Guardar cambios</button>
                                <?php echo Html::anchor('admin/compras/rep/info/' . $rep->id, 'Cancelar', ['class'=>'btn btn-secondary ml-2']); ?>
                            </div>
                        </div>
                        <?php echo Form::close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
