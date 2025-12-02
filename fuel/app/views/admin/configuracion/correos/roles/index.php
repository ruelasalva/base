<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Correos - Roles</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/configuracion/correos', 'Correos'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">Roles</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/configuracion/correos/roles/agregar', 'Nuevo Rol', array('class' => 'btn btn-sm btn-neutral')); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
	<div class="row">
		<div class="col">
			<div class="card">
				<!-- CARD HEADER -->
				<div class="card-header border-0">
					<?php echo Form::open(array('action' => 'admin/configuracion/correos/roles/buscar', 'method' => 'post')); ?>
					<div class="form-row">
						<div class="col-md-9">
							<h3 class="mb-0">Lista de Roles de Correo</h3>
						</div>
						<div class="col-md-3 mb-0">
							<div class="input-group input-group-sm mt-3 mt-md-0">
								<?php echo Form::input('search', (isset($search) ? $search : ''), array(
									'id' => 'search',
									'class' => 'form-control',
									'placeholder' => 'Rol o correo',
									'aria-describedby' => 'button-addon'
								)); ?>
								<div class="input-group-append">
									<?php echo Form::submit(array(
										'value'=> 'Buscar',
										'name'=>'submit',
										'id' => 'button-addon',
										'class' => 'btn btn-outline-primary'
									)); ?>
								</div>
							</div>
						</div>
					</div>
					<?php echo Form::close(); ?>
				</div>    

				<!-- LIGHT TABLE -->
				<div class="table-responsive" data-toggle="lists" data-list-values='["id","role","from_email","reply_to_email","to_emails","updated_at"]'>
					<table class="table align-items-center table-flush">
						<thead class="thead-light">
							<tr>
								<th>ID</th>
								<th>Rol</th>
								<th>From</th>
								<th>Reply-To</th>
								<th>Destinatarios</th>
								<th>Actualizado</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php if(!empty($roles)): ?>
								<?php foreach($roles as $r): ?>
								<tr>
									<td class="id">
										<?php echo Html::anchor('admin/configuracion/correos/roles/info/'.$r['id'], $r['id']); ?>
									</td>
									<td class="role"><?php echo $r['role']; ?></td>
									<td class="from_email"><?php echo $r['from_email']; ?> (<?php echo $r['from_name']; ?>)</td>
									<td class="reply_to_email"><?php echo $r['reply_to']; ?></td>
									<td class="to_emails"><?php echo $r['to_emails']; ?></td>
									<td class="updated_at"><?php echo $r['updated_at']; ?></td>
									<td class="text-right">
										<div class="dropdown">
											<a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown">
												<i class="fas fa-ellipsis-v"></i>
											</a>
											<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
												<?php echo Html::anchor('admin/configuracion/correos/roles/editar/'.$r['id'], 'Editar', ['class'=>'dropdown-item']); ?>
												<?php echo Html::anchor('admin/configuracion/correos/roles/eliminar/'.$r['id'], 'Eliminar', ['class'=>'dropdown-item','onclick'=>"return confirm('¿Seguro que deseas eliminar este rol?');"]); ?>
											</div>
										</div>
									</td>
								</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="7" class="text-center">No existen roles configurados.</td>
								</tr>
							<?php endif; ?>
						</tbody>


					</table>
				</div>
				<?php if($pagination != ''): ?>
					<div class="card-footer py-4">
						<?php echo $pagination; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
