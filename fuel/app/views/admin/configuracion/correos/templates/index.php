<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Correos - Plantillas</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/configuracion/correos', 'Correos'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">Plantillas</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/configuracion/correos/templates/agregar', 'Nueva Plantilla', array('class' => 'btn btn-sm btn-neutral')); ?>
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
					<?php echo Form::open(array('action' => 'admin/configuracion/correos/templates/buscar', 'method' => 'post')); ?>
					<div class="form-row">
						<div class="col-md-9">
							<h3 class="mb-0">Lista de Plantillas de Correo</h3>
						</div>
						<div class="col-md-3 mb-0">
							<div class="input-group input-group-sm mt-3 mt-md-0">
								<?php echo Form::input('search', (isset($search) ? $search : ''), array(
									'name' => 'search', // obligatorio para evitar warning
									'id' => 'search',
									'class' => 'form-control',
									'placeholder' => 'Código, rol o asunto',
									'aria-describedby' => 'button-addon'
								)); ?>
								<div class="input-group-append">
									<?php echo Form::submit(array(
										'value'=> 'Buscar',
										'name' => 'submit',
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
				<div class="table-responsive" data-toggle="lists" data-list-values='["id","code","role","subject","view","updated_at"]'>
					<table class="table align-items-center table-flush">
						<thead class="thead-light">
							<tr>
								<th>ID</th>
								<th>Código</th>
								<th>Rol</th>
								<th>Asunto</th>
								<th>Vista</th>
								<th>Actualizado</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php if(!empty($templates)): ?>
								<?php foreach($templates as $tpl): ?>
								<tr>
									<td class="id">
										<?php echo Html::anchor('admin/configuracion/correos/templates/info/'.$tpl['id'], $tpl['id']); ?>
									</td>
									<td class="code"><?php echo $tpl['code']; ?></td>
									<td class="role"><?php echo $tpl['role']; ?></td>
									<td class="subject"><?php echo $tpl['subject']; ?></td>
									<td class="view"><?php echo $tpl['view']; ?></td>
									<td class="updated_at"><?php echo $tpl['updated_at']; ?></td>
									<td class="text-right">
										<div class="dropdown">
											<a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown">
												<i class="fas fa-ellipsis-v"></i>
											</a>
											<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
												<?php echo Html::anchor('admin/configuracion/correos/templates/info/'.$tpl['id'], 'Ver', ['class'=>'dropdown-item']); ?>
												<?php echo Html::anchor('admin/configuracion/correos/templates/editar/'.$tpl['id'], 'Editar', ['class'=>'dropdown-item']); ?>
												<?php echo Html::anchor(
													'admin/configuracion/correos/templates/eliminar/'.$tpl['id'],
													'Eliminar',
													['class'=>'dropdown-item','onclick'=>"return confirm('¿Seguro que deseas eliminar esta plantilla?');"]
												); ?>
											</div>
										</div>
									</td>
								</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="7" class="text-center">No existen plantillas configuradas.</td>
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

