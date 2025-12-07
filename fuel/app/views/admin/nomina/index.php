<?php
/**
 * Vista: Listado de Períodos de Nómina
 */
?>
<div class="container-fluid">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h2><i class="fa fa-money-bill-wave"></i> Nómina</h2>
		<?php if (Auth::has_access('nomina.create')): ?>
			<a href="<?php echo Uri::create('admin/nomina/create'); ?>" class="btn btn-primary">
				<i class="fa fa-plus"></i> Nuevo Período
			</a>
		<?php endif; ?>
	</div>

	<!-- Filtros -->
	<div class="card mb-4">
		<div class="card-body">
			<form method="get" class="row g-3">
				<div class="col-md-3">
					<label>Año</label>
					<select name="year" class="form-control">
						<option value="">Todos los años</option>
						<?php for($y = date('Y'); $y >= 2020; $y--): ?>
							<option value="<?php echo $y; ?>"><?php echo $y; ?></option>
						<?php endfor; ?>
					</select>
				</div>
				<div class="col-md-3">
					<label>Tipo</label>
					<select name="type" class="form-control">
						<option value="">Todos los tipos</option>
						<option value="monthly">Mensual</option>
						<option value="biweekly">Quincenal</option>
						<option value="weekly">Semanal</option>
					</select>
				</div>
				<div class="col-md-3">
					<label>Estado</label>
					<select name="status" class="form-control">
						<option value="">Todos los estados</option>
						<option value="draft">Borrador</option>
						<option value="calculated">Calculada</option>
						<option value="approved">Aprobada</option>
						<option value="paid">Pagada</option>
						<option value="closed">Cerrada</option>
					</select>
				</div>
				<div class="col-md-3">
					<label>&nbsp;</label>
					<button type="submit" class="btn btn-secondary d-block w-100">
						<i class="fa fa-filter"></i> Filtrar
					</button>
				</div>
			</form>
		</div>
	</div>

	<!-- Tabla de períodos -->
	<div class="card">
		<div class="card-body">
			<?php if (empty($periods)): ?>
				<div class="alert alert-info">
					<i class="fa fa-info-circle"></i> No hay períodos de nómina registrados.
					<?php if (Auth::has_access('nomina.create')): ?>
						<a href="<?php echo Uri::create('admin/nomina/create'); ?>">Crear el primero</a>
					<?php endif; ?>
				</div>
			<?php else: ?>
				<div class="table-responsive">
					<table class="table table-hover">
						<thead>
							<tr>
								<th>Código</th>
								<th>Nombre</th>
								<th>Tipo</th>
								<th>Período</th>
								<th>Fecha de Pago</th>
								<th>Empleados</th>
								<th class="text-end">Total Neto</th>
								<th>Estado</th>
								<th width="180">Acciones</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($periods as $period): ?>
								<tr>
									<td><strong><?php echo htmlspecialchars($period->code, ENT_QUOTES, 'UTF-8'); ?></strong></td>
									<td><?php echo htmlspecialchars($period->name, ENT_QUOTES, 'UTF-8'); ?></td>
									<td><?php echo htmlspecialchars($period->get_period_type_label(), ENT_QUOTES, 'UTF-8'); ?></td>
									<td>
										<small class="text-muted">
											<?php echo date('d/m/Y', strtotime($period->start_date)); ?> - 
											<?php echo date('d/m/Y', strtotime($period->end_date)); ?>
										</small>
									</td>
									<td><?php echo date('d/m/Y', strtotime($period->payment_date)); ?></td>
									<td><span class="badge bg-info"><?php echo $period->total_employees; ?></span></td>
									<td class="text-end">
										<strong>$<?php echo number_format($period->total_net, 2); ?></strong>
									</td>
									<td><?php echo $period->get_status_badge(); ?></td>
									<td>
										<div class="btn-group btn-group-sm">
											<a href="<?php echo Uri::create('admin/nomina/view/' . $period->id); ?>" 
											   class="btn btn-info" title="Ver detalle">
												<i class="fa fa-eye"></i>
											</a>
											
											<?php if ($period->can_calculate() && Auth::has_access('nomina.calculate')): ?>
												<a href="<?php echo Uri::create('admin/nomina/calculate/' . $period->id); ?>" 
												   class="btn btn-primary" title="Calcular">
													<i class="fa fa-calculator"></i>
												</a>
											<?php endif; ?>
											
											<?php if ($period->is_editable() && Auth::has_access('nomina.edit')): ?>
												<a href="<?php echo Uri::create('admin/nomina/edit/' . $period->id); ?>" 
												   class="btn btn-warning" title="Editar">
													<i class="fa fa-edit"></i>
												</a>
											<?php endif; ?>
											
											<?php if ($period->can_approve() && Auth::has_access('nomina.approve')): ?>
												<a href="<?php echo Uri::create('admin/nomina/approve/' . $period->id); ?>" 
												   class="btn btn-success" title="Aprobar">
													<i class="fa fa-check"></i>
												</a>
											<?php endif; ?>
											
											<?php if (in_array($period->status, ['approved', 'paid']) && Auth::has_access('nomina.export')): ?>
												<a href="<?php echo Uri::create('admin/nomina/export/' . $period->id); ?>" 
												   class="btn btn-secondary" title="Exportar">
													<i class="fa fa-download"></i>
												</a>
											<?php endif; ?>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				<!-- Paginación -->
				<?php echo $pagination->render(); ?>
			<?php endif; ?>
		</div>
	</div>
</div>
