<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Proveedores</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/proveedores', 'Proveedores'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/proveedores/agregar', 'Agregar'); ?>
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
						<h3 class="mb-0">Agregar</h3>
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
										<div class="form-group <?php echo $classes['username']['form-group']; ?>">
											<?php echo Form::label('Usuario', 'username', array('class' => 'form-control-label', 'for' => 'username')); ?>
											<?php echo Form::input('username', (isset($username) ? $username : ''), array('id' => 'username', 'class' => 'form-control '.$classes['username']['form-control'], 'placeholder' => 'Nombre de usuario')); ?>
											<?php if(isset($errors['username'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['username']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['email']['form-group']; ?>">
											<?php echo Form::label('Email', 'email', array('class' => 'form-control-label', 'for' => 'email')); ?>
											<?php echo Form::input('email', (isset($email) ? $email : ''), array('id' => 'email', 'class' => 'form-control '.$classes['email']['form-control'], 'placeholder' => 'usuario@ejemplo.com')); ?>
											<?php if(isset($errors['email'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['email']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['password']['form-group']; ?>">
											<?php echo Form::label('Contraseña', 'password', array('class' => 'form-control-label', 'for' => 'password')); ?>
											<?php echo Form::password('password', (isset($password) ? $password : ''), array('id' => 'password', 'class' => 'form-control '.$classes['password']['form-control'], 'placeholder' => 'Contraseña')); ?>
											<?php if(isset($errors['password'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['password']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
								</div>
							</fieldset>
							<fieldset>
								<div class="form-row">
									<div class="col-md-12 mt-4 mb-3">
										<legend class="mb-0 heading">Información del proveedor</legend>
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['name']['form-group']; ?>">
											<?php echo Form::label('Razon Social', 'name', array('class' => 'form-control-label', 'for' => 'name')); ?>
											<?php echo Form::input('name', (isset($name) ? $name : ''), array('id' => 'name', 'class' => 'form-control '.$classes['name']['form-control'], 'placeholder' => 'Razon Social', 'style' => 'text-transform: uppercase;')); ?>
											<?php if(isset($errors['name'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['name']; ?>
												</div>
											<?php endif; ?>
										</div>
										
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['rfc']['form-group']; ?>">
											<?php echo Form::label('RFC', 'rfc', array('class' => 'form-control-label', 'for' => 'rfc')); ?>
											<?php echo Form::input('rfc', (isset($rfc) ? $rfc : ''), array('id' => 'rfc', 'class' => 'form-control '.$classes['rfc']['form-control'], 'placeholder' => 'RFC', 'style' => 'text-transform: uppercase;')); ?>
											<?php if(isset($errors['rfc'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['rfc']; ?>
												</div>
											<?php endif; ?>
										</div>
										
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['code_sap']['form-group']; ?>">
											<?php echo Form::label('Codigo Sistema', 'code_sap', array('class' => 'form-control-label', 'for' => 'code_sap')); ?>
											<?php echo Form::input('code_sap', (isset($code_sap) ? $code_sap : ''), array('id' => 'code_sap', 'class' => 'form-control '.$classes['code_sap']['form-control'], 'placeholder' => 'Codigo Sistema', 'style' => 'text-transform: uppercase;')); ?>
											<?php if(isset($errors['code_sap'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['code_sap']; ?>
												</div>
											<?php endif; ?>
										</div>
										
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['require_purchase']['form-group']; ?>">
											<?php echo Form::label('Requiere Orden de compra', 'require_purchase', array('class' => 'form-control-label', 'for' => 'require_purchase')); ?>
											<?php echo Form::select('require_purchase',Input::post('require_purchase', isset($require_purchase) ? $require_purchase : '0'),array('0' => 'No','1' => 'Si'),array('id' => 'require_purchase','class' => 'form-control '.$classes['require_purchase']['form-control'],'data-toggle' => 'select')); ?>
											<?php if(isset($errors['require_purchase'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['require_purchase']; ?>
												</div>
											<?php endif; ?>
										</div>

									</div>
								</div>
							</fieldset>
							<?php echo Form::submit(array('value'=> 'Agregar', 'name'=>'submit', 'class' => 'btn btn-primary')); ?>
						<?php echo Form::close(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
