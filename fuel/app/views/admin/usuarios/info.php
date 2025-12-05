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
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/usuarios/info/'.$id, $username); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/usuarios/manage_tenants/'.$id, '<i class="fas fa-building"></i> Gestionar Backends', array('class' => 'btn btn-sm btn-info')); ?>
					<?php echo Html::anchor('admin/usuarios/editar/'.$id, 'Editar', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php echo Html::anchor('admin/usuarios/agregar_facturacion/'.$id, 'Agregar datos de facturación', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php echo Html::anchor('admin/usuarios/admin_recuperar_contrasena/'.$id, 'Recuperar Contraseña', array('class' => 'btn btn-sm btn-neutral')); ?>
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
						<h3 class="mb-0">Ver información</h3>
					</div>
					<!-- CARD BODY -->
					<div class="card-body">
						<fieldset>
							<div class="form-row">
								<div class="col-md-12 mt-0 mb-3">
									<legend class="mb-0 heading">Información del usuario</legend>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Usuario', 'username', array('class' => 'form-control-label', 'for' => 'username')); ?>
										<span class="form-control"><?php echo $username; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Email', 'email', array('class' => 'form-control-label', 'for' => 'email')); ?>
										<span class="form-control"><?php echo $email; ?></span>
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
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Tipo de cliente', 'type', array('class' => 'form-control-label', 'for' => 'type')); ?>
										<span class="form-control"><?php echo $type; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Codigo Cliente SAP', 'sap_code', array('class' => 'form-control-label', 'for' => 'sap_code')); ?>
										<span class="form-control"><?php echo $sap_code; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Factura en todas las compras', 'require_bill', array('class' => 'form-control-label', 'for' => 'require_bill')); ?>
										<span class="form-control"><?php echo $require_bill; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Bloqueado', 'banned', array('class' => 'form-control-label', 'for' => 'banned')); ?>
										<span class="form-control"><?php echo $banned; ?></span>
									</div>
								</div>
							</div>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php if(!empty($addresses)): ?>
		<!-- TABLE -->
		<div class="row">
			<div class="col">
				<div class="card">
					<!-- CARD HEADER -->
					<div class="card-header border-0">
						<div class="form-row">
							<div class="col-md-9">
								<h3 class="mb-0">Lista de direcciones</h3>
							</div>
						</div>
					</div>
					<!-- LIGHT TABLE -->
					<div class="table-responsive" data-toggle="lists" data-list-values='["street", "colony", "zipcode", "city", "state"]'>
						<table class="table align-items-center table-flush">
							<thead class="thead-light">
								<tr>
									<th scope="col" class="sort" data-sort="street">Calle</th>
									<th scope="col" class="sort" data-sort="colony">Colonia</th>
									<th scope="col" class="sort" data-sort="zipcode">Código Postal</th>
									<th scope="col" class="sort" data-sort="city">Ciudad</th>
									<th scope="col" class="sort" data-sort="state">Estado</th>
									<th scope="col"></th>
								</tr>
							</thead>
							<tbody class="list">
								<?php foreach($addresses as $address): ?>
									<tr>
										<th class="street">
											<?php echo $address['street']; ?>
										</th>
										<td class="colony">
											<?php echo $address['colony']; ?>
										</td>
										<td class="zipcode">
											<?php echo $address['zipcode']; ?>
										</td>
										<td class="city">
											<?php echo $address['city']; ?>
										</td>
										<td class="state">
											<?php echo $address['state']; ?>
										</td>
										<td class="text-right">
											<div class="dropdown">
												<a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													<i class="fas fa-ellipsis-v"></i>
												</a>
												<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
													<?php echo Html::anchor('admin/usuarios/editar_direccion/'.$id.'/'.$address['id'], 'Editar', array('class' => 'dropdown-item')); ?>
												</div>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php if(!empty($tax_data)): ?>
		<!-- TABLE -->
		<div class="row">
			<div class="col">
				<div class="card">
					<!-- CARD HEADER -->
					<div class="card-header border-0">
						<div class="form-row">
							<div class="col-md-9">
								<h3 class="mb-0">Datos de facturación</h3>
							</div>
						</div>
					</div>
					<!-- LIGHT TABLE -->
					<div class="table-responsive" data-toggle="lists" data-list-values='["rfc", "business_name", "address", "default"]'>
						<table class="table align-items-center table-flush">
							<thead class="thead-light">
								<tr>
									<th scope="col" class="sort" data-sort="rfc">RFC</th>
									<th scope="col" class="sort" data-sort="business_name">Razón social</th>
									<th scope="col" class="sort" data-sort="address">Dirección</th>
									<th scope="col" class="sort" data-sort="default">Default</th>
									<th scope="col"></th>
								</tr>
							</thead>
							<tbody class="list">
								<?php foreach($tax_data as $tax_datum): ?>
									<tr>
										<th class="rfc">
											<?php echo $tax_datum['rfc']; ?>
										</th>
										<td class="business_name">
											<?php echo $tax_datum['business_name']; ?>
										</td>
										<td class="address">
											<?php echo $tax_datum['address']; ?>
										</td>
										<td class="default">
											<?php echo $tax_datum['default']; ?>
										</td>
										<td class="text-right">
											<div class="dropdown">
												<a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													<i class="fas fa-ellipsis-v"></i>
												</a>
												<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
													<?php echo Html::anchor('admin/usuarios/editar_facturacion/'.$id.'/'.$tax_datum['id'], 'Editar', array('class' => 'dropdown-item')); ?>
													<div class="dropdown-divider"></div>
													<?php echo Html::anchor('admin/usuarios/eliminar_facturacion/'.$id.'/'.$tax_datum['id'], 'Eliminar', array('class' => 'dropdown-item delete-item')); ?>
												</div>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>
