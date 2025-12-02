<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Usuarios</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/usuarios', 'Usuarios'); ?>
							</li>
                            <li class="breadcrumb-item">
								<?php echo Html::anchor('admin/usuarios/info/'.$user_id, $username); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/usuarios/editar_facturacion/'.$user_id, 'Agregar datos de facturación'); ?>
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
				<!-- CUSTOM FORM VALIDATION -->
				<div class="card">
					<!-- CARD HEADER -->
					<div class="card-header">
						<h3 class="mb-0">Editar usuario</h3>
					</div>
					<!-- CARD BODY -->
					<div class="card-body">
						<?php echo Form::open(array('method' => 'post', 'enctype' => 'multipart/form-data')); ?>
							<fieldset>
								<div class="form-row">
									<div class="col-md-12 mt-0 mb-3">
										<legend class="mb-0 heading">Información del usuario</legend>
									</div>
                                    <div class="col-md-6 mb-3">
										<div class="form-group">
											<?php echo Form::label('Usuario', 'username', array('class' => 'form-control-label', 'for' => 'username')); ?>
											<?php echo Form::input('username', (isset($username) ? $username : ''), array('id' => 'username', 'class' => 'form-control', 'placeholder' => 'Usuario', 'readonly' => 'readonly')); ?>
                                            <small id="username-help" class="form-text text-muted">El nombre de usuario no puede ser editado.</small>
										</div>
									</div>
								</div>
							</fieldset>
                            <fieldset>
								<div class="form-row">
									<div class="col-md-12 mt-0 mb-3">
										<legend class="mb-0 heading">Información de facturación</legend>
									</div>
                                    <div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['business_name']['form-group']; ?>">
											<?php echo Form::label('Razón social', 'business_name', array('class' => 'form-control-label', 'for' => 'business_name')); ?>
											<?php echo Form::input('business_name', (isset($business_name) ? $business_name : ''), array('id' => 'business_name', 'class' => 'form-control '.$classes['business_name']['form-control'], 'placeholder' => 'Razón social')); ?>
											<?php if(isset($errors['business_name'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['business_name']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
                                    <div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['rfc']['form-group']; ?>">
											<?php echo Form::label('RFC', 'rfc', array('class' => 'form-control-label', 'for' => 'rfc')); ?>
											<?php echo Form::input('rfc', (isset($rfc) ? $rfc : ''), array('id' => 'rfc', 'class' => 'form-control '.$classes['rfc']['form-control'], 'placeholder' => 'RFC')); ?>
											<?php if(isset($errors['rfc'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['rfc']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
                                    <div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['street']['form-group']; ?>">
											<?php echo Form::label('Calle', 'street', array('class' => 'form-control-label', 'for' => 'street')); ?>
											<?php echo Form::input('street', (isset($street) ? $street : ''), array('id' => 'street', 'class' => 'form-control '.$classes['street']['form-control'], 'placeholder' => 'Calle')); ?>
											<?php if(isset($errors['street'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['street']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['number']['form-group']; ?>">
											<?php echo Form::label('# Exterior', 'number', array('class' => 'form-control-label', 'for' => 'number')); ?>
											<?php echo Form::input('number', (isset($number) ? $number : ''), array('id' => 'number', 'class' => 'form-control '.$classes['number']['form-control'], 'placeholder' => '# Exterior')); ?>
											<?php if(isset($errors['number'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['number']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['internal_number']['form-group']; ?>">
											<?php echo Form::label('# Interior', 'internal_number', array('class' => 'form-control-label', 'for' => 'internal_number')); ?>
											<?php echo Form::input('internal_number', (isset($internal_number) ? $internal_number : ''), array('id' => 'internal_number', 'class' => 'form-control '.$classes['internal_number']['form-control'], 'placeholder' => '# Interior')); ?>
											<?php if(isset($errors['internal_number'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['internal_number']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['colony']['form-group']; ?>">
											<?php echo Form::label('Colonia', 'colony', array('class' => 'form-control-label', 'for' => 'colony')); ?>
											<?php echo Form::input('colony', (isset($colony) ? $colony : ''), array('id' => 'colony', 'class' => 'form-control '.$classes['colony']['form-control'], 'placeholder' => 'Colonia')); ?>
											<?php if(isset($errors['colony'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['colony']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['zipcode']['form-group']; ?>">
											<?php echo Form::label('Código postal', 'zipcode', array('class' => 'form-control-label', 'for' => 'zipcode')); ?>
											<?php echo Form::input('zipcode', (isset($zipcode) ? $zipcode : ''), array('id' => 'zipcode', 'class' => 'form-control '.$classes['zipcode']['form-control'], 'placeholder' => 'Código postal')); ?>
											<?php if(isset($errors['zipcode'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['zipcode']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['city']['form-group']; ?>">
											<?php echo Form::label('Ciudad', 'city', array('class' => 'form-control-label', 'for' => 'city')); ?>
											<?php echo Form::input('city', (isset($city) ? $city : ''), array('id' => 'city', 'class' => 'form-control '.$classes['city']['form-control'], 'placeholder' => 'Ciudad')); ?>
											<?php if(isset($errors['city'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['city']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['state']['form-group']; ?>">
											<?php echo Form::label('Estado', 'state', array('class' => 'form-control-label', 'for' => 'state')); ?>
											<?php
												echo Form::select('state', (isset($state) ? $state : 'none'), $states_opts, array('id' => 'state', 'class' => 'form-control '.$classes['state']['form-control'], 'data-toggle' => 'select'));
											?>
											<?php if(isset($errors['state'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['state']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
                                    <div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['payment_method']['form-group']; ?>">
											<?php echo Form::label('Forma de pago', 'payment_method', array('class' => 'form-control-label', 'for' => 'payment_method')); ?>
											<?php
												echo Form::select('payment_method', (isset($payment_method) ? $payment_method : 'none'), $payment_methods_opts, array('id' => 'payment_method', 'class' => 'form-control '.$classes['payment_method']['form-control'], 'data-toggle' => 'select'));
											?>
											<?php if(isset($errors['payment_method'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['payment_method']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['cfdi']['form-group']; ?>">
											<?php echo Form::label('Uso del CFDI', 'cfdi', array('class' => 'form-control-label', 'for' => 'cfdi')); ?>
											<?php
												echo Form::select('cfdi', (isset($cfdi) ? $cfdi : 'none'), $cfdis_opts, array('id' => 'cfdi', 'class' => 'form-control '.$classes['cfdi']['form-control'], 'data-toggle' => 'select'));
											?>
											<?php if(isset($errors['cfdi'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['cfdi']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
                                    <div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['sat_tax_regime']['form-group']; ?>">
											<?php echo Form::label('Uso del CFDI', 'sat_tax_regime', array('class' => 'form-control-label', 'for' => 'sat_tax_regime')); ?>
											<?php
												echo Form::select('sat_tax_regime', (isset($sat_tax_regime) ? $sat_tax_regime : 'none'), $sat_tax_regimes_opts, array('id' => 'sat_tax_regime', 'class' => 'form-control '.$classes['sat_tax_regime']['form-control'], 'data-toggle' => 'select'));
											?>
											<?php if(isset($errors['sat_tax_regime'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['sat_tax_regime']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['csf']['form-group']; ?>">
											<?php echo Form::label('Constancia de situación fiscal', 'csf', array('class' => 'form-control-label', 'for' => 'csf')); ?>
											<div class="custom-file">
												<?php echo Form::input('csf', (isset($csf) ? $csf : ''), array('id' => 'fileX', 'type' => 'file', 'class' => 'custom-file-input '.$classes['csf']['form-control'], 'lang' => 'es')); ?>
												<label class="custom-file-label" for="fileX">Archivo en formato PDF</label>
											</div>
											<?php if(isset($errors['csf'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['csf']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['default']['form-group']; ?>">
											<?php echo Form::label('Hacer este registro el RFC predeterminado', 'default', array('class' => 'form-control-label', 'for' => 'default')); ?>
											<?php
												echo Form::select('default', (isset($default) ? $default : 'none'), array(
													0 => 'No',
													1 => 'Sí'
												), array('id' => 'default', 'class' => 'form-control '.$classes['default']['form-control'], 'data-toggle' => 'select'));
											?>
											<?php if(isset($errors['default'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['default']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
								</div>
							</fieldset>
							<?php echo Form::submit(array('value'=> 'Guardar', 'name'=>'submit', 'class' => 'btn btn-primary')); ?>
						<?php echo Form::close(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
