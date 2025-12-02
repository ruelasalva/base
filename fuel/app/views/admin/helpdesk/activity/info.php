<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Reportes diarios</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/helpdesk/activity', 'Reportes CRM'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/helpdesk/activity/info/'.$act_num, 'Ver'); ?>
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
	<div class="row">
		<div class="col">
			<div class="card-wrapper">
				<div class="card">
					<?php echo Form::open(['method' => 'post', 'id' => 'activity-form']); ?>
					<div class="card-header d-flex justify-content-between align-items-center">
						<h3 class="mb-0">Ver actividades para <strong>CRM Num: <?php echo $act_num; ?></strong></h3>
						<div class="form-group mb-0 d-flex align-items-center">
							<label for="global-date" class="form-control-label mr-2">Fecha:</label>
							<?php if($completed == 0): ?>
								<?php echo Form::input('global_date', $global_date, array(
									'id' => 'global_date',
									'class' => 'form-control datepicker'
								)); ?>
							<?php else: ?>
								<label class="form-control-label mr-2"><?php echo $global_date; ?></label>
							<?php endif; ?>
						</div>
					</div>
					<!-- CARD FOOTER -->
					<!-- REGISTROS -->
					<div class="card-footer">
						<h4 class="mb-3 text-primary font-weight-bold">Actividades registradas</h4>
						<div class="table-responsive">
							<table class="table table-bordered table-hover align-middle text-center">
								<thead class="thead-light">
									<tr>
										<th>Cliente</th>
										<th>Factura</th>
										<th>Razon Social</th>
										<th>Foraneo</th>
										<th>Medio</th>
										<th>Hora</th>
										<th>Duracion <br> de llamada</th>
										<th>Entrante<br>Saliente</th>
										<th>Estatus</th>
										<th>Productos <br> de interes</th>
										<th>Minuta</th>
										<th>Total <br>sin IVA</th>
									</tr>
								</thead>
								<tbody id="activities-table-body">
									<?php if(!empty($activities)): ?>
										<?php foreach($activities as $activity): ?>
											<tr>
												<td><?php echo $activity['customer']; ?></td>
												<td><?php echo $activity['invoice']; ?></td>
												<td><?php echo $activity['company']; ?></td>
												<td><?php echo $activity['foreing']; ?></td>
												<td><?php echo $activity['contact']; ?></td>
												<td><?php echo $activity['hour']; ?></td>
												<td><?php echo $activity['time']; ?></td>
												<td><?php echo $activity['type']; ?></td>
												<td><?php echo $activity['status']; ?></td>
												<td><?php echo $activity['category']; ?></td>
												<td><?php echo $activity['comments']; ?></td>
												<td><?php echo $activity['total']; ?></td>
											</tr>
										<?php endforeach; ?>
									<?php endif; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
