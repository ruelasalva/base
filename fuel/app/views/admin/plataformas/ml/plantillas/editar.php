<!-- ============================================================
     EDITAR PLANTILLA ML – editar.php
     ============================================================ -->

<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">

                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">
                        <i class="fa-brands fa-mercadolibre"></i> Editar Plantilla ML
                    </h6>

                    <nav aria-label="breadcrumb"
                         class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <a href="<?php echo Uri::create('admin'); ?>">
                                    <i class="fas fa-home"></i>
                                </a>
                            </li>

                            <li class="breadcrumb-item">
                                <a href="<?php echo Uri::create('admin/plataforma/ml'); ?>">
                                    Mercado Libre
                                </a>
                            </li>

                            <li class="breadcrumb-item">
                                <a href="<?php echo Uri::create('admin/plataforma/ml/plantillas?config_id='.$plantilla->configuration_id); ?>">
                                    Plantillas
                                </a>
                            </li>

                            <li class="breadcrumb-item active">Editar</li>
                        </ol>
                    </nav>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt--6">

    <div class="card">
        <div class="card-header">
            <h3 class="mb-0"><?php echo $plantilla->name; ?></h3>
        </div>

        <div class="card-body">

            <?php echo Form::open(['method' => 'post', 'action' => 'admin/plataforma/ml/plantillas/guardar']); ?>

            <?php echo Form::hidden('id', $plantilla->id); ?>
            <?php echo Form::hidden('configuration_id', $plantilla->configuration_id); ?>

            <!-- Nombre -->
            <div class="form-group">
                <?php echo Form::label('Nombre de la plantilla', 'name', ['class' => 'form-control-label']); ?>
                <?php echo Form::input('name', $plantilla->name, [
                    'class' => 'form-control',
                    'required' => true
                ]); ?>
            </div>

            <!-- Contenido -->
            <div class="form-group">
                <?php echo Form::label('Contenido HTML', 'description_html', ['class' => 'form-control-label']); ?>
                <?php echo Form::textarea('description_html', $plantilla->description_html, [
                    'class' => 'form-control',
                    'id' => 'editor-description',
                    'rows' => 10
                ]); ?>
            </div>

            <!-- Estado -->
            <div class="form-group">
                <?php echo Form::label('Estado', 'is_active', ['class' => 'form-control-label']); ?>
                <?php echo Form::select('is_active', $plantilla->is_active, [
                    1 => 'Activa',
                    0 => 'Inactiva'
                ], ['class' => 'form-control']); ?>
            </div>

            <!-- Botones -->
            <div class="text-right">
                <?php echo Html::anchor(
                    'admin/plataforma/ml/plantillas?config_id='.$plantilla->configuration_id,
                    'Cancelar',
                    ['class' => 'btn btn-secondary']
                ); ?>

                <button class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </div>

            <?php echo Form::close(); ?>

        </div>
    </div>

</div>

<!-- CKEditor -->
<script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>
<script>
ClassicEditor.create(document.querySelector('#editor-description'))
.catch(error => console.error(error));
</script>
