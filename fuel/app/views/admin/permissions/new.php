<div class="animated fadeIn">
	<div class="row">
		<div class="col-lg-8">
			<div class="card">
				<div class="card-header">
					<i class="fa fa-plus"></i> <?php echo $title; ?>
				</div>
				<div class="card-body">
					<?php echo Form::open(['action' => 'admin/permissions/new', 'method' => 'post', 'class' => 'form-horizontal']); ?>
						
						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="module">Módulo <span class="text-danger">*</span></label>
							<div class="col-md-9">
								<?php echo Form::input('module', Input::post('module'), ['class' => 'form-control', 'id' => 'module', 'required' => true, 'list' => 'modules-list', 'placeholder' => 'users, roles, inventory, sales, crm']); ?>
								<datalist id="modules-list">
									<?php foreach ($existing_modules as $mod): ?>
										<option value="<?php echo $mod; ?>">
									<?php endforeach; ?>
								</datalist>
								<small class="form-text text-muted">Nombre del módulo al que pertenece este permiso (minúsculas, sin espacios)</small>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="action">Acción <span class="text-danger">*</span></label>
							<div class="col-md-9">
								<?php echo Form::input('action', Input::post('action'), ['class' => 'form-control', 'id' => 'action', 'required' => true, 'list' => 'actions-list', 'placeholder' => 'view, create, edit, delete']); ?>
								<datalist id="actions-list">
									<option value="view">
									<option value="create">
									<option value="edit">
									<option value="delete">
									<option value="export">
									<option value="import">
									<option value="approve">
									<option value="reject">
								</datalist>
								<small class="form-text text-muted">Acción específica del permiso (view, create, edit, delete, etc.)</small>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="name">Nombre <span class="text-danger">*</span></label>
							<div class="col-md-9">
								<?php echo Form::input('name', Input::post('name'), ['class' => 'form-control', 'id' => 'name', 'required' => true, 'placeholder' => 'Ver Usuarios, Crear Productos']); ?>
								<small class="form-text text-muted">Nombre descriptivo del permiso para mostrar en la interfaz</small>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="description">Descripción</label>
							<div class="col-md-9">
								<?php echo Form::textarea('description', Input::post('description'), ['class' => 'form-control', 'id' => 'description', 'rows' => 3, 'placeholder' => 'Permite ver el listado de usuarios del sistema y su información básica']); ?>
								<small class="form-text text-muted">Descripción detallada de qué permite hacer este permiso</small>
							</div>
						</div>

						<div class="form-group row">
							<div class="col-md-9 offset-md-3">
								<div class="alert alert-info">
									<i class="fa fa-info-circle"></i>
									<strong>Vista Previa:</strong><br>
									<span id="preview">-</span>
								</div>
							</div>
						</div>

						<div class="form-group row">
							<div class="col-md-9 offset-md-3">
								<button type="submit" class="btn btn-success">
									<i class="fa fa-check"></i> Crear Permiso
								</button>
								<a href="<?php echo Uri::create('admin/permissions'); ?>" class="btn btn-secondary">
									<i class="fa fa-times"></i> Cancelar
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
					<i class="fa fa-info-circle"></i> Guía de Permisos
				</div>
				<div class="card-body">
					<h6>Estructura de Permisos</h6>
					<p>Un permiso se compone de:</p>
					<ul>
						<li><strong>Módulo:</strong> Sistema o funcionalidad (users, inventory, sales)</li>
						<li><strong>Acción:</strong> Operación específica (view, create, edit, delete)</li>
						<li><strong>Nombre:</strong> Descripción corta para UI</li>
					</ul>

					<hr>

					<h6>Acciones Comunes</h6>
					<dl>
						<dt>view</dt>
						<dd>Ver/Consultar información</dd>
						
						<dt>create</dt>
						<dd>Crear nuevos registros</dd>
						
						<dt>edit</dt>
						<dd>Modificar registros existentes</dd>
						
						<dt>delete</dt>
						<dd>Eliminar registros</dd>
						
						<dt>export</dt>
						<dd>Exportar datos (Excel, PDF)</dd>
						
						<dt>approve</dt>
						<dd>Aprobar solicitudes o documentos</dd>
					</dl>

					<hr>

					<h6>Nomenclatura</h6>
					<p class="mb-0">
						<strong>Módulo:</strong> minúsculas, plural<br>
						<strong>Acción:</strong> minúsculas, verbo<br>
						<strong>Nombre:</strong> Título legible<br>
					</p>
				</div>
			</div>

			<div class="card">
				<div class="card-header">
					<i class="fa fa-list"></i> Módulos Existentes
				</div>
				<div class="card-body">
					<?php if (empty($existing_modules)): ?>
						<p class="text-muted mb-0"><em>No hay módulos registrados</em></p>
					<?php else: ?>
						<div class="list-group list-group-flush">
							<?php foreach ($existing_modules as $mod): ?>
								<div class="list-group-item px-0 py-2">
									<code><?php echo $mod; ?></code>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
// Auto-generar nombre basado en módulo y acción
function updatePreview() {
	var module = document.getElementById('module').value;
	var action = document.getElementById('action').value;
	var name = document.getElementById('name').value;
	
	if (module && action) {
		var preview = '<code>' + module + '.' + action + '</code>';
		if (name) {
			preview += ' → <strong>' + name + '</strong>';
		}
		document.getElementById('preview').innerHTML = preview;
	} else {
		document.getElementById('preview').innerHTML = '-';
	}
}

document.getElementById('module').addEventListener('input', updatePreview);
document.getElementById('action').addEventListener('input', updatePreview);
document.getElementById('name').addEventListener('input', updatePreview);

// Auto-sugerir nombre
document.getElementById('action').addEventListener('change', function() {
	var action = this.value.toLowerCase();
	var module = document.getElementById('module').value;
	var nameField = document.getElementById('name');
	
	if (nameField.value === '' && module && action) {
		var actionNames = {
			'view': 'Ver',
			'create': 'Crear',
			'edit': 'Editar',
			'delete': 'Eliminar',
			'export': 'Exportar',
			'import': 'Importar',
			'approve': 'Aprobar',
			'reject': 'Rechazar'
		};
		
		var moduleName = module.charAt(0).toUpperCase() + module.slice(1);
		var actionName = actionNames[action] || action.charAt(0).toUpperCase() + action.slice(1);
		
		nameField.value = actionName + ' ' + moduleName;
		updatePreview();
	}
});

// Validación del formulario
document.querySelector('form').addEventListener('submit', function(e) {
	var module = document.getElementById('module').value.toLowerCase();
	var action = document.getElementById('action').value.toLowerCase();
	
	// Validar formato de módulo y acción (sin espacios, minúsculas, solo letras, números y guiones bajos)
	var regex = /^[a-z0-9_]+$/;
	
	if (!regex.test(module)) {
		e.preventDefault();
		alert('El módulo solo puede contener letras minúsculas, números y guiones bajos');
		return false;
	}
	
	if (!regex.test(action)) {
		e.preventDefault();
		alert('La acción solo puede contener letras minúsculas, números y guiones bajos');
		return false;
	}
});
</script>
