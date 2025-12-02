<!-- ================================================ -->
<!-- AGREGAR PLANTILLA ML                             -->
<!-- ================================================ -->

<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">

            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Nueva Plantilla ML</h6>

                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/plataforma/ml', '<i class="fas fa-home"></i>'); ?>
                            </li>

                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/plataforma/ml/panel/'.$config_id, 'Panel ML'); ?>
                            </li>

                            <li class="breadcrumb-item active">Nueva plantilla</li>
                        </ol>
                    </nav>
                </div>

                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor(
                        'admin/plataforma/ml/plantillas?config_id='.$config_id,
                        'Regresar',
                        ['class' => 'btn btn-secondary']
                    ); ?>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="container-fluid mt-4">

    <div class="row">
        <div class="col-lg-10 offset-lg-1">

            <div class="card shadow">
                <div class="card-header">
                    <h3 class="mb-0">Crear Plantilla</h3>
                </div>

                <div class="card-body">

                    <?php echo Form::open(['method' => 'post', 'action' => 'admin/plataforma/ml/plantillas/guardar']); ?>

                    <?php echo Form::hidden('configuration_id', $config_id); ?>

                    <!-- Nombre -->
                    <div class="form-group">
                        <?php echo Form::label('Nombre de la plantilla', 'name', ['class' => 'form-control-label']); ?>
                        <?php echo Form::input('name', '', [
                            'class' => 'form-control',
                            'placeholder' => 'Ej. Descripción para Papel',
                            'required'
                        ]); ?>
                    </div>

                    <!-- Editor HTML -->
                    <div class="form-group">
                        <?php echo Form::label('Contenido HTML', 'description_html', ['class' => 'form-control-label']); ?>

                        <?php echo Form::textarea('description_html', '', [
                            'class' => 'form-control',
                            'id'    => 'editor_html',
                            'rows'  => 12
                        ]); ?>
                    </div>

                    <!-- Activo -->
                    <div class="form-group">
                        <?php echo Form::label('Estado', 'is_active', ['class' => 'form-control-label']); ?>
                        <?php echo Form::select(
                            'is_active',
                            1,
                            ['1' => 'Activa', '0' => 'Inactiva'],
                            ['class' => 'form-control']
                        ); ?>
                    </div>

                    <div class="text-right">
                        <?php echo Form::submit('submit', 'Guardar', ['class' => 'btn btn-success']); ?>
                    </div>

                    <?php echo Form::close(); ?>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- ================================================ -->
<!-- CKEDITOR 5                                       -->
<!-- ================================================ -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.2/classic/ckeditor.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        ClassicEditor
            .create(document.querySelector('#editor_html'))
            .catch(err => console.error(err));
    });
</script>
