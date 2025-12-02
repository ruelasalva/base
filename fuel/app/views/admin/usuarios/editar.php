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
								<?php echo Html::anchor('admin/usuarios/info/'.$id, $username); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/usuarios/editar/'.$id, 'Editar'); ?>
							</li>
						</ol>
					</nav>
				</div>
                <div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/usuarios/info/'.$id, 'Ver', array('class' => 'btn btn-sm btn-neutral')); ?>
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
								</div>
							</fieldset>
                            <fieldset>
								<div class="form-row">
									<div class="col-md-12 mt-0 mb-3">
										<legend class="mb-0 heading">Información del cliente</legend>
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
    									<div class="form-group <?php echo $classes['type']['form-group']; ?>">
    										<?php echo Form::label('Tipo de cliente', 'type', array('class' => 'form-control-label', 'for' => 'type')); ?>
    										<?php echo Form::select('type', (isset($type) ? $type : 'none'), $type_opts, array('id' => 'type', 'class' => 'form-control '.$classes['type']['form-control'], 'data-toggle' => 'select')); ?>
    										<?php if(isset($errors['type'])) : ?>
    											<div class="invalid-feedback">
    												<?php echo $errors['type']; ?>
    											</div>
    										<?php endif; ?>
    									</div>
    								</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['sap_code']['form-group']; ?>">
											<?php echo Form::label('Codigo Cliente SAP', 'sap_code', array('class' => 'form-control-label', 'for' => 'sap_code')); ?>
											<?php echo Form::input('sap_code', (isset($sap_code) ? $sap_code : ''), array('id' => 'sap_code', 'class' => 'form-control '.$classes['sap_code']['form-control'], 'placeholder' => 'Cliente SAP')); ?>
											<?php if(isset($errors['sap_code'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['sap_code']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<div class="col-md-6 mb-3">
    									<div class="form-group <?php echo $classes['require_bill']['form-group']; ?>">
										<?php echo Form::label('Factura en todas las compras', 'require_bill', array('class' => 'form-control-label', 'for' => 'require_bill')); ?>
											<?php
												echo Form::select('require_bill', (isset($require_bill) ? $require_bill : 0), array(
													'0' => 'No',
													'1' => 'Sí'
												), array('id' => 'require_bill', 'class' => 'form-control '.$classes['require_bill']['form-control'], 'data-toggle' => 'select'));
											?>
											<?php if(isset($errors['require_bill'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['require_bill']; ?>
												</div>
											<?php endif; ?>
    									</div>
    								</div>
									<div class="col-md-6 mb-3">
    									<div class="form-group <?php echo $classes['banned']['form-group']; ?>">
										<?php echo Form::label('Bloqueado', 'banned', array('class' => 'form-control-label', 'for' => 'banned')); ?>
											<?php
												echo Form::select('banned', (isset($banned) ? $banned : 0), array(
													'0' => 'No',
													'1' => 'Sí'
												), array('id' => 'banned', 'class' => 'form-control '.$classes['banned']['form-control'], 'data-toggle' => 'select'));
											?>
											<?php if(isset($errors['banned'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['banned']; ?>
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
