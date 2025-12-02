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
								<?php echo Html::anchor('admin/catalogo/generales/monedas', 'Monedas'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/catalogo/generales/monedas/info/'.$id, $name); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/catalogo/generales/monedas/editar/'.$id, 'Editar'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/catalogo/generales/monedas/info/'.$id, 'Ver', array('class' => 'btn btn-sm btn-neutral')); ?>
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
				<!-- CUSTOM FORM VALIDATION -->
				<div class="card">
					<!-- CARD HEADER -->
					<div class="card-header">
						<h3 class="mb-0">Editar moneda</h3>
					</div>
					<!-- CARD BODY -->
					<div class="card-body">
						<?php echo Form::open(array('method' => 'post')); ?>
							<fieldset>
								<div class="form-row">
									<div class="col-md-12 mt-0 mb-3">
										<legend class="mb-0 heading">Información de la moneda</legend>
									</div>
									<!-- NOMBRE -->
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['name']['form-group']; ?>">
											<?php echo Form::label('Nombre', 'name', array('class' => 'form-control-label', 'for' => 'name')); ?>
											<?php echo Form::input('name', (isset($name) ? $name : ''), array(
												'id' => 'name',
												'class' => 'form-control '.$classes['name']['form-control'],
												'placeholder' => 'Nombre'
											)); ?>
											<?php if(isset($errors['name'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['name']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<!-- CÓDIGO -->
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['code']['form-group']; ?>">
											<?php echo Form::label('Código', 'code', array('class' => 'form-control-label', 'for' => 'code')); ?>
											<?php echo Form::input('code', (isset($code) ? $code : ''), array(
												'id' => 'code',
												'class' => 'form-control '.$classes['code']['form-control'],
												'placeholder' => 'Ej. MXN, USD, EUR'
											)); ?>
											<?php if(isset($errors['code'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['code']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<!-- SÍMBOLO -->
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['symbol']['form-group']; ?>">
											<?php echo Form::label('Símbolo', 'symbol', array('class' => 'form-control-label', 'for' => 'symbol')); ?>
											<?php echo Form::input('symbol', (isset($symbol) ? $symbol : ''), array(
												'id' => 'symbol',
												'class' => 'form-control '.$classes['symbol']['form-control'],
												'placeholder' => 'Ej. $, €, ¥'
											)); ?>
											<?php if(isset($errors['symbol'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['symbol']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<!-- TIPO DE CAMBIO -->
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['type_exchange']['form-group']; ?>">
											<?php echo Form::label('Tipo de cambio', 'type_exchange', array('class' => 'form-control-label', 'for' => 'type_exchange')); ?>
											<?php echo Form::input('type_exchange', (isset($type_exchange) ? $type_exchange : ''), array(
												'id' => 'type_exchange',
												'class' => 'form-control '.$classes['type_exchange']['form-control'],
												'placeholder' => 'Ej. 1.0000',
												'type' => 'number',
												'min' => '0',
												'step' => '0.0001'
											)); ?>
											<?php if(isset($errors['type_exchange'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['type_exchange']; ?>
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
