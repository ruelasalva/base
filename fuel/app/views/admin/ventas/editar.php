<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Ventas</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/ventas', 'Ventas'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/ventas/info/'.$id, '#'.$id); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/ventas/editar/'.$id, 'Editar'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/ventas/info/'.$id, 'Ver', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php if($bill_id == 0): ?>
						<?php echo Html::anchor('admin/ventas/agregar_factura/'.$id, 'Agregar factura', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php else: ?>
						<?php echo Html::anchor(Uri::base(false).'assets/descargas/'.$pdf, 'Archivo PDF', array('class' => 'btn btn-sm btn-neutral', 'target' => '_blank')); ?>
						<?php echo Html::anchor(Uri::base(false).'assets/descargas/'.$xml, 'Archivo XML', array('class' => 'btn btn-sm btn-neutral', 'target' => '_blank')); ?>
						<?php echo Html::anchor('admin/ventas/editar_factura/'.$bill_id, 'Modificar Archivos', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php endif; ?>
					<?php if ($type_id == 2): ?>	
						<?php echo Html::anchor('admin/ventas/editar_voucher/'.$id, 'Modificar Comprobante', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php endif; ?>
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
						<h3 class="mb-0">Editar</h3>
					</div>
					<!-- CARD BODY -->
					<div class="card-body">
					<?php echo Form::open(array('method' => 'post')); ?>
						<fieldset>
							<div class="form-row">
								<div class="col-md-12 mt-0 mb-3">
									<legend class="mb-0 heading">Información de la venta</legend>
								</div>
								<div class="col-md-6 mb-3">
										<div class="form-group">
											<?php echo Form::label('Cliente', 'customer', array('class' => 'form-control-label', 'for' => 'customer')); ?>
											<?php echo Form::input('customer', (isset($customer) ? $customer : ''), array('id' => 'customer', 'class' => 'form-control', 'placeholder' => 'Cliente', 'readonly' => 'readonly')); ?>
                                            <small id="customer-help" class="form-text text-muted">El nombre de Cliente no puede ser editado.</small>
										</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Código SAP', 'codigosap', array('class' => 'form-control-label', 'for' => 'codigosap')); ?>
										<?php echo Form::input('codigosap', (isset($codigosap) ? $codigosap : ''), array('id' => 'codigosap', 'class' => 'form-control '.$classes['codigosap']['form-control'], 'placeholder' => 'Código SAP')); ?>
										<?php if(isset($errors['codigosap'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['codigosap']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['email']['form-group']; ?>">
										<?php echo Form::label('Correo electronico', 'email', array('class' => 'form-control-label', 'for' => 'email')); ?>
										<?php echo Form::input('email', (isset($email) ? $email : ''), array('id' => 'email', 'class' => 'form-control '.$classes['email']['form-control'], 'placeholder' => 'Email')); ?>
										<?php if(isset($errors['email'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['email']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Total', 'total', array('class' => 'form-control-label', 'for' => 'total')); ?>
										<?php echo Form::input('total', (isset($total) ? $total : ''), array('id' => 'total', 'class' => 'form-control', 'placeholder' => 'Cliente', 'readonly' => 'readonly')); ?>
                                        <small id="total-help" class="form-text text-muted">El Total no puede ser editado.</small>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Tipo de pago', 'type', array('class' => 'form-control-label', 'for' => 'type')); ?>
										<span class="form-control"><?php echo $type; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
											<?php echo Form::label('Estatus de pago', 'status', array('class' => 'form-control-label', 'for' => 'status')); ?>
												<?php echo Form::select('status',(isset($status)? $status : 1),array(
												'1' => 'Pagado',
												'2' => 'Por revisar',
												'3' => 'Cancelado'
											), array('class' => 'form-control ', 'data-toggle' => 'select')) ?>
												<?php if(isset($errors['status'])) : ?>
													<div class="invalid-feedback">
														<?php echo $errors['status']; ?>
											<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['order']['form-group']; ?>">
											<?php echo Form::label('Estatus pedido', 'order', array('class' => 'form-control-label', 'for' => 'order')); ?>
											<?php echo Form::select('order', (isset($order) ? $order : 'none'), $order_opts, array('id' => 'order', 'class' => 'form-control '.$classes['order']['form-control'], 'data-toggle' => 'select')); ?>
											<?php if(isset($errors['order'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['order']; ?>
												</div>
											<?php endif; ?>
										</div>
								</div>
								<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['ordersap']['form-group']; ?>">
											<?php echo Form::label('Numero de pedio de SAP', 'ordersap', array('class' => 'form-control-label', 'for' => 'ordersap')); ?>
											<?php echo Form::input('ordersap', (isset($ordersap) ? $ordersap : ''), array('id' => 'ordersap', 'class' => 'form-control '.$classes['ordersap']['form-control'], 'placeholder' => 'Numero de pedido SAP')); ?>
											<?php if(isset($errors['ordersap'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['ordersap']; ?>
												</div>
											<?php endif; ?>
										</div>
								</div>
								<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['factsap']['form-group']; ?>">
											<?php echo Form::label('Numero de factura de SAP', 'factsap', array('class' => 'form-control-label', 'for' => 'factsap')); ?>
											<?php echo Form::input('factsap', (isset($factsap) ? $factsap : ''), array('id' => 'factsap', 'class' => 'form-control '.$classes['factsap']['form-control'], 'placeholder' => 'Numero de factura SAP')); ?>
											<?php if(isset($errors['factsap'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['factsap']; ?>
												</div>
											<?php endif; ?>
										</div>
								</div>
								<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['package']['form-group']; ?>">
											<?php echo Form::label('Paqueteria', 'package', array('class' => 'form-control-label', 'for' => 'package')); ?>
											<?php echo Form::select('package', (isset($package) ? $package : 'none'), $package_opts, array('id' => 'package', 'class' => 'form-control '.$classes['package']['form-control'], 'data-toggle' => 'select')); ?>
											<?php if(isset($errors['package'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['package']; ?>
												</div>
											<?php endif; ?>
										</div>
								</div>
								<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['guide']['form-group']; ?>">
											<?php echo Form::label('Numero de guia de paqueteria', 'guide', array('class' => 'form-control-label', 'for' => 'guide')); ?>
											<?php echo Form::input('guide', (isset($guide) ? $guide : ''), array('id' => 'guide', 'class' => 'form-control '.$classes['guide']['form-control'], 'placeholder' => 'Numero de guia de paqueteria')); ?>
											<?php if(isset($errors['guide'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['guide']; ?>
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
									<div class="form-group">
										<?php echo Form::label('Nombre', 'name', array('class' => 'form-control-label', 'for' => 'name')); ?>
										<span class="form-control"><?php echo $name; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Apellidos', 'last_name', array('class' => 'form-control-label', 'for' => 'last_name')); ?>
										<span class="form-control"><?php echo $last_name; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Teléfono', 'phone', array('class' => 'form-control-label', 'for' => 'phone')); ?>
										<span class="form-control"><?php echo $phone; ?></span>
									</div>
								</div>
							</div>
						</fieldset>
						<?php if($address_flag): ?>
						<fieldset>
							<div class="form-row">
								<div class="col-md-12 mt-0 mb-3">
									<legend class="mb-0 heading">Información de la dirección de envío</legend>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Nombre', 'address_name', array('class' => 'form-control-label', 'for' => 'address_name')); ?>
										<span class="form-control"><?php echo $address_name; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Apellidos', 'address_last_name', array('class' => 'form-control-label', 'for' => 'address_last_name')); ?>
										<span class="form-control"><?php echo $address_last_name; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Teléfono', 'address_phone', array('class' => 'form-control-label', 'for' => 'address_phone')); ?>
										<span class="form-control"><?php echo $address_phone; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Calle', 'street', array('class' => 'form-control-label', 'for' => 'street')); ?>
										<span class="form-control"><?php echo $street; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Número', 'number', array('class' => 'form-control-label', 'for' => 'number')); ?>
										<span class="form-control"><?php echo $number; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Número interno', 'internal_number', array('class' => 'form-control-label', 'for' => 'internal_number')); ?>
										<span class="form-control"><?php echo $internal_number; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Colonia', 'colony', array('class' => 'form-control-label', 'for' => 'colony')); ?>
										<span class="form-control"><?php echo $colony; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Código postal', 'zipcode', array('class' => 'form-control-label', 'for' => 'zipcode')); ?>
										<span class="form-control"><?php echo $zipcode; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Ciudad', 'city', array('class' => 'form-control-label', 'for' => 'city')); ?>
										<span class="form-control"><?php echo $city; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Estado', 'state', array('class' => 'form-control-label', 'for' => 'state')); ?>
										<span class="form-control"><?php echo $state; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <?php echo Form::label('Detalles', 'details', array('class' => 'form-control-label', 'for' => 'details')); ?>
                                        <span class="form-control from-table form-table-area"><?php echo $details; ?></span>
                                    </div>
                                </div>
							</div>
						</fieldset>
						<?php endif; ?>
					<?php echo Form::submit(array('value'=> 'Guardar', 'name'=>'submit', 'class' => 'btn btn-primary')); ?>
					<?php echo Form::close(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
