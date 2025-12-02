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
                            <li class="breadcrumb-item active" aria-current="page">Editar Documento</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/legal/documentos', '<i class="fa-solid fa-arrow-left"></i> Volver', ['class' => 'btn btn-sm btn-neutral']); ?>
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
                    <!-- CARD HEADER -->
                    <div class="card-header">
                        <h3 class="mb-0"><i class="fa-solid fa-file-pen text-warning"></i> Editar Documento Legal</h3>
                    </div>

                    <!-- CARD BODY -->
                    <div class="card-body">
                        <?php echo Form::open(['enctype' => 'multipart/form-data']); ?>

                        <!-- TÍTULO -->
                        <div class="form-group">
                            <?php echo Form::label('Título', 'title', ['class' => 'form-control-label']); ?>
                            <?php echo Form::input('title', Input::post('title', $doc->title), ['class' => 'form-control', 'required'=>'required']); ?>
                        </div>

                        <!-- CATEGORÍA -->
                        <div class="form-group">
                            <?php echo Form::label('Categoría', 'category', ['class' => 'form-control-label']); ?>
                            <?php 
                                echo Form::select('category', Input::post('category', $doc->category), [
                                    'cliente'   => 'Cliente',
                                    'proveedor' => 'Proveedor',
                                    'socio'     => 'Socio',
                                    'empleado'  => 'Empleado',
                                    'visitante' => 'Visitante',
                                    'general'   => 'General',
                                ], ['class'=>'form-control']); 
                            ?>
                        </div>

                        <!-- TIPO -->
                        <div class="form-group">
                            <?php echo Form::label('Tipo', 'type', ['class' => 'form-control-label']); ?>
                            <?php 
                                echo Form::select('type', Input::post('type', $doc->type), [
                                    'aviso_privacidad' => 'Aviso de Privacidad',
                                    'terminos'         => 'Términos y Condiciones',
                                    'politicas'        => 'Políticas',
                                    'cookies'          => 'Cookies',
                                    'newsletter'       => 'Newsletter',
                                    'medidas'          => 'Medidas Compensatorias',
                                    'codigo'           => 'Código de Buenas Prácticas',
                                    'otros'            => 'Otros',
                                ], ['class'=>'form-control']); 
                            ?>
                        </div>

                        <!-- SHORTCODE -->
                        <div class="form-group">
                            <?php echo Form::label('Shortcode', 'shortcode', ['class'=>'form-control-label']); ?>
                            <?php echo Form::input('shortcode', Input::post('shortcode', $doc->shortcode), ['class'=>'form-control']); ?>
                            <small class="form-text text-muted">Identificador único para invocar este documento desde formularios.</small>
                        </div>

                        <!-- CONTENIDO (CKEDITOR) -->
                        <div class="form-group">
                            <?php echo Form::label('Contenido', 'content', ['class'=>'form-control-label']); ?>
                            <?php echo Form::textarea('content', Input::post('content', $doc->content), [
                                'id'=>'content',
                                'class'=>'form-control',
                                'rows'=>12
                            ]); ?>
                        </div>

                        <!-- ARCHIVO ACTUAL -->
                        <div class="form-group">
                            <?php echo Form::label('Archivo Actual', 'upload_path', ['class'=>'form-control-label']); ?>
                            <?php if ($doc->upload_path): ?>
                                <p>
                                    <a href="<?php echo Uri::base().$doc->upload_path; ?>" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fa-solid fa-file"></i> Descargar archivo actual
                                    </a>
                                </p>
                            <?php else: ?>
                                <span class="form-control text-muted">No hay archivo asociado</span>
                            <?php endif; ?>
                        </div>

                        <!-- SUBIR NUEVO ARCHIVO -->
                        <div class="form-group">
                            <?php echo Form::label('Reemplazar archivo (doc/pdf)', 'upload_path', ['class'=>'form-control-label']); ?>
                            <?php echo Form::file('upload_path', ['class'=>'form-control-file']); ?>
                        </div>

                        <!-- FLAGS -->
                        <div class="form-check mb-2">
                            <?php echo Form::checkbox('allow_edit', 0, ($doc->allow_edit==0), ['id'=>'allow_edit', 'class'=>'form-check-input']); ?>
                            <?php echo Form::label('Editable en Admin', 'allow_edit', ['class'=>'form-check-label']); ?>
                        </div>
                        <div class="form-check mb-2">
                            <?php echo Form::checkbox('allow_download', 0, ($doc->allow_download==0), ['id'=>'allow_download', 'class'=>'form-check-input']); ?>
                            <?php echo Form::label('Descargable en Frontend', 'allow_download', ['class'=>'form-check-label']); ?>
                        </div>
                        <div class="form-check mb-4">
                            <?php echo Form::checkbox('active', 0, ($doc->active==0), ['id'=>'active', 'class'=>'form-check-input']); ?>
                            <?php echo Form::label('Activo', 'active', ['class'=>'form-check-label']); ?>
                        </div>
                        <!-- REQUIRED -->
                        <div class="form-check mb-4">
                            <?php echo Form::checkbox('required', 1, ($doc->required == 1), [
                                'id' => 'required',
                                'class' => 'form-check-input'
                            ]); ?>
                            <?php echo Form::label('Obligatorio para todos los usuarios', 'required', ['class' => 'form-check-label']); ?>
                        </div>


                        <!-- BOTONES -->
                        <div class="form-group">
                            <?php echo Form::submit('guardar', 'Guardar Cambios', ['class'=>'btn btn-primary']); ?>
                            <?php echo Html::anchor('admin/legal/documentos', 'Cancelar', ['class'=>'btn btn-secondary']); ?>
                        </div>

                        <?php echo Form::close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CKEDITOR -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const contentField = document.querySelector('#content');
    if (contentField) {
        ClassicEditor
            .create(contentField, {
                language: 'es',
                toolbar: {
                    items: [
                        'heading', '|',
                        'fontFamily', 'fontSize', 'fontColor', 'fontBackgroundColor', '|',
                        'bold', 'italic', 'underline', 'strikethrough', '|',
                        'alignment', '|',
                        'link', 'bulletedList', 'numberedList', '|',
                        'blockQuote', 'insertTable', 'mediaEmbed', '|',
                        'undo', 'redo'
                    ]
                }
            })
            .then(editor => {
                window.legalEditor = editor;
            })
            .catch(error => {
                console.error('Error al iniciar CKEditor en editar documento:', error);
            });
    }
});
</script>
