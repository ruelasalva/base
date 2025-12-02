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
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/cupones', 'Cupones'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/cupones/agregar', 'Agregar', array('class' => 'btn btn-sm btn-neutral')); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
	<!-- TABLE -->
	<div class="row">
		<div class="col">
			<div class="card">
				<!-- CARD HEADER -->
				<div class="card-header border-0">
					<?php echo Form::open(array('action' => 'admin/cupones/buscar', 'method' => 'post')); ?>
					<div class="form-row">
						<div class="col-md-9">
							<h3 class="mb-0">Lista de cupones</h3>
						</div>
						<div class="col-md-3 mb-0">
							<div class="input-group input-group-sm mt-3 mt-md-0">
								<?php echo Form::input('search', (isset($search) ? $search : ''), array('id' => 'search', 'class' => 'form-control', 'placeholder' => 'Nombre', 'aria-describedby' => 'button-addon')); ?>
								<div class="input-group-append">
									<?php echo Form::submit(array('value'=> 'Buscar', 'name'=>'submit', 'id' => 'button-addon', 'class' => 'btn btn-outline-primary')); ?>
								</div>
							</div>
						</div>
					</div>
					<?php echo Form::close(); ?>
				</div>
				<!-- LIGHT TABLE -->
				<div class="table-responsive" data-toggle="lists" data-list-values='["name", "discount", "quantity", "available", "used", "minimum", "validity", "date", "deleted"]'>
					<table class="table align-items-center table-flush">
						<thead class="thead-light">
							<tr>
								<th scope="col" class="sort" data-sort="name">Nombre</th>
								<th scope="col" class="sort" data-sort="discount">Descuento</th>
								<th scope="col" class="sort" data-sort="quantity">Cantidad</th>
								<th scope="col" class="sort" data-sort="available">Disponibles</th>
								<th scope="col" class="sort" data-sort="used">Utilizados</th>
								<th scope="col" class="sort" data-sort="minimum">Mínimo</th>
								<th scope="col" class="sort" data-sort="validity">Vigencia</th>
								<th scope="col" class="sort" data-sort="date">Fecha de creación</th>
								<th scope="col" class="sort" data-sort="deleted">Estatus</th>
								<th scope="col"></th>
							</tr>
						</thead>
						<tbody class="list">
							<?php if(!empty($coupons)): ?>
								<?php foreach($coupons as $coupon): ?>
									<tr>
										<th class="name">
											<?php echo Html::anchor('admin/cupones/info/'.$coupon['id'], $coupon['name']); ?>
										</th>
										<td class="discount">
											<?php echo $coupon['discount']; ?>
										</td>
										<td class="quantity">
											<?php echo $coupon['quantity']; ?>
										</td>
										<td class="available">
											<?php echo $coupon['available']; ?>
										</td>
                                        <td class="used">
											<?php echo $coupon['used']; ?>
										</td>
										<td class="minimum">
											<?php echo $coupon['minimum']; ?>
										</td>
                                        <td class="validity">
											<?php echo $coupon['validity']; ?>
										</td>
                                        <td class="date">
											<?php echo $coupon['date']; ?>
										</td>
										<td class="deleted">
											<?php echo $coupon['deleted']; ?>
										</td>
										<td class="text-right">
											<div class="dropdown">
												<a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													<i class="fas fa-ellipsis-v"></i>
												</a>
												<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
													<?php echo Html::anchor('admin/cupones/info/'.$coupon['id'], 'Ver', array('class' => 'dropdown-item')); ?>
													<div class="dropdown-divider"></div>
													<?php echo Html::anchor('admin/cupones/eliminar/'.$coupon['id'], 'Eliminar', array('class' => 'dropdown-item delete-item')); ?>
												</div>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<th scope="row">
										No existen registros
									</th>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
				<?php if($pagination != ''): ?>
					<!-- CARD FOOTER -->
					<div class="card-footer py-4">
						<?php echo $pagination; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
