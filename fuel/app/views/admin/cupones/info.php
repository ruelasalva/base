<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Cupones</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/cupones', 'Cupones'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/cupones/info/'.$id, $name); ?>
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
						<h3 class="mb-0">Ver información</h3>
					</div>
					<!-- CARD BODY -->
					<div class="card-body">
						<fieldset>
							<div class="form-row">
								<div class="col-md-12 mt-0 mb-3">
									<legend class="mb-0 heading">Información del cupón</legend>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Nombre', 'name', array('class' => 'form-control-label', 'for' => 'name')); ?>
										<span class="form-control"><?php echo $name; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Texto del cupón', 'code', array('class' => 'form-control-label', 'for' => 'code')); ?>
										<span class="form-control"><?php echo $code; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Descuento', 'discount', array('class' => 'form-control-label', 'for' => 'discount')); ?>
										<span class="form-control"><?php echo $discount; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Cantidad de cupones', 'quantity', array('class' => 'form-control-label', 'for' => 'quantity')); ?>
										<span class="form-control"><?php echo $quantity; ?></span>
									</div>
								</div>
                                <div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Disponibles', 'available', array('class' => 'form-control-label', 'for' => 'available')); ?>
										<span class="form-control"><?php echo $available; ?></span>
									</div>
								</div>
                                <div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Utilizados', 'used', array('class' => 'form-control-label', 'for' => 'used')); ?>
										<span class="form-control"><?php echo $used; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Fecha de inicio', 'start_date', array('class' => 'form-control-label', 'for' => 'start_date')); ?>
										<span class="form-control"><?php echo $start_date; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Fecha final', 'end_date', array('class' => 'form-control-label', 'for' => 'end_date')); ?>
										<span class="form-control"><?php echo $end_date; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Cantidad mínima', 'minimum', array('class' => 'form-control-label', 'for' => 'minimum')); ?>
										<span class="form-control"><?php echo $minimum_txt; ?></span>
									</div>
								</div>
								<?php if($minimum == 1): ?>
									<div class="col-md-6 mb-3">
										<div class="form-group">
											<?php echo Form::label('Mínimo del pedido', 'total_minimum', array('class' => 'form-control-label', 'for' => 'total_minimum')); ?>
											<span class="form-control"><?php echo $total_minimum; ?></span>
										</div>
									</div>
								<?php endif; ?>
							</div>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
	</div>

    <?php if(!empty($codes)): ?>
    	<!-- TABLE -->
    	<div class="row">
    		<div class="col">
    			<div class="card">
    				<!-- CARD HEADER -->
    				<div class="card-header border-0">
    					<div class="form-row">
    						<div class="col-md-9">
    							<h3 class="mb-0">Códigos</h3>
    						</div>
    					</div>
    				</div>
    				<!-- LIGHT TABLE -->
    				<div class="table-responsive">
    					<table class="table align-items-center table-flush">
    						<thead class="thead-light">
    							<tr>
    								<th scope="col">Código</th>
    								<th scope="col">Usado</th>
									<th scope="col">En la venta</th>
    							</tr>
    						</thead>
    						<tbody class="list">
    							<?php foreach($codes as $code): ?>
    								<tr>
    									<th>
    										<?php echo $code['code']; ?>
    									</th>
                                        <td>
    										<?php echo $code['used']; ?>
    									</td>
										<td>
    										<?php echo $code['sale_id']; ?>
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
