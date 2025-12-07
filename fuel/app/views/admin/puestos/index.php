<div class="container-fluid">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div>
			<h2 class="mb-1"><i class="fa fa-user-tag me-2"></i>Puestos</h2>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb mb-0">
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin'); ?>">Inicio</a></li>
					<li class="breadcrumb-item active">Puestos</li>
				</ol>
			</nav>
		</div>
		<?php if (Helper_Permission::can('puestos', 'create')): ?>
		<div>
			<a href="<?php echo Uri::create('admin/puestos/create'); ?>" class="btn btn-primary">
				<i class="fa fa-plus me-2"></i>Nuevo Puesto
			</a>
		</div>
		<?php endif; ?>
	</div>

	<div class="card mb-4">
		<div class="card-body">
			<form method="GET" class="row g-3">
				<div class="col-md-10">
					<input type="text" name="search" class="form-control" placeholder="Buscar puesto..." value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
				</div>
				<div class="col-md-2">
					<button type="submit" class="btn btn-primary w-100">
						<i class="fa fa-search"></i> Buscar
					</button>
				</div>
			</form>
		</div>
	</div>

	<div class="card">
		<div class="card-header">
			<i class="fa fa-list me-2"></i>Listado de Puestos
		</div>
		<div class="card-body p-0">
			<div class="table-responsive">
				<table class="table table-hover table-striped mb-0">
					<thead class="table-light">
						<tr>
							<th width="80">Código</th>
							<th>Nombre</th>
							<th>Rango Salarial</th>
							<th>Empleados</th>
							<th width="100">Estatus</th>
							<th width="150" class="text-end">Acciones</th>
						</tr>
					</thead>
					<tbody>
						<?php if (count($positions) > 0): ?>
							<?php foreach ($positions as $pos): ?>
								<tr>
									<td><code><?php echo htmlspecialchars($pos->code ?: '-', ENT_QUOTES, 'UTF-8'); ?></code></td>
									<td><strong><?php echo htmlspecialchars($pos->name, ENT_QUOTES, 'UTF-8'); ?></strong></td>
									<td class="text-muted"><?php echo $pos->get_salary_range(); ?></td>
									<td>
										<span class="badge bg-info"><?php echo $pos->count_active_employees(); ?> empleados</span>
									</td>
									<td><?php echo $pos->get_status_badge(); ?></td>
									<td class="text-end">
										<div class="btn-group btn-group-sm">
											<?php if (Helper_Permission::can('puestos', 'edit')): ?>
												<a href="<?php echo Uri::create('admin/puestos/edit/' . $pos->id); ?>" class="btn btn-warning" title="Editar">
													<i class="fa fa-edit"></i>
												</a>
											<?php endif; ?>
											<?php if (Helper_Permission::can('puestos', 'delete')): ?>
												<button type="button" class="btn btn-danger" onclick="deleteItem(<?php echo $pos->id; ?>, 'puestos')" title="Eliminar">
													<i class="fa fa-trash"></i>
												</button>
											<?php endif; ?>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php else: ?>
							<tr>
								<td colspan="6" class="text-center py-4 text-muted">
									<i class="fa fa-info-circle me-2"></i>No se encontraron puestos
								</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<script>
function deleteItem(id, module) {
	Swal.fire({
		title: '¿Estás seguro?',
		text: 'Esta acción no se puede deshacer',
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#d33',
		cancelButtonColor: '#3085d6',
		confirmButtonText: 'Sí, eliminar',
		cancelButtonText: 'Cancelar'
	}).then((result) => {
		if (result.isConfirmed) {
			window.location.href = '<?php echo Uri::create("admin/"); ?>' + module + '/delete/' + id;
		}
	});
}
</script>
