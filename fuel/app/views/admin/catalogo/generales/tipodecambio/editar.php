<!-- CONTENT -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Catálogo</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/catalogo/generales/tipodecambio', 'Tipos de Cambio'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/catalogo/generales/tipodecambio/info/'.$id, 'Ver'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <?php echo Html::anchor('admin/catalogo/generales/tipodecambio/editar/'.$id, 'Editar'); ?>
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/catalogo/generales/tipodecambio/info/'.$id, 'Ver', array('class' => 'btn btn-sm btn-neutral')); ?>
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
                        <h3 class="mb-0">Editar tipo de cambio</h3>
                    </div>
                    <div class="card-body">
                        <?php echo Form::open(array('method' => 'post')); ?>
                            <fieldset>
                                <div class="form-row">
                                    <div class="col-md-12 mt-0 mb-3">
                                        <legend class="mb-0 heading">Información del tipo de cambio</legend>
                                    </div>
                                    <!-- Moneda (solo lectura) -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <?php echo Form::label('Moneda', 'currency_id', array('class' => 'form-control-label')); ?>
                                            <?php
                                                $currency_name = '';
                                                if (isset($currencies) && isset($currency_id)) {
                                                    foreach($currencies as $currency) {
                                                        if ($currency->id == $currency_id) {
                                                            $currency_name = $currency->name;
                                                            break;
                                                        }
                                                    }
                                                }
                                            ?>
                                            <span class="form-control"><?php echo $currency_name; ?></span>
                                            <?php echo Form::hidden('currency_id', (isset($currency_id) ? $currency_id : '')); ?>
                                        </div>
                                    </div>
                                    <!-- Fecha (solo lectura) -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <?php echo Form::label('Fecha', 'date', array('class' => 'form-control-label')); ?>
                                            <span class="form-control"><?php echo isset($date) ? $date : ''; ?></span>
                                            <?php echo Form::hidden('date', (isset($date) ? $date : '')); ?>
                                        </div>
                                    </div>
                                    <!-- Tipo de cambio (editable) -->
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group <?php echo $classes['rate']['form-group']; ?>">
                                            <?php echo Form::label('Tipo de Cambio', 'rate', array('class' => 'form-control-label')); ?>
                                            <?php echo Form::input('rate', (isset($rate) ? $rate : ''), array(
                                                'id' => 'rate',
                                                'class' => 'form-control '.$classes['rate']['form-control'],
                                                'placeholder' => 'Ejemplo: 18.8332'
                                            )); ?>
                                            <?php if(isset($errors['rate'])) : ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $errors['rate']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <?php echo Form::submit(array('value'=> 'Guardar', 'name'=>'submit', 'class' => 'btn btn-primary submit-form')); ?>
                        <?php echo Form::close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
