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
								<?php echo Html::anchor('admin/usuarios/editar_direccion/'.$user_id.'/'.$id, 'Editar dirección'); ?>
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
						<?php echo Form::open(array('method' => 'post')); ?>
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
										<legend class="mb-0 heading">Información de la dirección</legend>
									</div>
                                    <div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['name']['form-group']; ?>">
											<?php echo Form::label('Nombre', 'name', array('class' => 'form-control-label', 'for' => 'name')); ?>
											<?php echo Form::input('name', (isset($name) ? $name : ''), array('id' => 'name', 'class' => 'form-control '.$classes['name']['form-control'], 'placeholder' => 'Nombres')); ?>
											<?php if(isset($errors['name'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['name']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
                                    <div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['last_name']['form-group']; ?>">
											<?php echo Form::label('Apellidos', 'last_name', array('class' => 'form-control-label', 'for' => 'last_name')); ?>
											<?php echo Form::input('last_name', (isset($last_name) ? $last_name : ''), array('id' => 'last_name', 'class' => 'form-control '.$classes['last_name']['form-control'], 'placeholder' => 'Apellidos')); ?>
											<?php if(isset($errors['last_name'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['last_name']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
                                    <div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['phone']['form-group']; ?>">
											<?php echo Form::label('Teléfono', 'phone', array('class' => 'form-control-label', 'for' => 'phone')); ?>
											<?php echo Form::input('phone', (isset($phone) ? $phone : ''), array('id' => 'phone', 'class' => 'form-control '.$classes['phone']['form-control'], 'placeholder' => 'Teléfono')); ?>
											<?php if(isset($errors['phone'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['phone']; ?>
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
    										<?php echo Form::select('state', (isset($state) ? $state : 'none'), $state_opts, array('id' => 'state', 'class' => 'form-control '.$classes['state']['form-control'], 'data-toggle' => 'select')); ?>
    										<?php if(isset($errors['state'])) : ?>
    											<div class="invalid-feedback">
    												<?php echo $errors['state']; ?>
    											</div>
    										<?php endif; ?>
    									</div>
    								</div>
                                    <div class="col-6 mb-3">
    									<div class="form-group <?php echo $classes['details']['form-group']; ?>">
    										<?php echo Form::label('Información adicional', 'details', array('class' => 'form-control-label', 'for' => 'details')); ?>
    										<?php echo Form::textarea('details', (isset($details) ? $details : ''), array('id' => 'Description', 'class' => 'form-control '.$classes['details']['form-control'], 'placeholder' => 'Información adicional', 'rows' => 7)); ?>
    										<?php if(isset($errors['details'])) : ?>
    											<div class="invalid-feedback">
    												<?php echo $errors['details']; ?>
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
