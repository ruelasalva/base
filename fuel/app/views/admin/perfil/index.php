<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Mi perfil</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/perfil', 'Mi perfil'); ?>
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
						<h3 class="mb-0">Editar perfil</h3>
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
											<?php echo Form::input('username', (isset($username) ? $username : ''), array('id' => 'username', 'class' => 'form-control', 'placeholder' => 'Nombre de usuario', 'readonly' => 'readonly')); ?>
											<small id="username-help" class="form-text text-muted">El nombre de usuario no puede ser editado.</small>
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
											<small id="password-help" class="form-text text-muted">Sólo llenar en caso de querer cambiar la contraseña.</small>
										</div>
									</div>
								</div>
							</fieldset>
							<fieldset>
								<div class="form-row">
									<div class="col-md-12 mt-4 mb-3">
										<legend class="mb-0 heading">Información del perfil</legend>
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['full_name']['form-group']; ?>">
											<?php echo Form::label('Nombre completo', 'full_name', array('class' => 'form-control-label', 'for' => 'full_name')); ?>
											<?php echo Form::input('full_name', (isset($full_name) ? $full_name : ''), array('id' => 'full_name', 'class' => 'form-control '.$classes['full_name']['form-control'], 'placeholder' => 'Nombre completo')); ?>
											<?php if(isset($errors['full_name'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['full_name']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
								</div>
							</fieldset>
							<fieldset>
								<div class="form-row">
									<div class="col-md-12 mt-4 mb-3">
										<legend class="mb-0 heading">Consentimientos Legales</legend>
									</div>
									<div class="col-md-6 mb-3">
										<?php echo Helper_Legal::render_documents_for_user(Auth::check() ? Auth::get('id') : null); ?>
									</div>
								</div>
							</fieldset>
							<fieldset>
								<div class="form-row">
									<div class="col-md-12 mt-4 mb-3">
										<legend class="mb-0 heading">Cookies</legend>
									</div>
									<div class="col-md-6 mb-3">
										<?php echo Helper_Legal::render_cookies_preferences_profile(Auth::check() ? Auth::get('id') : null); ?>
										<small class="form-text text-muted">Puedes cambiar tus preferencias de cookies en cualquier momento.</small>
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
