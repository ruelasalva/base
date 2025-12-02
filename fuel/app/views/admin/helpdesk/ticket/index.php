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
					<?php echo Html::anchor('admin/helpdesk/ticket/graficas', 'Graficas', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php echo Html::anchor('admin/helpdesk/csv/tickets?r1='.$start_date_unix.'&r2='.$end_date_unix, 'Exportar CSV', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php echo Html::anchor('admin/helpdesk/ticket/agregar', 'Nuevo', array('class' => 'btn btn-sm btn-neutral')); ?>
					<br>
                    	<h6 class="h4 text-white d-inline-block mb-0"></h6><p class="h6 text-white" id="last-update"></p><p class="h6 text-white" id="next-update"></p>
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
					<?php echo Form::open(array('action' => 'admin/helpdesk/ticket/buscar', 'method' => 'post')); ?>
					<div class="form-row row align-items-start">
						<div class="col-md-5">
							<h3 class="mb-0">Lista de Tickets</h3>
						</div>
						<div class="col-md-2 mb-0">
							<div class="input-group input-group-sm mt-3 mt-md-0">
								<?php echo Form::input('search', (isset($search) ? $search : ''), array('id' => 'search', 'class' => 'form-control', 'placeholder' => 'Descripción, Solicitante y Usuario')); ?>
							</div>
						</div>
						<div class="col-md-2 mb-0">
							<div class="input-group input-group-sm mt-3 mt-md-0">
								<?php echo Form::input('start_date', (isset($start_date) ? $start_date : ''), array('id' => 'start_date', 'class' => 'form-control datepicker', 'placeholder' => 'Fecha inicio')); ?>
							</div>
						</div>
						<div class="col-md-2 mb-0">
							<div class="input-group input-group-sm mt-3 mt-md-0">
								<?php echo Form::input('end_date', (isset($end_date) ? $end_date : ''), array('id' => 'end_date', 'class' => 'form-control datepicker', 'placeholder' => 'Fecha final')); ?>
							</div>
						</div>
						<div class="col-md-1 mb-0">
							<div class="input-group input-group-sm mt-3 mt-md-0">
								<?php echo Form::submit(array('value'=> 'Buscar', 'name'=>'submit', 'class' => 'btn btn-outline-primary btn-sm btn-block')); ?>
							</div>
						</div>
					</div>
					<?php echo Form::close(); ?>
					<div class="form-row mt-4">
						<div class="col-md-6">
							<div class="form-group ">
								<?php echo Form::label('Personal de soporte asignado', 'asig_user_id'); ?>
								<?php echo Form::select('asig_user_id', Input::post('asig_user_id', isset($asig_user_id) ? $asig_user_id : 'none'), $asig_user_opts, array('id' => 'asig_user_id', 'data-toggle' => 'select')); ?>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group ">
								<?php echo Form::label('Estatus del Ticket', 'status_id'); ?>
								<?php echo Form::select('status_id', Input::post('status_id', isset($status_id) ? $status_id : 'none'), $statusticket_opts, array('id' => 'istatus_id','data-toggle' => 'select')); ?>
							</div>
						</div>
					</div>
				</div>

				<!-- LIGHT TABLE -->
				<div class="table-responsive" data-toggle="lists" data-list-values='["id", "asig_user_id", "user_id", "department_id", "type_id", "incident_id", "description", "start", " finish", "created_at", "updated_at", "time_to_solve","priority_id", "status_id", "rating"]'>
					<table class="table align-items-center table-flush">
						<thead class="thead-light">
							<tr>
								<th scope="col" class="sort" data-sort="id">ID</th>
								<th scope="col" class="sort" data-sort="asig_user_id">Usuario <br>asignado</th>
								<th scope="col" class="sort" data-sort="user_id">Solicitante</th>
								<th scope="col" class="sort" data-sort="department_id">Departamento <br>que Solicita</th>
								<th scope="col" class="sort" data-sort="type_id">Tipo<br> de Ticket</th>
								<th scope="col" class="sort" data-sort="incident_id">Tipo <br>de incidencia</th>
								<th scope="col" class="sort" data-sort="description">Descripcion <br>detallada</th>
								<th scope="col" class="sort" data-sort="created_at">Fecha Creación</th>
								<th scope="col" class="sort" data-sort="start">Inicio de ticket</th>
								<th scope="col" class="sort" data-sort="finish">Finalizacón<br> de ticket</th>
								<th scope="col" class="sort" data-sort="time_to_solve">Duración</th>
								<th scope="col" class="sort" data-sort="priority_id">Prioridad</th>
								<th scope="col" class="sort" data-sort="status_id">Status</th>
								<th scope="col" class="sort" data-sort="rating">Calificación</th>
								<th scope="col"></th>
							</tr>
						</thead>
						<tbody class="list">
							<?php if(!empty($tickets)): ?>
								<?php foreach($tickets as $ticket): ?>
									<?php $statusClass = '';
									switch ($ticket['status_id']) {
										case 'Asignado':
										$statusClass = 'text-primary';
										break;
										case 'Atendiendo':
										$statusClass = 'text-info';
										break;
										case 'Finalizado':
										$statusClass = 'text-success';
										break;
										case 'Cancelado':
										$statusClass = 'bg-danger text-white';
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
										<th class="user_id">
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
											<?php echo date('d/m/Y', $ticket['created_at']); ?><br><?php echo date('H:i', $ticket['created_at']); ?>
										</th>
										<th class="start">
											<?php if ($ticket['start'] !== null): ?>
												<?php echo date('d/m/Y', $ticket['start']); ?><br><?php echo date('H:i', $ticket['start']); ?>
											<?php endif; ?>
										</th>
										<th class="finish">
											<?php if ($ticket['finish'] !== null): ?>
												<?php echo date('d/m/Y', $ticket['finish']); ?><br><?php echo date('H:i', $ticket['finish']); ?>
											<?php endif; ?>
										</th>
										<th class="time_to_solve">
											<?php echo $ticket['time_to_solve']; ?>
										</th>
										<th class="<?php echo ($ticket['status_id'] == 'Finalizado') ? $statusClass : (($ticket['priority_id'] == 'Urgente') ? 'text-danger' : ''); ?>">
											<?php echo $ticket['priority_id']; ?>
										</th>
										<th class="status_id">
											<?php echo $ticket['status_id']; ?>
										</th>
										<th class="rating">
											<?php if($ticket['rating'] == 1 || $ticket['rating'] == 2): ?>
												<?php echo ($ticket['rating'] == 1) ? 'Buena' : 'Mala'; ?>
											<?php else: ?>
												<?php echo 'N/A'; ?>
											<?php endif; ?>
										</th>
										<td class="text-right">
											<div class="dropdown">
												<a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													<i class="fas fa-ellipsis-v"></i>
												</a>
												<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
													<?php echo Html::anchor('admin/helpdesk/ticket/info/'.$ticket['id'], 'Ver', array('class' => 'dropdown-item')); ?>
													<?php echo Html::anchor('admin/helpdesk/ticket/editar/'.$ticket['id'], 'Editar', array('class' => 'dropdown-item')); ?>
													<?php echo Html::anchor('admin/helpdesk/ticket/asignar/'.$ticket['id'], 'Asignar', array('class' => 'dropdown-item ticket-asig', 'data-ticket' => $ticket['id'])); ?>
													<?php echo Html::anchor('admin/helpdesk/ticket/iniciar/'.$ticket['id'], 'Iniciar', array('class' => 'dropdown-item')); ?>
													<?php echo Html::anchor('admin/helpdesk/ticket/cerrar/'.$ticket['id'], 'Finalizar', array('class' => 'dropdown-item ticket-clos', 'data-ticket' => $ticket['id'])); ?>
													<?php echo Html::anchor('admin/helpdesk/ticket/finalizar/'.$ticket['id'], 'Cerrar', array('class' => 'dropdown-item ticket-modal', 'data-ticket' => $ticket['id'])); ?>
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
<script>
  function mostrarProximaActualizacion() {
    var nextUpdateElement = document.getElementById('next-update');
    var lastUpdateElement = document.getElementById('last-update');

    var currentDateTime = moment();
    var formattedCurrentDateTime = currentDateTime.format('DD-MM-YYYY h:mm:ss A');
   
    lastUpdateElement.textContent = 'Última actualización: ' + formattedCurrentDateTime;
   
    var nextUpdateDateTime = moment().add(5, 'minutes');
    var formattedNextUpdate = nextUpdateDateTime.format('DD-MM-YYYY h:mm:ss A');
    nextUpdateElement.textContent = 'Próxima actualización: ' + formattedNextUpdate;
   
    var secondsUntilNextUpdate = nextUpdateDateTime.diff(moment(), 'seconds');
 
    setTimeout(function() {
      location.reload(); 
    }, secondsUntilNextUpdate * 1000); 
  }
  window.addEventListener('DOMContentLoaded', mostrarProximaActualizacion);
</script>
