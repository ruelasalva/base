<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Empleados</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/empleados', 'Empleados'); ?>
							</li>
                            <
						</ol>
					</nav>
				</div>
                <div class="col-lg-6 col-5 text-right">
					
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
										<legend class="mb-0 heading">Información del empleado</legend>
									</div>
                                    <div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['name']['form-group']; ?>">
											<?php echo Form::label('Nombre', 'name', array('class' => 'form-control-label', 'for' => 'name')); ?>
											<?php echo Form::input('name', (isset($name) ? $name : ''), array('id' => 'name', 'class' => 'form-control '.$classes['name']['form-control'], 'placeholder' => 'Nombre')); ?>
											<?php if(isset($errors['name'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['name']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
                                    <div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['last_name']['form-group']; ?>">
											<?php echo Form::label('Apellido', 'last_name', array('class' => 'form-control-label', 'for' => 'last_name')); ?>
											<?php echo Form::input('last_name', (isset($last_name) ? $last_name : ''), array('id' => 'last_name', 'class' => 'form-control '.$classes['last_name']['form-control'], 'placeholder' => 'Apellido')); ?>
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
    									<div class="form-group <?php echo $classes['department_id']['form-group']; ?>">
    										<?php echo Form::label('Tipo de empleado', 'department_id', array('class' => 'form-control-label', 'for' => 'department_id')); ?>
    										<?php echo Form::select('department_id', (isset($department_id) ? $department_id : 'none'), $department_opts, array('id' => 'department_id', 'class' => 'form-control '.$classes['department_id']['form-control'], 'data-toggle' => 'select')); ?>
    										<?php if(isset($errors['department_id'])) : ?>
    											<div class="invalid-feedback">
    												<?php echo $errors['department_id']; ?>
    											</div>
    										<?php endif; ?>
    									</div>
    								</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['email']['form-group']; ?>">
											<?php echo Form::label('Email', 'email', array('class' => 'form-control-label', 'for' => 'email')); ?>
											<?php echo Form::input('email', (isset($email) ? $email : ''), array('id' => 'email', 'class' => 'form-control '.$classes['email']['form-control'], 'placeholder' => 'Email')); ?>
											<?php if(isset($errors['email'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['email']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['codigo']['form-group']; ?>">
											<?php echo Form::label('Codigo de Empleado', 'codigo', array('class' => 'form-control-label', 'for' => 'codigo')); ?>
											<?php echo Form::input('codigo', (isset($codigo) ? $codigo : ''), array('id' => 'codigo', 'class' => 'form-control '.$classes['codigo']['form-control'], 'placeholder' => 'Codigo de Empleado')); ?>
											<?php if(isset($errors['codigo'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['codigo']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['code_seller']['form-group']; ?>">
											<?php echo Form::label('Codigo de Vendedor', 'code_seller', array('class' => 'form-control-label', 'for' => 'code_seller')); ?>
											<?php echo Form::input('code_seller', (isset($code_seller) ? $code_seller : ''), array('id' => 'code_seller', 'class' => 'form-control '.$classes['code_seller']['form-control'], 'placeholder' => 'Codigo de Empleado')); ?>
											<?php if(isset($errors['code_seller'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['code_seller']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
								
										


								</div>
							</fieldset>
							
							<fieldset>
								<div class="form-row">
									<div class="col-md-12 mt-0 mb-3">
										<legend class="mb-0 heading">Información del Empleado</legend>
									</div>
									
									<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['user_id']['form-group']; ?>">
										<?php echo Form::label('Usuario', 'user_id', array('class' => 'form-control-label', 'for' => 'user_id')); ?>
										<?php echo Form::select('user_id', Input::post('user_id', isset($user_id) ? $user_id : 'none'), $user_opts, array('id' => 'user_id', 'class' => 'form-control '.$classes['user_id']['form-control'], 'data-toggle' => 'select')); ?>
										<?php if(isset($errors['user_id'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['user_id']; ?>
											</div>
										<?php endif; ?>
									</div>
									</div>

                                    <div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['email_user']['form-group']; ?>">
											<?php echo Form::label('Email', 'email_user', array('class' => 'form-control-label', 'for' => 'email_user')); ?>
											<?php echo Form::input('email_user', (isset($email_user) ? $email_user : ''), array('id' => 'email_user', 'class' => 'form-control '.$classes['email_user']['form-control'], 'placeholder' => 'Email Usuario')); ?>
											<?php if(isset($errors['email_user'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['email_user']; ?>
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
