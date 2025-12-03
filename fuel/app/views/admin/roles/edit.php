<div class="animated fadeIn">
	<div class="row">
		<div class="col-lg-8">
			<div class="card">
				<div class="card-header">
					<i class="fa fa-edit"></i> <?php echo $title; ?>
					<?php if ($role['is_system']): ?>
						<span class="badge badge-warning">Rol del Sistema</span>
					<?php endif; ?>
				</div>
				<div class="card-body">
					<?php echo Form::open(['action' => 'admin/roles/edit/' . $role['id'], 'method' => 'post', 'class' => 'form-horizontal']); ?>
						
						<div class="form-group row">
							<label class="col-md-3 col-form-label">Nombre Interno</label>
							<div class="col-md-9">
								<input type="text" class="form-control" value="<?php echo $role['name']; ?>" disabled>
								<small class="form-text text-muted">El nombre interno no se puede modificar</small>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="display_name">Nombre para Mostrar <span class="text-danger">*</span></label>
							<div class="col-md-9">
								<?php echo Form::input('display_name', Input::post('display_name', $role['display_name']), ['class' => 'form-control', 'id' => 'display_name', 'required' => true]); ?>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="description">Descripción</label>
							<div class="col-md-9">
								<?php echo Form::textarea('description', Input::post('description', $role['description']), ['class' => 'form-control', 'id' => 'description', 'rows' => 3]); ?>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="level">Nivel de Jerarquía <span class="text-danger">*</span></label>
							<div class="col-md-9">
								<?php echo Form::input('level', Input::post('level', $role['level']), ['class' => 'form-control', 'id' => 'level', 'type' => 'number', 'min' => 1, 'max' => 100, 'required' => true]); ?>
								<small class="form-text text-muted">Nivel jerárquico (1-100). Mayor número = mayor privilegio</small>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-md-3 col-form-label">Estado</label>
							<div class="col-md-9">
								<?php if ($role['is_active']): ?>
									<span class="badge badge-success"><i class="fa fa-check"></i> Activo</span>
								<?php else: ?>
									<span class="badge badge-secondary"><i class="fa fa-ban"></i> Inactivo</span>
								<?php endif; ?>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-md-3 col-form-label">Fechas</label>
							<div class="col-md-9">
								<small class="text-muted">
									<strong>Creado:</strong> <?php echo date('d/m/Y H:i', $role['created_at']); ?><br>
									<?php if ($role['updated_at']): ?>
										<strong>Actualizado:</strong> <?php echo date('d/m/Y H:i', $role['updated_at']); ?>
									<?php endif; ?>
								</small>
							</div>
						</div>

						<div class="form-group row">
							<div class="col-md-9 offset-md-3">
								<button type="submit" class="btn btn-primary">
									<i class="fa fa-save"></i> Guardar Cambios
								</button>
								<a href="<?php echo Uri::create('admin/roles/view/' . $role['id']); ?>" class="btn btn-secondary">
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
					<i class="fa fa-cog"></i> Acciones Rápidas
				</div>
				<div class="card-body">
					<a href="<?php echo Uri::create('admin/roles/permissions/' . $role['id']); ?>" class="btn btn-block btn-info mb-2">
						<i class="fa fa-key"></i> Gestionar Permisos
					</a>
					<a href="<?php echo Uri::create('admin/roles/view/' . $role['id']); ?>" class="btn btn-block btn-secondary mb-2">
						<i class="fa fa-eye"></i> Ver Detalle
					</a>
					
					<?php if (!$role['is_system']): ?>
						<hr>
						<button type="button" class="btn btn-block btn-danger" onclick="deleteRole(<?php echo $role['id']; ?>)">
							<i class="fa fa-trash"></i> Eliminar Rol
						</button>
					<?php endif; ?>
				</div>
			</div>

			<div class="card">
				<div class="card-header">
					<i class="fa fa-info-circle"></i> Información
				</div>
				<div class="card-body">
					<?php if ($role['is_system']): ?>
						<div class="alert alert-warning mb-0">
							<i class="fa fa-exclamation-triangle"></i>
							<strong>Rol del Sistema</strong><br>
							Este es un rol especial del sistema. Algunas opciones están limitadas.
						</div>
					<?php else: ?>
						<p>Puedes modificar el nombre para mostrar, descripción y nivel de este rol.</p>
						<p>El nombre interno no se puede cambiar una vez creado el rol.</p>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
function deleteRole(roleId) {
	if (confirm('¿Estás seguro de eliminar este rol?\n\nEsta acción no se puede deshacer y afectará a todos los usuarios asignados a este rol.')) {
		fetch('<?php echo Uri::create("admin/roles/delete"); ?>/' + roleId, {
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
				window.location.href = '<?php echo Uri::create("admin/roles"); ?>';
			} else {
				alert('Error: ' + data.message);
			}
		})
		.catch(error => {
			console.error('Error:', error);
			alert('Error al eliminar el rol');
		});
	}
}
</script>
