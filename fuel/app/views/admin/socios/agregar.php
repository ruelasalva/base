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
                                <?php echo Html::anchor('admin/socios/agregar', 'Agregar'); ?>
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
                                        <legend class="mb-0 heading">Información del Socio de Negocios</legend>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group <?php echo $classes['code_sap']['form-group']; ?>">
                                            <?php echo Form::label('Código del Socio de Negocios', 'code_sap', array('class' => 'form-control-label', 'for' => 'code_sap')); ?>
                                            <?php echo Form::input('code_sap', (isset($code_sap) ? $code_sap : ''), array('id' => 'code_sap', 'class' => 'form-control '.$classes['code_sap']['form-control'], 'placeholder' => 'Código del Socio','style' => 'text-transform: uppercase;')); ?>
                                            <?php if(isset($errors['code_sap'])) : ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $errors['code_sap']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group <?php echo $classes['name']['form-group']; ?>">
                                            <?php echo Form::label('Razon Social del Socio de Negocios', 'name', array('class' => 'form-control-label', 'for' => 'name')); ?>
                                            <?php echo Form::input('name', (isset($name) ? $name : ''), array('id' => 'name', 'class' => 'form-control '.$classes['name']['form-control'], 'placeholder' => 'Razon Social','style' => 'text-transform: uppercase;')); ?>
                                            <?php if(isset($errors['name'])) : ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $errors['name']; ?>
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
                                            <?php echo Form::password('password', '', array('id' => 'password', 'class' => 'form-control '.$classes['password']['form-control'], 'placeholder' => 'Contraseña')); ?>
                                            <?php if(isset($errors['password'])) : ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $errors['password']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group <?php echo $classes['rfc']['form-group']; ?>">
                                            <?php echo Form::label('RFC', 'rfc', array('class' => 'form-control-label', 'for' => 'rfc')); ?>
                                            <?php echo Form::input('rfc', (isset($rfc) ? $rfc : ''), array('id' => 'rfc', 'class' => 'form-control '.$classes['rfc']['form-control'], 'placeholder' => 'RFC','style' => 'text-transform: uppercase;')); ?>
                                            <?php if(isset($errors['rfc'])) : ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $errors['rfc']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <div class="form-row">
                                    <div class="col-md-12 mt-4 mb-3">
                                        <legend class="mb-0 heading">Datos de Ventas</legend>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group <?php echo $classes['customer_id']['form-group']; ?>">
                                            <?php echo Form::label('Usuario Web (Opcional)', 'customer_id', array('class' => 'form-control-label', 'for' => 'customer_id')); ?>
                                            <?php echo Form::select('customer_id', Input::post('customer_id', isset($customer_id) ? $customer_id : 'none'), $customer_opts, array('id' => 'customer_id', 'class' => 'form-control '.$classes['customer_id']['form-control'], 'data-toggle' => 'select')); ?>
                                            <?php if(isset($errors['customer_id'])) : ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $errors['customer_id']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
									</div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group <?php echo $classes['employee_id']['form-group']; ?>">
                                            <?php echo Form::label('Vendedor (Opcional)', 'employee_id', array('class' => 'form-control-label', 'for' => 'employee_id')); ?>
                                            <?php echo Form::select('employee_id', Input::post('employee_id', isset($employee_id) ? $employee_id : 'none'), $employee_opts, array('id' => 'employee_id', 'class' => 'form-control '.$classes['employee_id']['form-control'], 'data-toggle' => 'select')); ?>
                                            <?php if(isset($errors['employee_id'])) : ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $errors['employee_id']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
									</div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group <?php echo $classes['type_id']['form-group']; ?>">
                                            <?php echo Form::label('Lista de precios', 'type_id', array('class' => 'form-control-label', 'for' => 'type_id')); ?>
                                            <?php echo Form::select('type_id', Input::post('type_id', isset($type_id) ? $type_id : 'none'), $type_opts, array('id' => 'type_id', 'class' => 'form-control '.$classes['type_id']['form-control'], 'data-toggle' => 'select')); ?>
                                            <?php if(isset($errors['type_id'])) : ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $errors['type_id']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
									</div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <div class="form-row">
                                    <div class="col-md-12 mt-4 mb-3">
                                        <legend class="mb-0 heading">Adicional</legend>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group <?php echo $classes['banned']['form-group']; ?>">
                                            <?php echo Form::label('Bloqueado Si/No', 'banned', array('class' => 'form-control-label', 'for' => 'banned')); ?>
                                            <?php echo Form::select('banned', Input::post('banned', isset($banned) ? $banned : ''), array(0 => 'No', 1 => 'Sí'), array('id' => 'banned', 'class' => 'form-control '.$classes['banned']['form-control'])); ?>
                                            <?php if(isset($errors['banned'])) : ?>
                                               

												<div class="invalid-feedback">
													<?php echo $errors['banned']; ?>
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
