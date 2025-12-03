<div class="animated fadeIn">
	<div class="row">
		<div class="col-lg-8">
			<div class="card">
				<div class="card-header">
					<i class="fa fa-edit"></i> <?php echo $title; ?>
				</div>
				<div class="card-body">
					<?php echo Form::open(['action' => 'admin/permissions/edit/' . $permission['id'], 'method' => 'post', 'class' => 'form-horizontal']); ?>
						
						<div class="form-group row">
							<label class="col-md-3 col-form-label">Módulo</label>
							<div class="col-md-9">
								<input type="text" class="form-control" value="<?php echo $permission['module']; ?>" disabled>
								<small class="form-text text-muted">El módulo no se puede modificar</small>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-md-3 col-form-label">Acción</label>
							<div class="col-md-9">
								<input type="text" class="form-control" value="<?php echo $permission['action']; ?>" disabled>
								<small class="form-text text-muted">La acción no se puede modificar</small>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="name">Nombre <span class="text-danger">*</span></label>
							<div class="col-md-9">
								<?php echo Form::input('name', Input::post('name', $permission['name']), ['class' => 'form-control', 'id' => 'name', 'required' => true]); ?>
								<small class="form-text text-muted">Nombre descriptivo para mostrar en la interfaz</small>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="description">Descripción</label>
							<div class="col-md-9">
								<?php echo Form::textarea('description', Input::post('description', $permission['description']), ['class' => 'form-control', 'id' => 'description', 'rows' => 3]); ?>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-md-3 col-form-label">Estado</label>
							<div class="col-md-9">
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="is_active" id="active1" value="1" <?php echo $permission['is_active'] ? 'checked' : ''; ?>>
									<label class="form-check-label" for="active1">
										<span class="badge badge-success"><i class="fa fa-check"></i> Activo</span>
									</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="is_active" id="active0" value="0" <?php echo !$permission['is_active'] ? 'checked' : ''; ?>>
									<label class="form-check-label" for="active0">
										<span class="badge badge-secondary"><i class="fa fa-ban"></i> Inactivo</span>
									</label>
								</div>
								<br>
								<small class="form-text text-muted">Si se desactiva, los roles que lo tengan asignado no podrán usar esta funcionalidad</small>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-md-3 col-form-label">Identificador</label>
							<div class="col-md-9">
								<code><?php echo $permission['module'] . '.' . $permission['action']; ?></code>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-md-3 col-form-label">Fechas</label>
							<div class="col-md-9">
								<small class="text-muted">
									<strong>Creado:</strong> <?php echo date('d/m/Y H:i', $permission['created_at']); ?><br>
									<?php if ($permission['updated_at']): ?>
										<strong>Actualizado:</strong> <?php echo date('d/m/Y H:i', $permission['updated_at']); ?>
									<?php endif; ?>
								</small>
							</div>
						</div>

						<div class="form-group row">
							<div class="col-md-9 offset-md-3">
								<button type="submit" class="btn btn-primary">
									<i class="fa fa-save"></i> Guardar Cambios
								</button>
								<a href="<?php echo Uri::create('admin/permissions/view/' . $permission['id']); ?>" class="btn btn-secondary">
									<i class="fa fa-arrow-left"></i> Volver
								</a>
							</div>
						</div>

					<?php echo Form::close(); ?>
				</div>
			</div>
		</div>

		<div class="col-lg-4">
			<div class="card">
				<div class="card-header">
					<i class="fa fa-cog"></i> Acciones
				</div>
				<div class="card-body">
					<a href="<?php echo Uri::create('admin/permissions/view/' . $permission['id']); ?>" class="btn btn-block btn-secondary mb-2">
						<i class="fa fa-eye"></i> Ver Detalle
					</a>
					<a href="<?php echo Uri::create('admin/permissions'); ?>" class="btn btn-block btn-outline-secondary mb-2">
						<i class="fa fa-list"></i> Ver Todos los Permisos
					</a>
					
					<hr>
					
					<button type="button" class="btn btn-block btn-danger" onclick="deletePermission(<?php echo $permission['id']; ?>)">
						<i class="fa fa-trash"></i> Eliminar Permiso
					</button>
				</div>
			</div>

			<div class="card">
				<div class="card-header">
					<i class="fa fa-info-circle"></i> Información
				</div>
				<div class="card-body">
					<p><strong>¿Qué puedes editar?</strong></p>
					<ul>
						<li>Nombre para mostrar</li>
						<li>Descripción detallada</li>
						<li>Estado (activo/inactivo)</li>
					</ul>
					
					<p><strong>No modificable:</strong></p>
					<ul class="mb-0">
						<li>Módulo</li>
						<li>Acción</li>
					</ul>
					
					<hr>
					
					<p class="mb-0">
						<small class="text-muted">
							Si necesitas cambiar el módulo o acción, debes crear un nuevo permiso y eliminar este.
						</small>
					</p>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
function deletePermission(id) {
	if (confirm('¿Estás seguro de eliminar este permiso?\n\nEsta acción no se puede deshacer y afectará a todos los roles que lo tienen asignado.')) {
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
				window.location.href = '<?php echo Uri::create("admin/permissions"); ?>';
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
