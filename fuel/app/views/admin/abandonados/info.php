<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-5">
					<h6 class="h2 text-white d-inline-block mb-0">Carritos Abandonados</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/abandonados', 'Carritos Abandonados'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/abandonados/info/'.$id, '#'.$id); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-7 text-right">
					<?php echo Html::anchor('admin/abandonados/editar/'.$id, 'Editar', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php //echo Html::anchor('admin/abandonados/asignar_facturacion/'.$id, 'Asignar datos de facturación', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php if($bill_id == 0): ?>
						<?php //echo Html::anchor('admin/abandonados/agregar_factura/'.$id, 'Agregar factura', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php else: ?>
						<?php //echo Html::anchor(Uri::base(false).'assets/descargas/'.$pdf, 'Archivo PDF', array('class' => 'btn btn-sm btn-neutral', 'target' => '_blank')); ?>
						<?php //echo Html::anchor(Uri::base(false).'assets/descargas/'.$xml, 'Archivo XML', array('class' => 'btn btn-sm btn-neutral', 'target' => '_blank')); ?>
						<?php //echo Html::anchor('admin/abandonados/editar_factura/'.$bill_id, 'Modificar Archivos', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php endif; ?>
					<?php if($status == 2): ?>
						<?php //echo Html::anchor('admin/abandonados/agregar_voucher/'.$id,'Subir Comprobante', array('class' => 'btn btn-sm btn-neutral')); ?>
						<?php// echo Html::anchor('admin/abandonados/confirmar_transferencia/'.$id, 'Confirmar transferencia', array('class' => 'btn btn-sm btn-neutral confirm-transfer')); ?>
					<?php endif; ?>
					<?php if($voucher != ''): ?>
						<?php //echo Html::anchor(Uri::base(false).'assets/vouchers/'.$voucher, 'Comprobante', array('class' => 'btn btn-sm btn-neutral', 'target' => '_blank')); ?>
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
						<h3 class="mb-0">Ver información</h3>
					</div>
					<!-- CARD BODY -->
					<div class="card-body">
						<fieldset>
							<div class="form-row">
								<div class="col-md-12 mt-0 mb-3">
									<legend class="mb-0 heading">Información del carrito abandonado</legend>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Cliente No.: ' .$customer_id, 'customer', array('class' => 'form-control-label', 'for' => 'customer')); ?>
										<span class="form-control"><?php echo $customer; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Id de Transación', 'Id de Transacion', array('class' => 'form-control-label', 'for' => 'transacion')); ?>
										<span class="form-control"><?php echo $transaction; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Codigo SAP', 'Codigo SAP', array('class' => 'form-control-label', 'for' => 'codigo sap')); ?>
										<span class="form-control"><?php echo $codigosap; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Email', 'email', array('class' => 'form-control-label', 'for' => 'email')); ?>
										<span class="form-control"><?php echo $email; ?></span>
									</div>
								</div>
								<?php if($total_discount != '$0.00'): ?>
									<div class="col-md-6 mb-3">
										<div class="form-group">
											<?php echo Form::label('Total de productos', 'total_products', array('class' => 'form-control-label', 'for' => 'total_products')); ?>
											<span class="form-control"><?php echo $total_products; ?></span>
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group">
											<?php echo Form::label('Descuento', 'total_discount', array('class' => 'form-control-label', 'for' => 'total_discount')); ?>
											<span class="form-control"><?php echo $total_discount; ?></span>
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group">
											<?php echo Form::label('Total', 'total', array('class' => 'form-control-label', 'for' => 'total')); ?>
											<span class="form-control text-blue font-weight-bold"><?php echo $total; ?></span>
										</div>
									</div>
								<?php else: ?>
									<div class="col-md-6 mb-3">
										<div class="form-group">
											<?php echo Form::label('Total', 'total', array('class' => 'form-control-label', 'for' => 'total')); ?>
											<span class="form-control text-blue font-weight-bold"><?php echo $total; ?></span>
										</div>
									</div>
								<?php endif; ?>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Tipo de pago', 'type', array('class' => 'form-control-label', 'for' => 'type')); ?>
										<span class="form-control"><?php echo $type; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Estatus del pago', 'status', array('class' => 'form-control-label', 'for' => 'status')); ?>
										<span class="form-control"><?php echo $status_txt; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Estatus del Pedido', 'order', array('class' => 'form-control-label', 'for' => 'order')); ?>
										<span class="form-control"><?php echo $order; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Numero de pedido SAP', 'ordersap', array('class' => 'form-control-label', 'for' => 'ordersap')); ?>
										<span class="form-control"><?php echo $ordersap; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Numero de factura SAP', 'factsap', array('class' => 'form-control-label', 'for' => 'factsap')); ?>
										<span class="form-control"><?php echo $factsap; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Paqueteria por la que se envia', 'package', array('class' => 'form-control-label', 'for' => 'package')); ?>
										<span class="form-control"><?php echo $package; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Numero de Guia', 'guide', array('class' => 'form-control-label', 'for' => 'guide')); ?>
										<span class="form-control"><?php echo $guide; ?></span>
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
						<?php if($bill_flag): ?>
						<fieldset>
							<div class="form-row">
								<div class="col-md-12 mt-0 mb-3">
									<legend class="mb-0 heading">Información de facturación</legend>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Razón social', 'business_name', array('class' => 'form-control-label', 'for' => 'business_name')); ?>
										<span class="form-control"><?php echo $business_name; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('RFC', 'rfc', array('class' => 'form-control-label', 'for' => 'rfc')); ?>
										<span class="form-control"><?php echo $rfc; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Calle', 'tax_data_street', array('class' => 'form-control-label', 'for' => 'tax_data_street')); ?>
										<span class="form-control"><?php echo $tax_data_street; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('# Exterior', 'tax_data_number', array('class' => 'form-control-label', 'for' => 'tax_data_number')); ?>
										<span class="form-control"><?php echo $tax_data_number; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('# Interior', 'tax_data_internal_number', array('class' => 'form-control-label', 'for' => 'tax_data_internal_number')); ?>
										<span class="form-control"><?php echo $tax_data_internal_number; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Colonia', 'tax_data_colony', array('class' => 'form-control-label', 'for' => 'tax_data_colony')); ?>
										<span class="form-control"><?php echo $tax_data_colony; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Código postal', 'tax_data_zipcode', array('class' => 'form-control-label', 'for' => 'tax_data_zipcode')); ?>
										<span class="form-control"><?php echo $tax_data_zipcode; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Ciudad', 'tax_data_city', array('class' => 'form-control-label', 'for' => 'tax_data_city')); ?>
										<span class="form-control"><?php echo $tax_data_city; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Estado', 'tax_data_state', array('class' => 'form-control-label', 'for' => 'tax_data_state')); ?>
										<span class="form-control"><?php echo $tax_data_state; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
                                    <div class="form-group">
										<?php echo Form::label('Forma de pago', 'payment_method', array('class' => 'form-control-label', 'for' => 'payment_method')); ?>
										<span class="form-control"><?php echo $payment_method; ?></span>
                                    </div>
                                </div>
								<div class="col-md-6 mb-3">
                                    <div class="form-group">
										<?php echo Form::label('Uso del CFDI', 'cfdi', array('class' => 'form-control-label', 'for' => 'cfdi')); ?>
										<span class="form-control"><?php echo $cfdi; ?></span>
                                    </div>
                                </div>
								<div class="col-md-6 mb-3">
                                    <div class="form-group">
										<?php echo Form::label('Régimen fiscal', 'sat_tax_regime', array('class' => 'form-control-label', 'for' => 'sat_tax_regime')); ?>
										<span class="form-control"><?php echo $sat_tax_regime; ?></span>
                                    </div>
                                </div>
								<?php if($csf != ''): ?>
									<div class="col-md-6 mb-3">
	                                    <div class="form-group">
											<?php echo Form::label('Constancia de Situación Fiscal', 'csf', array('class' => 'form-control-label', 'for' => 'csf')); ?>
											<span class="form-control"><?php echo Html::anchor(Uri::base(false).'assets/csf_final/'.$csf.'.pdf', 'Ver archivo', array('target' => '_blank')); ?></span>
	                                    </div>
	                                </div>
								<?php endif; ?>
							</div>
						</fieldset>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php if(!empty($products)): ?>
		<!-- TABLE -->
		<div class="row">
			<div class="col">
				<div class="card">
					<!-- CARD HEADER -->
					<div class="card-header border-0">
						<div class="form-row">
							<div class="col-md-9">
								<h3 class="mb-0">Lista de productos</h3>
							</div>
						</div>
					</div>
					<!-- LIGHT TABLE -->
					<div class="table-responsive" data-toggle="lists" data-list-values='["image", "code", "name", "quantity", "price", "total"]'>
						<table class="table align-items-center table-flush">
							<thead class="thead-light">
								<tr>
									<th scope="col" class="sort" data-sort="image">Imagen</th>
									<th scope="col" class="sort" data-sort="code">Codigo</th>
									<th scope="col" class="sort" data-sort="name">Producto</th>
									<th scope="col" class="sort" data-sort="quantity">Cantidad</th>
									<th scope="col" class="sort" data-sort="price">Precio</th>
									<th scope="col" class="sort" data-sort="total">Total</th>
								</tr>
							</thead>
							<tbody class="list">
								<?php if (is_array($products) && !empty($products)): ?>
									<?php foreach($products as $product): ?>
										<tr>
											<th class="image">
												<?php
												if (file_exists(DOCROOT.'assets/uploads/'.$product['image'])) {
													echo Asset::img($product['image'], array('class' => 'avatar'));
												} else {
													echo Asset::img('thumb_no_image.png', array('class' => 'avatar'));
												}
												?>
											</th>
											<td class="code">
												<?php echo $product['code']; ?>
											</td>
											<td class="name" title="<?php echo $product['name_complete']; ?>">
												<?php echo $product['name']; ?>
											</td>
											<td class="quantity">
												<?php echo $product['quantity']; ?>
											</td>
											<td class="price">
												<?php echo $product['price']; ?>
											</td>
											<td class="total">
												<?php echo $product['total']; ?>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>
