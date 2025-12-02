<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Carritos Abandonados</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/abandonados', 'Carritos abandonados'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					
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
					<?php echo Form::open(array('action' => 'admin/abandonados/buscar', 'method' => 'post')); ?>
					<div class="form-row">
						<div class="col-md-9">
							<h3 class="mb-0">Lista de de carritos abandonados</h3>
						</div>
						<div class="col-md-3 mb-0">
							<div class="input-group input-group-sm mt-3 mt-md-0">
								<?php echo Form::input('search', (isset($search) ? $search : ''), array('id' => 'search', 'class' => 'form-control', 'placeholder' => 'ID o nombre', 'aria-describedby' => 'button-addon')); ?>
								<div class="input-group-append">
									<?php echo Form::submit(array('value'=> 'Buscar', 'name'=>'submit', 'id' => 'button-addon', 'class' => 'btn btn-outline-primary')); ?>
								</div>
							</div>
						</div>
					</div>
					<?php echo Form::close(); ?>
				</div>
				<!-- LIGHT TABLE -->
				<div class="table-responsive" data-toggle="lists" data-list-values='["id", "customer", "email", "transaction", "type", "total", "status", "sale_date", "order"]'>
					<table class="table align-items-center table-flush">
						<thead class="thead-light">
							<tr>
								<th scope="col" class="sort" data-sort="id">ID</th>
								<th scope="col" class="sort" data-sort="customer">Cliente</th>
								<th scope="col" class="sort" data-sort="email">Email</th>
								<th scope="col" class="sort" data-sort="transaction">ID transación</th>
								<th scope="col" class="sort" data-sort="type">Tipo de pago</th>
								<th scope="col" class="sort" data-sort="status">Estado <br> del Pago</th>
								<th scope="col" class="sort" data-sort="total">Total</th>
								<th scope="col" class="sort" data-sort="sale_date">Fecha</th>
								<th scope="col" class="sort" data-sort="order">Estado <br>del Pedido</th>
								<th scope="col"></th>
							</tr>
						</thead>
						<tbody class="list">
							<?php if(!empty($sales)): ?>
								<?php foreach($sales as $sale): ?>
									<tr class="<?php echo ($sale['status'] == 'Cancelada') ? 'text-danger' : ''; ?>">
										<th class="id">
											<?php echo Html::anchor('admin/abandonados/info/'.$sale['id'], $sale['id']); ?>
										</th>
										<td class="customer">
											<?php echo $sale['customer']; ?>
										</td>
										<td class="email">
											<?php echo $sale['email']; ?>
										</td>
										<td class="transaction">
											<?php echo $sale['transaction']; ?>
										</td>
										<td class="type">
											<?php echo $sale['type']; ?>
										</td>
										<td class="status">
											<?php echo $sale['status']; ?>
										</td>
										<td class="total">
											<?php echo $sale['total']; ?>
										</td>
										<td class="sale_date">
											<?php echo $sale['sale_date']; ?>
										</td>
										<td class="order">
											<?php echo $sale['order']; ?>
										</td>
										<td class="text-right">
											<div class="dropdown">
												<a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													<i class="fas fa-ellipsis-v"></i>
												</a>
												<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
													<?php echo Html::anchor('admin/abandonados/info/'.$sale['id'], 'Ver', array('class' => 'dropdown-item')); ?>
													<?php echo Html::anchor('admin/abandonados/editar/'.$sale['id'], 'Editar', array('class' => 'dropdown-item')); ?>
													<?php echo Html::anchor('admin/ventas/agregar_factura/'.$sale['id'], 'Agregar Factura', array('class' => 'dropdown-item')); ?>
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
