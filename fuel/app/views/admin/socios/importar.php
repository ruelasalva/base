<!-- CONTENT -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Socios de Negocios</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/socios', 'Socios de Negocios'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <?php echo Html::anchor('admin/socios/importar_csv', 'Importar CSV'); ?>
                            </li>
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
                    <div class="card-header">
                        <h3 class="mb-0">Importar Socios desde CSV</h3>
                    </div>
                    <div class="card-body">

                        <!-- Mostrar mensajes -->
                        <?php if (Session::get_flash('success')): ?>
                            <div class="alert alert-success">
                                <?php echo Session::get_flash('success'); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (Session::get_flash('error')): ?>
                            <div class="alert alert-danger">
                                <?php echo Session::get_flash('error'); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Formulario de subida -->
                        <?php echo Form::open(array('method' => 'post', 'enctype' => 'multipart/form-data')); ?>
                            <div class="form-group">
                                <?php echo Form::label('Seleccionar archivo CSV', 'archivo_csv', array('class' => 'form-control-label')); ?>
                                <?php echo Form::file('file', array('class' => 'form-control-file')); ?>

                            </div>
                            <button type="submit" class="btn btn-primary">Importar</button>
                            <?php echo Html::anchor('admin/socios', 'Cancelar', array('class' => 'btn btn-secondary ml-2')); ?>
                        <?php echo Form::close(); ?>

                        <?php if (Session::get_flash('csv_error_link')): ?>
                            <a href="<?php echo Session::get_flash('csv_error_link'); ?>" class="btn btn-outline-danger mt-3" target="_blank">
                                <i class="fas fa-file-csv"></i> Descargar CSV de errores
                            </a>
                        <?php endif; ?>

                        <hr>

                        <p class="mt-3"><strong>Formato esperado del CSV:</strong></p>
                        <pre><code>code_sap,name,email,password,rfc,type_id,customer_id,employee_id</code></pre>
                        
                        <?php echo Html::anchor(Uri::base(false).'assets/csv/ejemplo.csv', 'Descargar ejemplo', array('title' => 'Descargar ejemplo', 'class' => 'btn btn-outline-info', 'targer' => '_blank')); ?> 
                            
                        

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
