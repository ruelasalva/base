<!-- CONTENT -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Histórico de Documentos Legales</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/legal/documentos', 'Documentos Legales'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Versión <?php echo $version->version; ?></li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/legal/documentos/info/'.$version->document_id, '<i class="fa-solid fa-arrow-left"></i> Volver al Documento', ['class'=>'btn btn-sm btn-neutral']); ?>
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
                <div class="card shadow">
                    <div class="card-header border-0">
                        <h3 class="mb-0"><i class="fa-solid fa-clock-rotate-left text-secondary"></i> Versión <?php echo $version->version; ?></h3>
                    </div>

                    <div class="card-body">
                        <!-- TÍTULO -->
                        <div class="form-group">
                            <?php echo Form::label('Título', 'title'); ?>
                            <span class="form-control"><?php echo $version->title; ?></span>
                        </div>

                        <!-- CATEGORÍA -->
                        <div class="form-group">
                            <?php echo Form::label('Categoría', 'category'); ?>
                            <span class="form-control"><?php echo ucfirst($version->category); ?></span>
                        </div>

                        <!-- TIPO -->
                        <div class="form-group">
                            <?php echo Form::label('Tipo', 'type'); ?>
                            <span class="form-control"><?php echo str_replace('_',' ', ucfirst($version->type)); ?></span>
                        </div>

                        <!-- SHORTCODE -->
                        <div class="form-group">
                            <?php echo Form::label('Shortcode', 'shortcode'); ?>
                            <span class="form-control"><?php echo $version->shortcode; ?></span>
                        </div>

                        <!-- CONTENIDO -->
                        <div class="form-group">
                            <?php echo Form::label('Contenido', 'content'); ?>
                            <div class="p-3 border rounded bg-light" style="max-height: 400px; overflow:auto;">
                                <?php echo $version->content ? html_entity_decode($version->content) : '<em class="text-muted">Sin contenido</em>'; ?>
                            </div>
                        </div>

                        <!-- ARCHIVO -->
                        <div class="form-group">
                            <?php echo Form::label('Archivo asociado', 'upload_path'); ?>
                            <?php if ($version->upload_path): ?>
                                <?php echo Html::anchor($version->upload_path, '<i class="fa-solid fa-file"></i> Descargar archivo', ['class'=>'btn btn-sm btn-info', 'target'=>'_blank']); ?>
                            <?php else: ?>
                                <span class="form-control text-muted">No hay archivo asociado</span>
                            <?php endif; ?>
                        </div>

                        <!-- METADATOS -->
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <?php echo Form::label('Tipo de cambio', 'change_type'); ?>
                                <span class="form-control">
                                    <?php if ($version->change_type == 'archivo'): ?>
                                        <span class="badge badge-primary"><i class="fa-solid fa-file"></i> Archivo</span>
                                    <?php else: ?>
                                        <span class="badge badge-info"><i class="fa-solid fa-pen"></i> Edición</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="form-group col-md-4">
                                <?php echo Form::label('Fecha de creación', 'created_at'); ?>
                                <span class="form-control"><?php echo Date::forge($version->created_at)->format('%d/%m/%Y %H:%M'); ?></span>
                            </div>
                            <div class="form-group col-md-4">
                                <?php echo Form::label('Última actualización', 'updated_at'); ?>
                                <span class="form-control"><?php echo Date::forge($version->updated_at)->format('%d/%m/%Y %H:%M'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
