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
								<?php echo Html::anchor('admin/ventas/editar_voucher/'.$id, 'Editar Comprobante'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/ventas/info/'.$id, 'Ver venta', array('class' => 'btn btn-sm btn-neutral')); ?>
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
						<h3 class="mb-0">Editas archivo</h3>
					</div>
					<!-- CARD BODY -->
					<div class="card-body">
						<div class="form-row">
							<?php if($sale_history['status_id'] == 1): ?>
									<div class="mb-3">
										<?php echo Form::open(array(
											'action'     => '',
											'method'     => 'post',
											'class'      => 'voucher',
											'id'         => '',
											'enctype'    => 'multipart/form-data',
											'novalidate' => true
										)); ?>
											<div class="form-group upload_box">
												Sube el comprobante nuevo, correcto o modificado de la transferencia o deposito.<br>
												<?php echo Form::file('voucher'); ?>
												<small>
													<br><strong>NOTA:</strong> El tamaño del archivo JPG/JPEG/PNG no debe exceder de 15 Mb.<br>
												</small>
											</div>
											<?php echo Form::button('submit', 'Enviar', array('class' => 'btn btn-primary btn-block text-uppercase')); ?>
										<?php echo Form::close(); ?>
									</div>
								<?php endif; ?>
							</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
