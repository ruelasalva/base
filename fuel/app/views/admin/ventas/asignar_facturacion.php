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
								<?php echo Html::anchor('admin/ventas/asignar_facturacion/'.$id, 'Asignar facrtuación'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/ventas/info/'.$id, 'Ver', array('class' => 'btn btn-sm btn-neutral')); ?>
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
						<h3 class="mb-0">Asignar datos de facturación</h3>
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
										<?php echo Form::label('Venta #'.$id, 'id', array('class' => 'form-control-label', 'for' => 'id')); ?>
										<?php echo Form::input('id', (isset($id) ? $id : ''), array('id' => 'id', 'class' => 'form-control', 'placeholder' => 'Cliente', 'readonly' => 'readonly')); ?>
                                        <small id="id-help" class="form-text text-muted">El ID de la Venta no puede ser editado.</small>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Cliente', 'customer', array('class' => 'form-control-label', 'for' => 'customer')); ?>
										<?php echo Form::input('customer', (isset($customer) ? $customer : ''), array('id' => 'customer', 'class' => 'form-control', 'placeholder' => 'Cliente', 'readonly' => 'readonly')); ?>
                                        <small id="customer-help" class="form-text text-muted">El nombre de Cliente no puede ser editado.</small>
									</div>
								</div>
							</div>
						</fieldset>
						<fieldset>
							<div class="form-row">
								<div class="col-md-12 mt-0 mb-3">
									<legend class="mb-0 heading">Información de facturación</legend>
								</div>
                                <div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('RFC', 'rfc', array('class' => 'form-control-label', 'for' => 'rfc')); ?>
											<?php echo Form::select('rfc',(isset($rfc)? $rfc : 1), $rfc_opts, array('class' => 'form-control ', 'data-toggle' => 'select')) ?>
											<?php if(isset($errors['rfc'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['rfc']; ?>
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
