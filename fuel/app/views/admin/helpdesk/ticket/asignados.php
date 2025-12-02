<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Tickets</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/helpdesk/ticket', 'Tickets'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/helpdesk/ticket/agregar', 'Nuevo', array('class' => 'btn btn-sm btn-neutral')); ?>
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
					<?php echo Form::open(array('action' => 'admin/helpdesk/ticket/buscar_asig', 'method' => 'post')); ?>
					<div class="form-row">
						<div class="col-md-9">
							<h3 class="mb-0">Mis Tickets asigandos</h3>
						</div>
						<div class="col-md-3 mb-0">
							<div class="input-group input-group-sm mt-3 mt-md-0">
								<?php echo Form::input('search', (isset($search) ? $search : ''), array('id' => 'search', 'class' => 'form-control', 'placeholder' => 'Descripción, Solcitante', 'aria-describedby' => 'button-addon')); ?>
								<div class="input-group-append">
									<?php echo Form::submit(array('value'=> 'Buscar', 'name'=>'submit', 'id' => 'button-addon', 'class' => 'btn btn-outline-primary')); ?>
								</div>
							</div>
						</div>
					</div>
					<?php echo Form::close(); ?>
				</div>    
<!-- LIGHT TABLE -->
				<div class="table-responsive" data-toggle="lists" data-list-values='["id", "type_id", "incident_id", "description", "status_id", "priority_id", "employee_id", "asig_user_id","department_id"]'>
					<table class="table align-items-center table-flush">
						<thead class="thead-light">
							<tr>
								<th scope="col" class="sort" data-sort="id">ID</th>
								<th scope="col" class="sort" data-sort="asig_user_id">Usuario asignado</th>
								<th scope="col" class="sort" data-sort="employee_id">Solicitante</th>
								<th scope="col" class="sort" data-sort="department_id">Departamento</th>
								<th scope="col" class="sort" data-sort="type_id">Tipo de Ticket</th>
								<th scope="col" class="sort" data-sort="incident_id">Tipo de incidencia</th>
                                <th scope="col" class="sort" data-sort="description">Descripción detallada</th>
								<th scope="col" class="sort" data-sort="created_at">Fecha Creación</th>
                                <th scope="col" class="sort" data-sort="updated_at">Fecha ultima modificación</th>
                                <th scope="col" class="sort" data-sort="priority_id">Prioridad</th>
								<th scope="col" class="sort" data-sort="status_id">Status</th>
								<th scope="col"></th>
							</tr>
						</thead>
						<tbody class="list ">
							<!--<span id="opts" data-opts="<?php //echo $asig_user_modal; ?>"></span>-->
							<?php if(!empty($tickets)): ?>
								<?php foreach($tickets as $ticket): ?>
									<?php $statusClass = '';
											switch ($ticket['status_id']) {
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
											<?php echo Html::anchor('admin/helpdesk/ticket/info/'.$ticket['id'], $ticket['id']); ?>
										</th>
										<th class="asig_user_id">
											<?php echo $ticket['asig_user_id']; ?>
										</th>
										<th class="employee_id">
											<?php echo $ticket['employee_id']; ?>
										</th>
										<th class="department_id">
											<?php echo $ticket['department_id']; ?>
										</th>
                                        <th class="type_id">
											<?php echo $ticket['type_id']; ?>
										</th>
										<th class="incident_id">
											<?php echo  $ticket['incident_id']; ?>
										</th>
                                        <th class="description">
											<?php echo substr($ticket['description'], 0, 40);  ?>
										</th>
										<th class="created_at">
											<?php echo $ticket['created_at']; ?>
										</th>
										<th class="updated_at">
											<?php if ($ticket['updated_at'] !== null): ?>
												<?php echo date('d/m/Y - H:i', $ticket['updated_at']); ?>
											<?php endif; ?>
										</th>
                                        <th class="<?php echo ($ticket['status_id'] == 'Finalizado') ? $statusClass : (($ticket['priority_id'] == 'Urgente') ? 'text-danger' : ''); ?>">
											<?php echo $ticket['priority_id']; ?>
										</th>
										 <th class="status_id">
											<?php echo $ticket['status_id']; ?>
										</th>
										<td class="text-right">
											<div class="dropdown">
												<a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													<i class="fas fa-ellipsis-v"></i>
												</a>
												<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
													<?php echo Html::anchor('admin/helpdesk/ticket/info/'.$ticket['id'], 'Ver', array('class' => 'dropdown-item')); ?>
													<?php echo Html::anchor('admin/helpdesk/ticket/editar/'.$ticket['id'], 'Editar', array('class' => 'dropdown-item')); ?>
													<?php echo Html::anchor('admin/helpdesk/ticket/finalizar/'.$ticket['id'], 'Finalizar', array('class' => 'dropdown-item ticket-modal', 'data-ticket' => $ticket['id'])); ?>
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
