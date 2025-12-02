<!-- HEADER -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white mb-0">Detalle del Contrato</h6>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?= Html::anchor('admin/legal/contratos', '<i class="fas fa-arrow-left"></i> Volver', ['class' => 'btn btn-secondary']); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CONTENIDO -->
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col-lg-10 mx-auto">

            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">
                        <i class="fas fa-file-contract text-info"></i>
                        <?= e($contract->title); ?>
                    </h3>
                    <span>
                        <?= Model_Legal_Contract::status_label($contract->status); ?>
                    </span>
                </div>

                <div class="card-body">
                    <!-- INFORMACIÓN GENERAL -->
                    <h5 class="text-muted mb-3"><i class="fas fa-info-circle"></i> Información General</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-sm">
                            <tbody>
                                <tr>
                                    <th style="width: 25%;">Código</th>
                                    <td><?= e($contract->code ?: '---'); ?></td>
                                </tr>
                                <tr>
                                    <th>Categoría</th>
                                    <td>
                                        <?php
                                            $cats = [
                                                'provider' => 'Proveedor',
                                                'employee' => 'Empleado',
                                                'customer' => 'Cliente',
                                                'external' => 'Externo'
                                            ];
                                            echo e($cats[$contract->category] ?? ucfirst($contract->category));
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Usuario asignado</th>
                                    <td>
                                        <?php if (!empty($contract->user)): ?>
                                            <i class="fas fa-user"></i> <?= e($contract->user->username); ?>
                                            <small class="text-muted">(<?= e($contract->user->email); ?>)</small>
                                        <?php else: ?>
                                            <em>No asignado</em>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tipo de Documento</th>
                                    <td><?= !empty($contract->type) ? e($contract->type->name) : '---'; ?></td>
                                </tr>
                                <tr>
                                    <th>Contrato Global</th>
                                    <td>
                                        <?php if ($contract->is_global): ?>
                                            <span class="badge badge-success"><i class="fas fa-check"></i> Sí</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">No</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- FECHAS -->
                    <h5 class="text-muted mb-3"><i class="fas fa-calendar-alt"></i> Vigencia</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-sm">
                            <tbody>
                                <tr>
                                    <th style="width: 25%;">Fecha Inicio</th>
                                    <td><?= $contract->start_date ? date('d/m/Y', strtotime($contract->start_date)) : '---'; ?></td>
                                </tr>
                                <tr>
                                    <th>Fecha Fin</th>
                                    <td><?= $contract->end_date ? date('d/m/Y', strtotime($contract->end_date)) : '---'; ?></td>
                                </tr>
                                <tr>
                                    <th>Última actualización</th>
                                    <td><?= date('d/m/Y H:i', $contract->updated_at); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- DESCRIPCIÓN / CONTENIDO -->
                    <h5 class="text-muted mb-3"><i class="fas fa-align-left"></i> Contenido</h5>
                    <div class="border rounded p-3 mb-4" style="background-color:#f9f9f9;">
                        <?= !empty($contract->description) ? $contract->description : '<em>Sin contenido redactado.</em>'; ?>
                    </div>

                    <!-- ARCHIVO -->
                    <h5 class="text-muted mb-3"><i class="fas fa-file-pdf"></i> Archivo</h5>
                    <?php if (!empty($contract->file_path) && file_exists(DOCROOT . $contract->file_path)): ?>
                        <div class="text-center mb-4">
                            <a href="<?= Uri::base(false) . $contract->file_path; ?>" target="_blank" class="btn btn-danger">
                                <i class="fas fa-file-download"></i> Descargar PDF
                            </a>
                        </div>
                        <div class="embed-responsive embed-responsive-16by9 border">
                            <iframe class="embed-responsive-item" src="<?= Uri::base(false) . $contract->file_path; ?>" allowfullscreen></iframe>
                        </div>
                    <?php else: ?>
                        <p class="text-muted"><em>No hay archivo PDF disponible para este contrato.</em></p>
                    <?php endif; ?>

                    <!-- AUTORIZACIÓN -->
                    <h5 class="text-muted mt-4 mb-3"><i class="fas fa-user-check"></i> Autorización</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <tbody>
                                <tr>
                                    <th style="width: 25%;">Autorizado por</th>
                                    <td>
                                        <?php if (!empty($contract->authorizer)): ?>
                                            <?= e($contract->authorizer->username); ?>
                                            <small class="text-muted">(<?= e($contract->authorizer->email); ?>)</small>
                                        <?php else: ?>
                                            <em>No registrado</em>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Estado actual</th>
                                    <td><?= Model_Legal_Contract::status_label($contract->status); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-right mt-4">
                        <?= Html::anchor('admin/legal/contratos/editar/'.$contract->id, '<i class="fas fa-edit"></i> Editar', ['class' => 'btn btn-warning']); ?>
                        <?= Html::anchor('admin/legal/contratos', '<i class="fas fa-list"></i> Volver al listado', ['class' => 'btn btn-secondary']); ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
