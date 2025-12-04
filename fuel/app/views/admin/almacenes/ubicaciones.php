<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Ubicaciones - <?= $almacen['name'] ?> | Sistema ERP</title>
	<link rel="stylesheet" href="<?= Uri::base() ?>assets/plugins/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?= Uri::base() ?>assets/plugins/fontawesome/css/all.min.css">
	<link rel="stylesheet" href="<?= Uri::base() ?>assets/plugins/datatables/datatables.min.css">
	<link rel="stylesheet" href="<?= Uri::base() ?>assets/plugins/sweetalert2/sweetalert2.min.css">
	<link rel="stylesheet" href="<?= Uri::base() ?>assets/css/style.css">
</head>
<body>
	<div class="wrapper">
		<?php echo View::forge('layouts/sidebar'); ?>
		
		<div class="main">
			<?php echo View::forge('layouts/topbar'); ?>
			
			<main class="content">
				<div class="container-fluid p-0">
					<div class="mb-3">
						<h1 class="h3 d-inline align-middle">Ubicaciones - <?= $almacen['name'] ?></h1>
						<div class="float-end">
							<a href="<?= Uri::create('admin/almacenes') ?>" class="btn btn-secondary">
								<i class="fas fa-arrow-left"></i> Volver
							</a>
							<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearUbicacion">
								<i class="fas fa-plus"></i> Nueva Ubicación
							</button>
						</div>
					</div>

					<?php if (Session::get_flash('success')): ?>
						<div class="alert alert-success alert-dismissible fade show" role="alert">
							<i class="fas fa-check-circle me-2"></i>
							<?= Session::get_flash('success') ?>
							<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
						</div>
					<?php endif; ?>

					<?php if (Session::get_flash('error')): ?>
						<div class="alert alert-danger alert-dismissible fade show" role="alert">
							<i class="fas fa-exclamation-triangle me-2"></i>
							<?= Session::get_flash('error') ?>
							<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
						</div>
					<?php endif; ?>

					<!-- Info del almacén -->
					<div class="row mb-3">
						<div class="col-md-3">
							<div class="card">
								<div class="card-body">
									<h6 class="text-muted">Código</h6>
									<h4><?= $almacen['code'] ?></h4>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="card">
								<div class="card-body">
									<h6 class="text-muted">Tipo</h6>
									<h4><?= ucfirst($almacen['type']) ?></h4>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="card">
								<div class="card-body">
									<h6 class="text-muted">Ubicaciones</h6>
									<h4><?= count($ubicaciones) ?></h4>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="card">
								<div class="card-body">
									<h6 class="text-muted">Capacidad Total</h6>
									<h4><?= number_format($almacen['capacity_units'] ?? 0) ?> unidades</h4>
								</div>
							</div>
						</div>
					</div>

					<!-- Tabla de ubicaciones -->
					<div class="card">
						<div class="card-body">
							<table id="tableUbicaciones" class="table table-hover table-striped">
								<thead>
									<tr>
										<th>Código</th>
										<th>Nombre</th>
										<th>Tipo</th>
										<th>Pasillo</th>
										<th>Sección</th>
										<th>Nivel</th>
										<th>Capacidad</th>
										<th>Estado</th>
										<th>Acciones</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($ubicaciones as $ub): ?>
									<tr>
										<td><strong><?= $ub['code'] ?></strong></td>
										<td><?= $ub['name'] ?></td>
										<td>
											<span class="badge bg-info"><?= ucfirst($ub['type']) ?></span>
										</td>
										<td><?= $ub['aisle'] ?? '-' ?></td>
										<td><?= $ub['section'] ?? '-' ?></td>
										<td><?= $ub['level'] ?? '-' ?></td>
										<td><?= number_format($ub['capacity_units'] ?? 0) ?></td>
										<td>
											<?php if ($ub['is_active']): ?>
												<span class="badge bg-success">Activo</span>
											<?php else: ?>
												<span class="badge bg-danger">Inactivo</span>
											<?php endif; ?>
										</td>
										<td>
											<button type="button" class="btn btn-sm btn-primary" onclick="editarUbicacion(<?= $ub['id'] ?>)">
												<i class="fas fa-edit"></i>
											</button>
											<button type="button" class="btn btn-sm btn-danger" onclick="eliminarUbicacion(<?= $ub['id'] ?>, '<?= $ub['code'] ?>')">
												<i class="fas fa-trash"></i>
											</button>
										</td>
									</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</main>
		</div>
	</div>

	<!-- Modal Crear Ubicación -->
	<div class="modal fade" id="modalCrearUbicacion" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<form id="formCrearUbicacion" method="POST" action="<?= Uri::create('admin/almacenes/ubicaciones/' . $almacen['id']) ?>">
					<input type="hidden" name="<?= Config::get('security.csrf_token_key') ?>" value="<?= Security::fetch_token() ?>">
					<div class="modal-header">
						<h5 class="modal-title">Nueva Ubicación</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
					</div>
					<div class="modal-body">
						<div class="mb-3">
							<label class="form-label">Código <span class="text-danger">*</span></label>
							<input type="text" class="form-control" name="code" id="code" required maxlength="20">
							<small class="text-muted">Ej: A-01-01 (Pasillo-Sección-Nivel)</small>
						</div>
						<div class="mb-3">
							<label class="form-label">Nombre <span class="text-danger">*</span></label>
							<input type="text" class="form-control" name="name" id="name" required maxlength="100">
						</div>
						<div class="mb-3">
							<label class="form-label">Tipo <span class="text-danger">*</span></label>
							<select class="form-control" name="type" required>
								<option value="">Seleccionar...</option>
								<option value="estante">Estante</option>
								<option value="rack">Rack</option>
								<option value="piso">Piso</option>
								<option value="tarima">Tarima</option>
								<option value="refrigerador">Refrigerador</option>
								<option value="otro">Otro</option>
							</select>
						</div>
						<div class="row">
							<div class="col-md-4">
								<div class="mb-3">
									<label class="form-label">Pasillo</label>
									<input type="text" class="form-control" name="aisle" maxlength="10">
								</div>
							</div>
							<div class="col-md-4">
								<div class="mb-3">
									<label class="form-label">Sección</label>
									<input type="text" class="form-control" name="section" maxlength="10">
								</div>
							</div>
							<div class="col-md-4">
								<div class="mb-3">
									<label class="form-label">Nivel</label>
									<input type="text" class="form-control" name="level" maxlength="10">
								</div>
							</div>
						</div>
						<div class="mb-3">
							<label class="form-label">Capacidad (unidades)</label>
							<input type="number" class="form-control" name="capacity_units" min="0" step="1">
						</div>
						<div class="mb-3">
							<label class="form-label">Notas</label>
							<textarea class="form-control" name="notes" rows="3"></textarea>
						</div>
						<div class="mb-3">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" name="is_active" value="1" checked id="is_active">
								<label class="form-check-label" for="is_active">
									Activo
								</label>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
						<button type="submit" class="btn btn-primary">
							<i class="fas fa-save"></i> Guardar
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Modal Editar Ubicación -->
	<div class="modal fade" id="modalEditarUbicacion" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<form id="formEditarUbicacion" method="POST">
					<input type="hidden" name="<?= Config::get('security.csrf_token_key') ?>" value="<?= Security::fetch_token() ?>">
					<input type="hidden" name="ubicacion_id" id="edit_ubicacion_id">
					<div class="modal-header">
						<h5 class="modal-title">Editar Ubicación</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
					</div>
					<div class="modal-body">
						<div class="mb-3">
							<label class="form-label">Código <span class="text-danger">*</span></label>
							<input type="text" class="form-control" name="code" id="edit_code" required maxlength="20">
						</div>
						<div class="mb-3">
							<label class="form-label">Nombre <span class="text-danger">*</span></label>
							<input type="text" class="form-control" name="name" id="edit_name" required maxlength="100">
						</div>
						<div class="mb-3">
							<label class="form-label">Tipo <span class="text-danger">*</span></label>
							<select class="form-control" name="type" id="edit_type" required>
								<option value="estante">Estante</option>
								<option value="rack">Rack</option>
								<option value="piso">Piso</option>
								<option value="tarima">Tarima</option>
								<option value="refrigerador">Refrigerador</option>
								<option value="otro">Otro</option>
							</select>
						</div>
						<div class="row">
							<div class="col-md-4">
								<div class="mb-3">
									<label class="form-label">Pasillo</label>
									<input type="text" class="form-control" name="aisle" id="edit_aisle" maxlength="10">
								</div>
							</div>
							<div class="col-md-4">
								<div class="mb-3">
									<label class="form-label">Sección</label>
									<input type="text" class="form-control" name="section" id="edit_section" maxlength="10">
								</div>
							</div>
							<div class="col-md-4">
								<div class="mb-3">
									<label class="form-label">Nivel</label>
									<input type="text" class="form-control" name="level" id="edit_level" maxlength="10">
								</div>
							</div>
						</div>
						<div class="mb-3">
							<label class="form-label">Capacidad (unidades)</label>
							<input type="number" class="form-control" name="capacity_units" id="edit_capacity_units" min="0" step="1">
						</div>
						<div class="mb-3">
							<label class="form-label">Notas</label>
							<textarea class="form-control" name="notes" id="edit_notes" rows="3"></textarea>
						</div>
						<div class="mb-3">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_is_active">
								<label class="form-check-label" for="edit_is_active">
									Activo
								</label>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
						<button type="submit" class="btn btn-primary">
							<i class="fas fa-save"></i> Actualizar
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script src="<?= Uri::base() ?>assets/plugins/jquery/jquery-3.6.0.min.js"></script>
	<script src="<?= Uri::base() ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="<?= Uri::base() ?>assets/plugins/datatables/datatables.min.js"></script>
	<script src="<?= Uri::base() ?>assets/plugins/sweetalert2/sweetalert2.min.js"></script>
	<script>
	$(document).ready(function() {
		// DataTable
		$('#tableUbicaciones').DataTable({
			language: {
				url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
			},
			order: [[0, 'asc']],
			pageLength: 25
		});

		// Auto-generar código
		$('#name').on('blur', function() {
			var name = $(this).val().trim();
			if (name && !$('#code').val()) {
				// Formato sugerido basado en el nombre
				var code = name.substring(0, 10).toUpperCase().replace(/\s+/g, '-');
				$('#code').val(code);
			}
		});
	});

	// Editar ubicación
	function editarUbicacion(id) {
		$.get('<?= Uri::create('admin/almacenes/get_ubicacion') ?>/' + id, function(data) {
			if (data.success) {
				var ub = data.ubicacion;
				$('#edit_ubicacion_id').val(ub.id);
				$('#edit_code').val(ub.code);
				$('#edit_name').val(ub.name);
				$('#edit_type').val(ub.type);
				$('#edit_aisle').val(ub.aisle);
				$('#edit_section').val(ub.section);
				$('#edit_level').val(ub.level);
				$('#edit_capacity_units').val(ub.capacity_units);
				$('#edit_notes').val(ub.notes);
				$('#edit_is_active').prop('checked', ub.is_active == 1);
				
				$('#formEditarUbicacion').attr('action', '<?= Uri::create('admin/almacenes/ubicaciones/' . $almacen['id']) ?>');
				$('#modalEditarUbicacion').modal('show');
			} else {
				Swal.fire('Error', data.message || 'No se pudo cargar la ubicación', 'error');
			}
		}).fail(function() {
			Swal.fire('Error', 'Error de conexión', 'error');
		});
	}

	// Eliminar ubicación
	function eliminarUbicacion(id, codigo) {
		Swal.fire({
			title: '¿Eliminar ubicación?',
			html: 'Se eliminará la ubicación <strong>' + codigo + '</strong>.<br>Esta acción no se puede deshacer.',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#3085d6',
			confirmButtonText: 'Sí, eliminar',
			cancelButtonText: 'Cancelar'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: '<?= Uri::create('admin/almacenes/eliminar_ubicacion') ?>/' + id,
					type: 'POST',
					data: {
						'<?= Config::get('security.csrf_token_key') ?>': '<?= Security::fetch_token() ?>'
					},
					success: function(response) {
						if (response.success) {
							Swal.fire('Eliminado', response.message, 'success').then(() => {
								location.reload();
							});
						} else {
							Swal.fire('Error', response.message, 'error');
						}
					},
					error: function() {
						Swal.fire('Error', 'Error de conexión', 'error');
					}
				});
			}
		});
	}
	</script>
</body>
</html>
