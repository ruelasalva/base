<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-5">
					<h6 class="h2 text-white d-inline-block mb-0">Información de la Factura</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/compras/facturas', 'Facturas'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								#<?php echo $factura->uuid; ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-7 text-right">
					<?php echo Html::anchor(Uri::base(false).'assets/facturas/proveedores/'.$factura->provider_id.'/'.$factura->pdf, 'Descargar PDF', ['class' => 'btn btn-sm btn-neutral', 'target' => '_blank']); ?>
					<?php echo Html::anchor(Uri::base(false).'assets/facturas/proveedores/'.$factura->provider_id.'/'.$factura->xml, 'Descargar XML', ['class' => 'btn btn-sm btn-neutral', 'target' => '_blank']); ?>
					
					<?php if ($factura->status == '0'): ?>
						<?php echo Html::anchor('admin/compras/facturas/eliminar/'.$factura->id, 'Eliminar', [
							'class' => 'btn btn-sm btn-danger',
							'onclick' => 'return confirm("¿Seguro que deseas eliminar esta factura?")'
						]); ?>
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

				<!-- INFO FACTURA -->
				<div class="card">
					<div class="card-header">
						<h3 class="mb-0">Detalles de la Factura</h3>
					</div>
					<div class="card-body">
						<table class="table">
							<tr>
								<th>Proveedor registrado:</th>
								<td><?php echo $provider_name; ?></td>
							</tr>
							<tr>
								<th>UUID:</th>
								<td><?php echo $factura->uuid; ?></td>
							</tr>
							<tr>
								<th>Total:</th>
								<td><strong class="text-blue">$<?php echo number_format($factura->total, 2); ?></strong></td>
							</tr>
							<tr>
								<th>Fecha de subida:</th>
								<td><?php echo date('d/m/Y H:i', $factura->created_at); ?></td>
							</tr>
							<tr>
								<th>Estado:</th>
								<td><?php echo $badge_html; ?></td>
							</tr>
							<?php if (!empty($factura->order_id)): ?>
							<tr>
								<th>Orden de compra vinculada:</th>
								<td>
									<?php echo Html::anchor('admin/compras/ordenes/info/'.$factura->order_id, 'Ver OC #'.$factura->order_id, ['target' => '_blank']); ?>
								</td>
							</tr>
							<?php endif; ?>
						</table>
					</div>
				</div>

				<!-- DATOS CFDI -->
				<div class="card mt-4">
					<div class="card-header">
						<h3 class="mb-0">Datos del CFDI</h3>
					</div>
					<div class="card-body">
						<div class="row">
							<!-- Emisor -->
							<div class="col-md-6">
								<h5 class="heading">Datos del Emisor</h5>
								<p><strong>Nombre:</strong> <?= $invoice_data['emisor_nombre'] ?? 'N/A'; ?></p>
								<p><strong>RFC:</strong> <?= $invoice_data['emisor_rfc'] ?? 'N/A'; ?></p>
							</div>
							<!-- Receptor -->
							<div class="col-md-6">
								<h5 class="heading">Datos del Receptor</h5>
								<p><strong>Nombre:</strong> <?= $invoice_data['receptor_nombre'] ?? 'N/A'; ?></p>
								<p><strong>RFC:</strong> <?= $invoice_data['receptor_rfc'] ?? 'N/A'; ?></p>
								<?php if (!empty($empresa_rfc) && $empresa_rfc != ($invoice_data['receptor_rfc'] ?? '')): ?>
									<span class="badge badge-danger">⚠ RFC no coincide con el de la empresa</span>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>

				<!-- LISTA DE PRODUCTOS -->
				<?php if (!empty($productos)): ?>
				<div class="card mt-4">
					<div class="card-header">
						<h3 class="mb-0">Productos en la Factura</h3>
					</div>
					<div class="table-responsive">
						<table class="table align-items-center table-flush">
							<thead class="thead-light">
								<tr>
									<th>SKU</th>
									<th>Descripción</th>
									<th>Cantidad</th>
									<th>Unidad</th>
									<th>Precio Unitario</th>
									<th>Subtotal</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($productos as $producto): ?>
									<tr>
										<td><?= $producto['noidentificacion']; ?></td>
										<td><?= $producto['descripcion']; ?></td>
										<td><?= $producto['cantidad']; ?></td>
										<td><?= $producto['clave_unidad']; ?></td>
										<td>$<?= $producto['valor_unitario']; ?></td>
										<td>$<?= $producto['importe']; ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
				<?php endif; ?>

				<!-- FORMULARIO PARA CAMBIAR ESTADO -->
				<div class="card mt-6">
					<div class="card-header">
						<h3 class="mb-0">Actualizar Estado</h3>
					</div>
					<div class="card-body">
						<?php echo Form::open(); ?>
						<div class="form-row">
							<!-- Estado -->
							<div class="col-md-4">
								<div class="form-group <?= $classes['status']['form-group']; ?>">
									<?php echo Form::label('Estado', 'status', ['class' => 'form-control-label']); ?>
									<?php echo Form::select('status', $factura->status, Helper_Purchases::options('bill'), ['class' => 'form-control']); ?>
								</div>
							</div>

							<!-- Mensaje -->
							<div class="col-md-4">
								<div class="form-group <?= $classes['message']['form-group']; ?>">
									<?php echo Form::label('Mensaje', 'message', ['class' => 'form-control-label']); ?>
									<?php echo Form::textarea('message', $factura->message, ['class' => 'form-control', 'rows' => '2']); ?>
								</div>
							</div>

							<!-- Fecha de Pago -->
							<div class="col-md-4">
								<div class="form-group <?= $classes['payment_date']['form-group']; ?>">
									<?php echo Form::label('Fecha de Pago', 'payment_date', ['class' => 'form-control-label']); ?>
									<?php echo Form::input('payment_date', 
										(!empty($factura->payment_date) 
											? date('Y-m-d', $factura->payment_date) 
											: $tentative_payment
										), 
										['type' => 'date', 'class' => 'form-control']
									); ?>
									<small class="form-text text-muted">
										Fecha sugerida automáticamente 
										(<?= !empty($provider->payment_terms_id) ? 'por términos del proveedor' : 'por configuración global'; ?>, editable por el usuario).
									</small>
								</div>
							</div>




							<!-- Botón -->
							<div class="text-right col-md-12">
								<?php echo Form::submit(['value' => 'Actualizar Estado', 'name' => 'submit', 'class' => 'btn btn-primary']); ?>
							</div>
							<div class="text-right col-md-12">
								<?php if ((int)$factura->status === 2): ?>
								<?php echo Html::anchor(
									'admin/compras/contrarecibos/crear_desde_factura/'.$factura->id,
									'<i class="fas fa-receipt"></i> Generar Contrarecibo',
									['class' => 'btn btn-sm btn-primary',
									'onclick' => 'return confirm("¿Deseas generar un contrarecibo para esta factura?");']
								); ?>
							<?php endif; ?>
							</div>
						</div>
						<?php echo Form::close(); ?>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
