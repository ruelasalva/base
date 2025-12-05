<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Proveedores</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/proveedores', 'Proveedores'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/proveedores/pagos', '<i class="fas fa-money-bill-wave"></i> Pagos', array('class' => 'btn btn-sm btn-success mr-2')); ?>
					<?php echo Html::anchor('admin/proveedores/recepciones', '<i class="fas fa-box-open"></i> Recepciones', array('class' => 'btn btn-sm btn-info mr-2')); ?>
					<?php echo Html::anchor('admin/proveedores/agregar', '<i class="fas fa-plus"></i> Agregar', array('class' => 'btn btn-sm btn-neutral')); ?>
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
					<?php echo Form::open(array('action' => 'admin/proveedores/buscar', 'method' => 'post')); ?>
					<div class="form-row">
						<div class="col-md-9">
							<h3 class="mb-0">Lista de Proveedores</h3>
						</div>
						<div class="col-md-3 mb-0">
							<div class="input-group input-group-sm mt-3 mt-md-0">
								<?php echo Form::input('search', (isset($search) ? $search : ''), array('id' => 'search', 'class' => 'form-control', 'placeholder' => 'Usuario o email', 'aria-describedby' => 'button-addon')); ?>
								<div class="input-group-append">
									<?php echo Form::submit(array('value'=> 'Buscar', 'name'=>'submit', 'id' => 'button-addon', 'class' => 'btn btn-outline-primary')); ?>
								</div>
							</div>
						</div>
					</div>
					<?php echo Form::close(); ?>
				</div>
				<!-- LIGHT TABLE -->
				<div class="table-responsive" data-toggle="lists" data-list-values='["username", "name", "email", "rfc", "code_sap", "group", "status", "banned"]'>
					<table class="table align-items-center table-flush">
						<thead class="thead-light">
							<tr>
								<th scope="col" class="sort" data-sort="username">Usuario</th>
								<th scope="col" class="sort" data-sort="name">Razon Social</th>
								<th scope="col" class="sort" data-sort="email">Email</th>
								<th scope="col" class="sort" data-sort="rfc">RFC</th>
								<th scope="col" class="sort" data-sort="code_sap">Codigo Sistema</th>
								<th scope="col" class="sort" data-sort="status">En linea</th>
								<th scope="col" class="sort" data-sort="banned">Bloqueado</th>
								<th scope="col"></th>
							</tr>
						</thead>
						<tbody class="list">
							<?php if(!empty($providers)): ?>
								<?php foreach($providers as $provider): ?>
									<tr>
										<th class="username">
											<?php echo Html::anchor('admin/proveedores/info/'.$provider['id'], $provider['username']); ?>
										</th>
										<td class="name">
											<?php echo $provider['name']; ?>
										</td>
										<td class="email">
											<?php echo $provider['email']; ?>
										</td>
										<td class="rfc">
											<?php echo $provider['rfc']; ?>
										</td>
										<td class="code_sap">
											<?php echo $provider['code_sap']; ?>
										</td>
										<td>
											<span class="badge badge-dot mr-4">
												<i class="<?php echo ($provider['connected'] == 'Conectado') ? 'bg-success' : 'bg-warning'; ?>"></i>
												<span class="status"><?php echo $provider['connected']; ?></span>
											</span>
										</td>
										<td class="banned">
											<?php echo $provider['banned']; ?>
										</td>
										<td class="text-right">
											<div class="dropdown">
												<a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													<i class="fas fa-ellipsis-v"></i>
												</a>
												<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
													<?php echo Html::anchor('admin/proveedores/info/'.$provider['id'], '<i class="fas fa-eye"></i> Ver', array('class' => 'dropdown-item')); ?>
													<?php echo Html::anchor('admin/proveedores/editar/'.$provider['id'], '<i class="fas fa-edit"></i> Editar', array('class' => 'dropdown-item')); ?>
													<div class="dropdown-divider"></div>
													<?php echo Html::anchor('admin/proveedores/pagos/create/'.$provider['id'], '<i class="fas fa-money-bill"></i> Crear Pago', array('class' => 'dropdown-item')); ?>
													<?php echo Html::anchor('admin/proveedores/recepciones/create', '<i class="fas fa-truck-loading"></i> Nueva Recepción', array('class' => 'dropdown-item')); ?>
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
