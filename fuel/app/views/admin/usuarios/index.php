<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Usuarios</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/usuarios', 'Usuarios'); ?>
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
	<!-- TABLE -->
	<div class="row">
		<div class="col">
			<div class="card">
				<!-- CARD HEADER -->
				<div class="card-header border-0">
					<?php echo Form::open(array('action' => 'admin/usuarios/buscar', 'method' => 'post')); ?>
					<div class="form-row">
						<div class="col-md-9">
							<h3 class="mb-0">Lista de usuarios</h3>
						</div>
						<div class="col-md-3 mb-0">
							<div class="input-group input-group-sm mt-3 mt-md-0">
								<?php echo Form::input('search', (isset($search) ? $search : ''), array('id' => 'search', 'class' => 'form-control', 'placeholder' => 'Usuario, email o numero', 'aria-describedby' => 'button-addon')); ?>
								<div class="input-group-append">
									<?php echo Form::submit(array('value'=> 'Buscar', 'name'=>'submit', 'id' => 'button-addon', 'class' => 'btn btn-outline-primary')); ?>
								</div>
							</div>
						</div>
					</div>
					<?php echo Form::close(); ?>
				</div>
				<!-- LIGHT TABLE -->
				<div class="table-responsive" data-toggle="lists" data-list-values='["id", "username", "name", "email", "type","sap_code", "banned","connected" ,"updated_at"]'>
					<table class="table align-items-center table-flush">
						<thead class="thead-light">
							<tr>
								<th scope="col" class="sort" data-sort="id">Codigo</th>
								<th scope="col" class="sort" data-sort="username">Usuario</th>
								<th scope="col" class="sort" data-sort="name">Nombre</th>
								<th scope="col" class="sort" data-sort="email">Email</th>
								<th scope="col" class="sort" data-sort="type">Tipo de cliente</th>
								<th scope="col" class="sort" data-sort="sap_code">Codigo<br> cliente SAP</th>
								<th scope="col" class="sort" data-sort="banned">Bloqueado</th>
								<th scope="col" class="sort" data-sort="connected">En linea</th>
								<th scope="col" class="sort" data-sort="updated_at">Ultima Actualizacion</th>
								<th scope="col"></th>
							</tr>
						</thead>
						<tbody class="list">
							<?php if(!empty($users)): ?>
								<?php foreach($users as $user): ?>
									<tr>
										<th class="id">
											<?php echo Html::anchor('admin/usuarios/info/'.$user['id'],$user['customer_id']); ?>
										</th>
										<th class="username">
											<?php echo Html::anchor('admin/usuarios/info/'.$user['id'], $user['username']); ?>
										</th>
										<td class="name">
											<?php echo $user['name']; ?>
										</td>
										<td class="email">
											<?php echo $user['email']; ?>
										</td>
										<td class="type">
											<?php echo $user['type']; ?>
										</td>
										<td class="sap_code">
											<?php echo $user['sap_code']; ?>
										</td>
										<td class="banned">
											<?php echo $user['banned']; ?>
										</td>
										<td>
											<span class="connected badge badge-dot mr-4">
												<i class="<?php echo ($user['connected'] == 'Conectado') ? 'bg-success' : 'bg-warning'; ?>"></i>
												<span class="status"><?php echo $user['connected']; ?></span>
											</span>
										</td>
										<td class="updated_at">
											<?php echo $user['updated_at']; ?>
										</td>
										<td class="text-right">
											<div class="dropdown">
												<a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													<i class="fas fa-ellipsis-v"></i>
												</a>
												<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
													<?php echo Html::anchor('admin/usuarios/info/'.$user['id'], 'Ver', array('class' => 'dropdown-item')); ?>
													<?php echo Html::anchor('admin/usuarios/editar/'.$user['id'], 'Editar', array('class' => 'dropdown-item')); ?>
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
