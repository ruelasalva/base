<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Tareas Pendientes</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/helpdesk/task', 'Pendientes'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/helpdesk/task/agregar', 'Nuevo', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php echo Html::anchor('admin/helpdesk/csv/tasks', 'Exporta CSV', array('class' => 'btn btn-sm btn-neutral')); ?>
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
					<?php echo Form::open(array('action' => 'admin/helpdesk/task/buscar', 'method' => 'post')); ?>
					<div class="form-row">
						<div class="col-md-9">
							<h3 class="mb-0">Lista de Tareas Pendientes</h3>
						</div>
						<div class="col-md-3 mb-0">
							<div class="input-group input-group-sm mt-3 mt-md-0">
								<?php echo Form::input('search', (isset($search) ? $search : ''), array('id' => 'search', 'class' => 'form-control', 'placeholder' => 'Descripcion', 'aria-describedby' => 'button-addon')); ?>
								<div class="input-group-append">
									<?php echo Form::submit(array('value'=> 'Buscar', 'name'=>'submit', 'id' => 'button-addon', 'class' => 'btn btn-outline-primary')); ?>
								</div>
							</div>
						</div>
					</div>
					<?php echo Form::close(); ?>
				</div>
				<!-- LIGHT TABLE -->
				<div class="table-responsive" data-toggle="lists" data-list-values='["id", "user_id", "description", "status_id", "created_at" , "updated_at", "finish_at","comments"]'>
					<table class="table align-items-center table-flush">
						<thead class="thead-light">
							<tr>
								<th scope="col" class="sort" data-sort="id">ID</th>
								<th scope="col" class="sort" data-sort="created_at">Fecha creación</th>
								<th scope="col" class="sort" data-sort="employee_id">Empleado</th>
								<th scope="col" class="sort" data-sort="department_id">Departamento</th>
								<th scope="col" class="sort" data-sort="description">Descripción detallada</th>
								<th scope="col" class="sort" data-sort="commitment_at">Fecha compromiso</th>
								<th scope="col" class="sort" data-sort="finish_at">Fecha finalizado</th>
								<th scope="col" class="sort" data-sort="comments">Comentarios</th>
								<th scope="col" class="sort" data-sort="status_id">Estatus</th>
								<th scope="col"></th>
							</tr>
						</thead>
						<tbody class="list">
							<?php if(!empty($tasks)): ?>
								<?php foreach($tasks as $task): ?>
									<?php $statusClass = '';
									switch ($task['status_id']) {
										case 'Asignado':
										$statusClass = 'text-primary';
										break;
										case 'Atendido':
										$statusClass = 'text-info';
										break;
										case 'Finalizado':
										$statusClass = 'text-success';
										break;
										case 'Cancelado':
										$statusClass = 'text-danger';
										break;
										default:
										$statusClass = '';
										break;
									}
									?>
									<tr class="<?php echo $statusClass; ?>">
										<th class="id">
											<?php echo Html::anchor('admin/helpdesk/task/info/'.$task['id'], $task['id']); ?>
										</th>
										<th class="created_at">
											<?php echo $task['created_at']; ?>
										</th>
										<th class="employee_id">
											<?php echo $task['employee_id']; ?>
										</th>
										<th class="department_id">
											<?php echo  $task['department_id']; ?>
										</th>
										<th class="description">
											<?php echo $task['description']; ?>
										</th>
										<th class="commitment_at">
											<?php echo $task['commitment_at']; ?>
										</th>
										<th class="finish_at">
											<?php echo $task['finish_at']; ?>
										</th>
										<th class="comments">
											<?php echo $task['comments']; ?>
										</th>
										<th class="status_id">
											<?php echo $task['status_id']; ?>
										</th>
										<td class="text-right">
											<div class="dropdown">
												<a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													<i class="fas fa-ellipsis-v"></i>
												</a>
												<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
													<?php echo Html::anchor('admin/helpdesk/task/info/'.$task['id'], 'Ver', array('class' => 'dropdown-item')); ?>
													<?php echo Html::anchor('admin/helpdesk/task/editar/'.$task['id'], 'Editar', array('class' => 'dropdown-item')); ?>
													<?php echo Html::anchor('admin/helpdesk/task/finalizar/'.$task['id'], 'Finalizar', array('class' => 'dropdown-item task-modal', 'data-task' => $task['id'])); ?>
													<?php echo Html::anchor('admin/helpdesk/task/cancelar/'.$task['id'], 'Cancelar', array('class' => 'dropdown-item')); ?>
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
