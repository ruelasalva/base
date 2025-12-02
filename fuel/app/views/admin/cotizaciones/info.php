<!-- HEADER -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-5">
					<h6 class="h2 text-white d-inline-block mb-0">Cotización</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/cotizaciones', 'Cotizaciones'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">#<?php echo $id; ?></li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-7 text-right">
					<?php if ($status == 0): ?>
						<?php echo Html::anchor('admin/cotizaciones/editar/'.$id, 'Editar', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php endif; ?>
					<?php if ($status == 0): ?>
						<?php echo Html::anchor('admin/cotizaciones/enfirme/'.$id, 'Poner en firme', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php endif; ?>
					<?php echo Html::anchor('admin/cotizaciones/imprimir/'.$id, 'Imprimir', array('class' => 'btn btn-sm btn-neutral', 'target' => '_blank')); ?>
					<?php echo Html::anchor('admin/cotizaciones/descargar_pdf/'.$id, 'Descargar PDF', array('class' => 'btn btn-sm btn-neutral', 'target' => '')); ?>
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
				<div class="card">
					<div class="card-header">
						<h3 class="mb-0">Información de la cotización</h3>
					</div>
					<div class="card-body">
						<fieldset>
							<div class="form-row">
								<div class="col-md-6 mb-3">
									<?php echo Form::label('Socio de Negocios', 'partner', ['class' => 'form-control-label']); ?>
									<span class="form-control"><?php echo $partner; ?></span>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo Form::label('Correo electrónico', 'email', ['class' => 'form-control-label']); ?>
									<span class="form-control"><?php echo $email; ?></span>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo Form::label('Vendedor Asignado', 'seller_asig_id', ['class' => 'form-control-label']); ?>
									<span class="form-control"><?php echo $seller_asig_id; ?></span>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo Form::label('Referencia', 'reference', ['class' => 'form-control-label']); ?>
									<span class="form-control"><?php echo $reference; ?></span>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo Form::label('Fecha de validez', 'valid_date', ['class' => 'form-control-label']); ?>
									<span class="form-control"><?php echo $valid_date; ?></span>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo Form::label('Estatus de la Cotizacion', 'statusd', ['class' => 'form-control-label']); ?>
									<span class="form-control"><?php echo $statusd; ?></span>
								</div>
								<div class="col-md-12 mb-3">
									<?php echo Form::label('Comentarios', 'comments', ['class' => 'form-control-label']); ?>
									<span class="form-control from-table from-table-area"><?php echo $comments; ?></span>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo Form::label('Forma de pago', 'payment_type', ['class' => 'form-control-label']); ?>
									<span class="form-control"><?php echo $payment_type; ?></span>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo Form::label('Descuento', 'discount', ['class' => 'form-control-label']); ?>
									<span class="form-control text-green font-weight-bold"><?php echo $discount; ?></span>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo Form::label('Sub-Total', 'subtotal', ['class' => 'form-control-label']); ?>
									<span class="form-control text-green font-weight-bold"><?php echo $subtotal; ?></span>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo Form::label('IVA', 'iva', ['class' => 'form-control-label']); ?>
									<span class="form-control text-red font-weight-bold"><?php echo $iva; ?></span>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo Form::label('Total', 'total', ['class' => 'form-control-label']); ?>
									<span class="form-control text-blue font-weight-bold"><?php echo $total; ?></span>
								</div>
							</div>
						</fieldset>
						<?php if($address_flag): ?>
						<fieldset class="mb-4">
							<legend class="heading">Dirección de entrega</legend>
							<div class="form-row">
								<div class="col-md-6 mb-3">
									<?php echo Form::label('Calle', 'street', ['class' => 'form-control-label']); ?>
									<span class="form-control"><?php echo $street; ?></span>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo Form::label('Número', 'number', ['class' => 'form-control-label']); ?>
									<span class="form-control"><?php echo $number; ?></span>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo Form::label('Número interior', 'internal_number', ['class' => 'form-control-label']); ?>
									<span class="form-control"><?php echo $internal_number; ?></span>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo Form::label('Colonia', 'colony', ['class' => 'form-control-label']); ?>
									<span class="form-control"><?php echo $colony; ?></span>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo Form::label('Código Postal', 'zipcode', ['class' => 'form-control-label']); ?>
									<span class="form-control"><?php echo $zipcode; ?></span>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo Form::label('Ciudad', 'city', ['class' => 'form-control-label']); ?>
									<span class="form-control"><?php echo $city; ?></span>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo Form::label('Estado', 'state', ['class' => 'form-control-label']); ?>
									<span class="form-control"><?php echo $state; ?></span>
								</div>
								<div class="col-md-12 mb-3">
									<?php echo Form::label('Detalles', 'details', ['class' => 'form-control-label']); ?>
									<span class="form-control from-table from-table-area"><?php echo $details; ?></span>
								</div>
							</div>
						</fieldset>
						<?php endif; ?>

						<?php if($bill_flag): ?>
						<fieldset class="mb-4">
							<legend class="heading">Datos fiscales</legend>
							<div class="form-row">
								<div class="col-md-6 mb-3">
									<?php echo Form::label('Razón social', 'business_name', ['class' => 'form-control-label']); ?>
									<span class="form-control"><?php echo $business_name; ?></span>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo Form::label('RFC', 'rfc', ['class' => 'form-control-label']); ?>
									<span class="form-control"><?php echo $rfc; ?></span>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo Form::label('Calle', 'tax_data_street', ['class' => 'form-control-label']); ?>
									<span class="form-control"><?php echo $tax_data_street; ?></span>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo Form::label('# Exterior', 'tax_data_number', ['class' => 'form-control-label']); ?>
									<span class="form-control"><?php echo $tax_data_number; ?></span>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo Form::label('# Interior', 'tax_data_internal_number', ['class' => 'form-control-label']); ?>
									<span class="form-control"><?php echo $tax_data_internal_number; ?></span>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo Form::label('Colonia', 'tax_data_colony', ['class' => 'form-control-label']); ?>
									<span class="form-control"><?php echo $tax_data_colony; ?></span>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo Form::label('Código Postal', 'tax_data_zipcode', ['class' => 'form-control-label']); ?>
									<span class="form-control"><?php echo $tax_data_zipcode; ?></span>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo Form::label('Ciudad', 'tax_data_city', ['class' => 'form-control-label']); ?>
									<span class="form-control"><?php echo $tax_data_city; ?></span>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo Form::label('Estado', 'tax_data_state', ['class' => 'form-control-label']); ?>
									<span class="form-control"><?php echo $tax_data_state; ?></span>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo Form::label('Forma de pago', 'payment_method', ['class' => 'form-control-label']); ?>
									<span class="form-control"><?php echo $payment_method; ?></span>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo Form::label('Uso de CFDI', 'cfdi', ['class' => 'form-control-label']); ?>
									<span class="form-control"><?php echo $cfdi; ?></span>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo Form::label('Régimen fiscal', 'sat_tax_regime', ['class' => 'form-control-label']); ?>
									<span class="form-control"><?php echo $sat_tax_regime; ?></span>
								</div>
							</div>
						</fieldset>
						<?php endif; ?>

						<?php if(!empty($products) && is_array($products)): ?>
						<fieldset class="mt-4">
							<legend class="heading">Productos cotizados</legend>
							<div class="table-responsive">
								<table class="table align-items-center table-flush">
									<thead class="thead-light">
										<tr>
											<th>Imagen</th>
											<th>Código</th>
											<th>Producto</th>
											<th>Cantidad</th>
											<th>Precio unitario</th>
											<th>Descuento %</th>
											<th>Sub-Total</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach($products as $product): ?>
										<tr>
											<th class="image">
												<?php
													if (file_exists(DOCROOT.'assets/uploads/'.$product['image']))
													{
														echo Asset::img($product['image'], array('class' => 'avatar')) ;
													}else{
														echo Asset::img('thumb_no_image.png', array('class' => 'avatar'));
													}
												?>
											</th>
											<td><?php echo $product['code']; ?></td>
											<td title="<?php echo $product['name_complete']; ?>"><?php echo $product['name']; ?></td>
											<td><?php echo $product['quantity']; ?></td>
											<td><?php echo $product['price']; ?></td>
											<td><?php echo $product['discount']; ?></td>
											<td><?php echo $product['total']; ?></td>
										</tr>
									<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						</fieldset>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>