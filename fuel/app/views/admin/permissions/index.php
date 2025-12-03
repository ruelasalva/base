<div class="animated fadeIn">
	<!-- Estadísticas -->
	<div class="row mb-3">
		<div class="col-sm-6 col-lg-3">
			<div class="card text-white bg-primary">
				<div class="card-body pb-0">
					<div class="text-value"><?php echo $stats['total']; ?></div>
					<div>Total Permisos</div>
				</div>
				<div class="chart-wrapper px-3" style="height:70px;">
					<canvas id="card-chart1" class="chart" height="70"></canvas>
				</div>
			</div>
		</div>

		<div class="col-sm-6 col-lg-3">
			<div class="card text-white bg-success">
				<div class="card-body pb-0">
					<div class="text-value"><?php echo $stats['active']; ?></div>
					<div>Activos</div>
				</div>
				<div class="chart-wrapper px-3" style="height:70px;">
					<canvas id="card-chart2" class="chart" height="70"></canvas>
				</div>
			</div>
		</div>

		<div class="col-sm-6 col-lg-3">
			<div class="card text-white bg-info">
				<div class="card-body pb-0">
					<div class="text-value"><?php echo $stats['modules']; ?></div>
					<div>Módulos</div>
				</div>
				<div class="chart-wrapper px-3" style="height:70px;">
					<canvas id="card-chart3" class="chart" height="70"></canvas>
				</div>
			</div>
		</div>

		<div class="col-sm-6 col-lg-3">
			<div class="card text-white bg-warning">
				<div class="card-body pb-0">
					<div class="text-value"><?php echo $stats['total'] - $stats['active']; ?></div>
					<div>Inactivos</div>
				</div>
				<div class="chart-wrapper px-3" style="height:70px;">
					<canvas id="card-chart4" class="chart" height="70"></canvas>
				</div>
			</div>
		</div>
	</div>

	<!-- Lista de Permisos -->
	<div class="row">
		<div class="col-lg-12">
			<div class="card">
				<div class="card-header">
					<i class="fa fa-key"></i> <?php echo $title; ?>
					<?php if ($can_create): ?>
						<a href="<?php echo Uri::create('admin/permissions/new'); ?>" class="btn btn-success btn-sm float-right">
							<i class="fa fa-plus"></i> Nuevo Permiso
						</a>
					<?php endif; ?>
				</div>
				<div class="card-body">
					<?php if (empty($permissions_by_module)): ?>
						<div class="alert alert-info">
							<i class="fa fa-info-circle"></i> No hay permisos registrados en el sistema.
							<?php if ($can_create): ?>
								<a href="<?php echo Uri::create('admin/permissions/new'); ?>">Crear el primer permiso</a>
							<?php endif; ?>
						</div>
					<?php else: ?>
						<!-- Filtros -->
						<div class="form-group">
							<input type="text" id="searchPermissions" class="form-control" placeholder="Buscar permisos por módulo, acción o nombre...">
						</div>

						<!-- Accordion de Módulos -->
						<div class="accordion" id="permissionsAccordion">
							<?php $index = 0; foreach ($permissions_by_module as $module => $permissions): ?>
								<div class="card mb-2 module-card" data-module="<?php echo strtolower($module); ?>">
									<div class="card-header p-2 bg-light" id="heading<?php echo $index; ?>">
										<h6 class="mb-0">
											<button class="btn btn-link btn-block text-left d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapse<?php echo $index; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>">
												<span>
													<i class="fa fa-folder-open text-primary"></i> 
													<strong><?php echo ucfirst($module); ?></strong>
												</span>
												<span class="badge badge-primary badge-pill"><?php echo count($permissions); ?></span>
											</button>
										</h6>
									</div>
									<div id="collapse<?php echo $index; ?>" class="collapse <?php echo $index === 0 ? 'show' : ''; ?>" aria-labelledby="heading<?php echo $index; ?>" data-parent="#permissionsAccordion">
										<div class="card-body p-0">
											<table class="table table-hover table-sm mb-0">
												<thead class="bg-light">
													<tr>
														<th width="15%">Acción</th>
														<th width="25%">Nombre</th>
														<th width="40%">Descripción</th>
														<th width="10%" class="text-center">Estado</th>
														<th width="10%" class="text-right">Acciones</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach ($permissions as $perm): ?>
														<tr class="permission-row" data-name="<?php echo strtolower($perm['name']); ?>" data-action="<?php echo strtolower($perm['action']); ?>">
															<td>
																<code><?php echo $perm['action']; ?></code>
															</td>
															<td>
																<strong><?php echo $perm['name']; ?></strong>
															</td>
															<td>
																<small class="text-muted"><?php echo $perm['description'] ?: '-'; ?></small>
															</td>
															<td class="text-center">
																<?php if ($perm['is_active']): ?>
																	<span class="badge badge-success" onclick="togglePermission(<?php echo $perm['id']; ?>)" style="cursor: pointer;">
																		<i class="fa fa-check"></i> Activo
																	</span>
																<?php else: ?>
																	<span class="badge badge-secondary" onclick="togglePermission(<?php echo $perm['id']; ?>)" style="cursor: pointer;">
																		<i class="fa fa-ban"></i> Inactivo
																	</span>
																<?php endif; ?>
															</td>
															<td class="text-right">
																<div class="btn-group btn-group-sm">
																	<a href="<?php echo Uri::create('admin/permissions/view/' . $perm['id']); ?>" class="btn btn-info" title="Ver">
																		<i class="fa fa-eye"></i>
																	</a>
																	<?php if ($can_edit): ?>
																		<a href="<?php echo Uri::create('admin/permissions/edit/' . $perm['id']); ?>" class="btn btn-primary" title="Editar">
																			<i class="fa fa-edit"></i>
																		</a>
																	<?php endif; ?>
																	<?php if ($can_delete): ?>
																		<button onclick="deletePermission(<?php echo $perm['id']; ?>)" class="btn btn-danger" title="Eliminar">
																			<i class="fa fa-trash"></i>
																		</button>
																	<?php endif; ?>
																</div>
															</td>
														</tr>
													<?php endforeach; ?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							<?php $index++; endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
