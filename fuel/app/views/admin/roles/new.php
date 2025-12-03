<div class="animated fadeIn">
	<div class="row">
		<div class="col-lg-8">
			<div class="card">
				<div class="card-header">
					<i class="fa fa-shield-alt"></i> <?php echo $title; ?>
				</div>
				<div class="card-body">
					<?php echo Form::open(['action' => 'admin/roles/new', 'method' => 'post', 'class' => 'form-horizontal']); ?>
						
						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="name">Nombre Interno <span class="text-danger">*</span></label>
							<div class="col-md-9">
								<?php echo Form::input('name', Input::post('name'), ['class' => 'form-control', 'id' => 'name', 'required' => true, 'placeholder' => 'admin, manager, seller (sin espacios, minúsculas)']); ?>
								<small class="form-text text-muted">Identificador único del rol (sin espacios, minúsculas, guiones bajos permitidos)</small>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="display_name">Nombre para Mostrar <span class="text-danger">*</span></label>
							<div class="col-md-9">
								<?php echo Form::input('display_name', Input::post('display_name'), ['class' => 'form-control', 'id' => 'display_name', 'required' => true, 'placeholder' => 'Administrador, Gerente, Vendedor']); ?>
								<small class="form-text text-muted">Nombre que se mostrará en la interfaz</small>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="description">Descripción</label>
							<div class="col-md-9">
								<?php echo Form::textarea('description', Input::post('description'), ['class' => 'form-control', 'id' => 'description', 'rows' => 3, 'placeholder' => 'Descripción del rol y sus responsabilidades']); ?>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="level">Nivel de Jerarquía <span class="text-danger">*</span></label>
							<div class="col-md-9">
								<?php echo Form::input('level', Input::post('level', 1), ['class' => 'form-control', 'id' => 'level', 'type' => 'number', 'min' => 1, 'max' => 100, 'required' => true]); ?>
								<small class="form-text text-muted">Nivel jerárquico (1-100). Mayor número = mayor privilegio. Super Admin: 100, Admin: 90</small>
							</div>
						</div>

						<div class="form-group row">
							<div class="col-md-9 offset-md-3">
								<button type="submit" class="btn btn-success">
									<i class="fa fa-check"></i> Crear Rol
								</button>
								<a href="<?php echo Uri::create('admin/roles'); ?>" class="btn btn-secondary">
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
					<i class="fa fa-info-circle"></i> Ayuda
				</div>
				<div class="card-body">
					<h6>Creación de Roles</h6>
					<p><strong>Nombre Interno:</strong> Identificador único usado en el código. Debe ser en minúsculas, sin espacios.</p>
					<p><strong>Nombre para Mostrar:</strong> El nombre que verán los usuarios en la interfaz.</p>
					<p><strong>Nivel de Jerarquía:</strong> Define la importancia del rol. Niveles más altos tienen más autoridad.</p>
					
					<hr>
					
					<h6>Niveles Sugeridos</h6>
					<ul class="mb-0">
						<li><strong>100:</strong> Super Administrador</li>
						<li><strong>90:</strong> Administrador</li>
						<li><strong>50:</strong> Gerente</li>
						<li><strong>30:</strong> Supervisor</li>
						<li><strong>10:</strong> Empleado</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
// Generar nombre interno automáticamente desde el nombre para mostrar
document.getElementById('display_name').addEventListener('input', function() {
	var displayName = this.value;
	var internalName = displayName.toLowerCase()
		.normalize("NFD").replace(/[\u0300-\u036f]/g, "") // Quitar acentos
		.replace(/[^a-z0-9\s]/g, '') // Quitar caracteres especiales
		.replace(/\s+/g, '_') // Espacios a guiones bajos
		.trim();
	
	document.getElementById('name').value = internalName;
});

// Validación del formulario
document.querySelector('form').addEventListener('submit', function(e) {
	var name = document.getElementById('name').value;
	var displayName = document.getElementById('display_name').value;
	
	if (name.trim() === '' || displayName.trim() === '') {
		e.preventDefault();
		alert('El nombre interno y nombre para mostrar son requeridos');
		return false;
	}
	
	// Validar que el nombre interno no tenga espacios ni mayúsculas
	if (name.match(/[^a-z0-9_]/)) {
		e.preventDefault();
		alert('El nombre interno solo puede contener letras minúsculas, números y guiones bajos');
		return false;
	}
});
</script>
