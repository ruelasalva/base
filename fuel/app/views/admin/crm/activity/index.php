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
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/crm/activity', 'Reportes CRM'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/crm/activity/agregar', 'Nuevo', array('class' => 'btn btn-sm btn-neutral')); ?>
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
					<?php echo Form::open(array('action' => 'admin/crm/activity/buscar', 'method' => 'post')); ?>
					<div class="form-row">
						<div class="col-md-9">
							<h3 class="mb-0">Reportes CRM</h3>
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
                    <div class="table-responsive">
						<table class="table align-items-center table-flush">
							<thead class="thead-light">
								<tr>
									<th>ID</th>
									<th>Fecha</th>
									<th>Agente</th>
									<th>Atendidos</th>
									<th>Iniciación</th>
									<th>Seguimiento</th>
									<th>Venta</th>
									<th>Facturados</th>
									<th>Monto</th>
								</tr>
							</thead>
							<tbody>
								<?php if (!empty($activities)): ?>
									<?php foreach ($activities as $activity): ?>
										<tr>
											<td><?php echo Html::anchor('admin/crm/activity/editar/'.$activity['act_num'], $activity['act_num']); ?></td>
											<td><?php echo $activity['global_date']; ?></td>
											<td><?php echo $activity['agent']; ?></td>
											<td><?php echo $activity['total_activities']; ?></td>
											<td><?php echo $activity['total_iniciacion']; ?></td>
											<td><?php echo $activity['total_seguimiento']; ?></td>
											<td><?php echo $activity['total_ventas']; ?></td>
											<td><?php echo $activity['total_facturados']; ?></td>
											<td>$ <?php echo $activity['total_monto']; ?></td>
										</tr>
									<?php endforeach; ?>
								<?php else: ?>
									<tr>
										<td colspan="9" class="text-center">No se encontraron actividades.</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>

<!-- CARD FOOTER -->
            </div>
        </div>
    </div>
</div>