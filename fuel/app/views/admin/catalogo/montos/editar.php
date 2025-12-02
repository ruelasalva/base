<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Catálogo</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/catalogo/montos', 'Montos'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/catalogo/montos/info/'.$id, $name); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/catalogo/montos/editar/'.$id, 'Editar'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/catalogo/montos/agregar_rango/'.$id, 'Agregar rango', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php echo Html::anchor('admin/catalogo/montos/info/'.$id, 'Ver', array('class' => 'btn btn-sm btn-neutral')); ?>
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
										<legend class="mb-0 heading">Información del monto</legend>
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
								</div>
							</fieldset>
							<?php echo Form::submit(array('value'=> 'Guardar', 'name'=>'submit', 'class' => 'btn btn-primary')); ?>
						<?php echo Form::close(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php if(!empty($prices_amounts)): ?>
	<!-- TABLE -->
	<div class="row">
		<div class="col">
			<div class="card">
				<!-- CARD HEADER -->
				<div class="card-header border-0">
					<div class="form-row">
						<div class="col-md-9">
							<h3 class="mb-0">Rangos por monto</h3>
						</div>
					</div>
				</div>
				<!-- LIGHT TABLE -->
				<div class="table-responsive">
					<table class="table align-items-center">
						<thead class="thead-light">
							<tr>
								<th scope="col">Monto mínimo</th>
								<th scope="col">Monto máximo</th>
								<th scope="col">Porcentaje</th>
								<th scope="col"></th>
							</tr>
						</thead>
						<tbody class="list">
							<?php foreach($prices_amounts as $price_amount): ?>
								<tr>
									<td>
										<?php echo $price_amount['min_amount']; ?>
									</td>
									<td>
										<?php echo $price_amount['max_amount']; ?>
									</td>
									<td>
										<?php echo $price_amount['percentage']; ?>
									</td>
									<td class="text-right">
										<div class="dropdown">
											<a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<i class="fas fa-ellipsis-v"></i>
											</a>
											<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
												<?php echo Html::anchor('admin/catalogo/montos/info_rango/'.$price_amount['id'], 'Ver', array('class' => 'dropdown-item')); ?>
												<?php echo Html::anchor('admin/catalogo/montos/editar_rango/'.$price_amount['id'], 'Editar', array('class' => 'dropdown-item')); ?>
												<div class="dropdown-divider"></div>
												<?php echo Html::anchor('admin/catalogo/montos/eliminar_rango/'.$price_amount['id'], 'Eliminar', array('class' => 'dropdown-item delete-item')); ?>
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