// Búsqueda en tiempo real
document.getElementById('searchPermissions').addEventListener('input', function() {
	var searchTerm = this.value.toLowerCase();
	var moduleCards = document.querySelectorAll('.module-card');
	var found = false;

	moduleCards.forEach(function(card) {
		var module = card.dataset.module;
		var rows = card.querySelectorAll('.permission-row');
		var hasVisibleRows = false;

		rows.forEach(function(row) {
			var name = row.dataset.name;
			var action = row.dataset.action;
			
			if (module.includes(searchTerm) || name.includes(searchTerm) || action.includes(searchTerm)) {
				row.style.display = '';
				hasVisibleRows = true;
				found = true;
			} else {
				row.style.display = 'none';
			}
		});

		// Mostrar/ocultar módulo completo
		if (hasVisibleRows) {
			card.style.display = '';
			// Expandir si hay búsqueda
			if (searchTerm) {
				$(card.querySelector('.collapse')).collapse('show');
			}
		} else {
			card.style.display = 'none';
		}
	});

	// Si no hay búsqueda, colapsar todos excepto el primero
	if (!searchTerm) {
		$('.collapse').each(function(index) {
			if (index === 0) {
				$(this).collapse('show');
			} else {
				$(this).collapse('hide');
			}
		});
	}
});

// Toggle estado de permiso
function togglePermission(id) {
	<?php if (!$can_edit): ?>
		alert('No tienes permisos para modificar permisos');
		return;
	<?php endif; ?>

	if (confirm('¿Cambiar el estado de este permiso?')) {
		fetch('<?php echo Uri::create("admin/permissions/toggle"); ?>/' + id, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-Requested-With': 'XMLHttpRequest'
			}
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				location.reload();
			} else {
				alert('Error: ' + data.message);
			}
		})
		.catch(error => {
			console.error('Error:', error);
			alert('Error al cambiar el estado');
		});
	}
}

// Eliminar permiso
function deletePermission(id) {
	if (confirm('¿Estás seguro de eliminar este permiso?\n\nEsta acción no se puede deshacer y puede afectar a los roles que lo tienen asignado.')) {
		fetch('<?php echo Uri::create("admin/permissions/delete"); ?>/' + id, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-Requested-With': 'XMLHttpRequest'
			}
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				alert(data.message);
				location.reload();
			} else {
				alert('Error: ' + data.message);
			}
		})
		.catch(error => {
			console.error('Error:', error);
			alert('Error al eliminar el permiso');
		});
	}
}
</script>
