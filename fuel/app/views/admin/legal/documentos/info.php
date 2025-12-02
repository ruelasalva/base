<!-- CONTENT -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Documentos Legales</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/legal/documentos', 'Documentos Legales'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Detalle Documento</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/legal/documentos/editar/'.$doc->id, '<i class="fa-solid fa-pen-to-square"></i> Editar', ['class'=>'btn btn-sm btn-warning']); ?>
                    <?php echo Html::anchor('admin/legal/documentos', '<i class="fa-solid fa-arrow-left"></i> Volver', ['class'=>'btn btn-sm btn-neutral']); ?>
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
                <!-- DETALLE DEL DOCUMENTO -->
                <div class="card shadow">
                    <div class="card-header border-0">
                        <h3 class="mb-0"><i class="fa-solid fa-file-contract text-primary"></i> Detalle Documento Legal</h3>
                    </div>

                    <div class="card-body">
                        <!-- TÍTULO -->
                        <div class="form-group">
                            <?php echo Form::label('Título', 'title'); ?>
                            <span class="form-control"><?php echo $doc->title; ?></span>
                        </div>

                        <!-- CATEGORÍA -->
                        <div class="form-group">
                            <?php echo Form::label('Categoría', 'category'); ?>
                            <span class="form-control"><?php echo ucfirst($doc->category); ?></span>
                        </div>

                        <!-- TIPO -->
                        <div class="form-group">
                            <?php echo Form::label('Tipo', 'type'); ?>
                            <span class="form-control"><?php echo str_replace('_',' ', ucfirst($doc->type)); ?></span>
                        </div>

                        <!-- SHORTCODE -->
                        <div class="form-group">
                            <?php echo Form::label('Shortcode', 'shortcode'); ?>
                            <span class="form-control"><?php echo $doc->shortcode; ?></span>
                        </div>

                        <!-- CONTENIDO -->
                        <div class="form-group">
                            <?php echo Form::label('Contenido', 'content'); ?>
                            <div class="p-3 border rounded bg-light" style="max-height: 400px; overflow:auto;">
                                <?php echo $doc->content ? html_entity_decode($doc->content) : '<em class="text-muted">Sin contenido</em>'; ?>
                            </div>
                        </div>

                        <!-- ARCHIVO ORIGINAL -->
                        <div class="form-group">
                            <?php echo Form::label('Archivo Original', 'upload_path'); ?>
                            <?php if ($doc->upload_path): ?>
                                <?php echo Html::anchor(Uri::base().$doc->upload_path, '<i class="fa-solid fa-file"></i> Descargar archivo original', ['class'=>'btn btn-sm btn-info', 'target'=>'_blank']); ?>
                            <?php else: ?>
                                <span class="form-control text-muted">No se subió archivo</span>
                            <?php endif; ?>
                        </div>

                        <!-- FLAGS -->
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <?php echo Form::label('Editable en Admin', 'allow_edit'); ?>
                                <span class="form-control">
                                    <?php echo $doc->allow_edit == 0 ? '<i class="fa-solid fa-check text-success"></i> Sí' : '<i class="fa-solid fa-xmark text-danger"></i> No'; ?>
                                </span>
                            </div>
                            <div class="form-group col-md-3">
                                <?php echo Form::label('Descargable en Frontend', 'allow_download'); ?>
                                <span class="form-control">
                                    <?php echo $doc->allow_download == 0 ? '<i class="fa-solid fa-check text-success"></i> Sí' : '<i class="fa-solid fa-xmark text-danger"></i> No'; ?>
                                </span>
                            </div>
                            <div class="form-group col-md-3">
                                <?php echo Form::label('Activo', 'active'); ?>
                                <span class="form-control">
                                    <?php echo $doc->active == 0 ? '<span class="badge badge-success">Sí</span>' : '<span class="badge badge-danger">No</span>'; ?>
                                </span>
                            </div>
                            <div class="form-group col-md-3">
                                <?php echo Form::label('Obligatorio', 'required'); ?>
                                <span class="form-control">
                                    <?php echo $doc->required == 1 ? '<span class="badge badge-success">Sí</span>' : '<span class="badge badge-danger">No</span>'; ?>
                                </span>
                            </div>
                        </div>

                        <!-- AUDITORÍA -->
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <?php echo Form::label('Versión', 'version'); ?>
                                <span class="form-control"><?php echo $doc->version; ?></span>
                            </div>
                            <div class="form-group col-md-4">
                                <?php echo Form::label('Fecha de creación', 'created_at'); ?>
                                <span class="form-control"><?php echo Date::forge($doc->created_at)->format('%d/%m/%Y %H:%M'); ?></span>
                            </div>
                            <div class="form-group col-md-4">
                                <?php echo Form::label('Última actualización', 'updated_at'); ?>
                                <span class="form-control"><?php echo Date::forge($doc->updated_at)->format('%d/%m/%Y %H:%M'); ?></span>
                            </div>
                        </div>

                        <!-- ÚLTIMA ACCIÓN -->
                        <?php if (!empty($last_change)): ?>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <?php echo Form::label('Última acción', 'last_change'); ?>
                                <span class="form-control">
                                    <?php if ($last_change['change_type'] == 'archivo'): ?>
                                        <span class="badge badge-primary"><i class="fa-solid fa-file"></i> Archivo nuevo cargado</span>
                                    <?php else: ?>
                                        <span class="badge badge-info"><i class="fa-solid fa-pen"></i> Edición de contenido</span>
                                    <?php endif; ?>
                                    el <?php echo Date::forge($last_change['updated_at'])->format('%d/%m/%Y %H:%M'); ?>
                                </span>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- BOTONES PDF -->
                        <?php if ($doc->allow_download == 0): ?>
                        <div class="form-group d-flex gap-2">
                            <?php echo Html::anchor('admin/legal/documentos/download/'.$doc->id.'?mode=download', '<i class="fa-solid fa-file-arrow-down"></i> Descargar PDF', ['class'=>'btn btn-sm btn-success mr-2']); ?>
                            <?php echo Html::anchor('admin/legal/documentos/download/'.$doc->id.'?mode=preview', '<i class="fa-solid fa-eye"></i> Ver en navegador', ['class'=>'btn btn-sm btn-primary', 'target'=>'_blank']); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- HISTÓRICO DE VERSIONES -->
                <div class="card mt-4">
                    <div class="card-header border-0">
                        <h3 class="mb-0"><i class="fa-solid fa-clock-rotate-left text-secondary"></i> Histórico de Versiones</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($versions)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Versión</th>
                                            <th>Cambio</th>
                                            <th>Título</th>
                                            <th>Categoría</th>
                                            <th>Tipo</th>
                                            <th>Fecha</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($versions as $v): ?>
                                        <tr>
                                            <td><?php echo $v['version']; ?></td>
                                            <td>
                                                <?php if ($v['change_type'] == 'archivo'): ?>
                                                    <span class="badge badge-primary"><i class="fa-solid fa-file"></i> Archivo</span>
                                                <?php else: ?>
                                                    <span class="badge badge-info"><i class="fa-solid fa-pen"></i> Edición</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $v['title']; ?></td>
                                            <td><?php echo ucfirst($v['category']); ?></td>
                                            <td><?php echo str_replace('_',' ', ucfirst($v['type'])); ?></td>
                                            <td><?php echo Date::forge($v['updated_at'])->format('%d/%m/%Y %H:%M'); ?></td>
                                            <td>
                                                <?php echo Html::anchor('admin/legal/documentos/version/'.$v['id'], '<i class="fa-solid fa-eye"></i> Ver', ['class'=>'btn btn-sm btn-info']); ?>
                                                <?php if ($v['upload_path']): ?>
                                                    <?php echo Html::anchor(Uri::base().$v['upload_path'], '<i class="fa-solid fa-file"></i> Archivo', ['class'=>'btn btn-sm btn-secondary', 'target'=>'_blank']); ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No hay versiones previas registradas.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- /HISTÓRICO DE VERSIONES -->
            </div>
        </div>
    </div>
</div>
