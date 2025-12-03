<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">
					<i class="fas fa-edit"></i> Editar Módulo: <?php echo $module['name']; ?>
				</h3>
				<div class="card-tools">
					<a href="<?php echo Uri::create('admin/system_modules'); ?>" class="btn btn-sm btn-default">
						<i class="fas fa-arrow-left"></i> Volver
					</a>
				</div>
			</div>
			
			<?php echo Form::open(['class' => 'form-horizontal']); ?>
			<?php echo Form::csrf(); ?>
			
			<div class="card-body">
				
				<!-- Nombre Interno (solo lectura) -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Nombre Interno:</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" value="<?php echo $module['name']; ?>" disabled>
						<small class="text-muted">Este campo no se puede modificar</small>
					</div>
				</div>

				<!-- Nombre Visible (CAMPO PRINCIPAL PARA CORREGIR ACENTOS) -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">
						<span class="text-danger">*</span> Nombre Visible:
					</label>
					<div class="col-sm-9">
						<?php echo Form::input('display_name', Input::post('display_name', $module['display_name']), [
							'class' => 'form-control',
							'required' => 'required',
							'maxlength' => 100,
							'placeholder' => 'Ejemplo: Órdenes de Compra'
						]); ?>
						<small class="text-info">
							<i class="fas fa-info-circle"></i> 
							Este es el nombre que se muestra en el menú. Corrige aquí los acentos.
						</small>
					</div>
				</div>

				<!-- Descripción -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Descripción:</label>
					<div class="col-sm-9">
						<?php echo Form::textarea('description', Input::post('description', $module['description']), [
							'class' => 'form-control',
							'rows' => 3,
							'maxlength' => 255,
							'placeholder' => 'Descripción del módulo'
						]); ?>
						<small class="text-muted">Máximo 255 caracteres</small>
					</div>
				</div>

				<!-- Icono -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Icono:</label>
					<div class="col-sm-9">
						<div class="input-group">
							<span class="input-group-text">
								<i id="icon-preview" class="<?php echo Input::post('icon', $module['icon']); ?>"></i>
							</span>
							<?php echo Form::input('icon', Input::post('icon', $module['icon']), [
								'class' => 'form-control',
								'id' => 'icon-input',
								'placeholder' => 'fas fa-shopping-cart'
							]); ?>
						</div>
						<small class="text-muted">
							Clases de Font Awesome 6. 
							<a href="https://fontawesome.com/icons" target="_blank">Ver iconos disponibles</a>
						</small>
					</div>
				</div>

				<!-- Categoría -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">
						<span class="text-danger">*</span> Categoría:
					</label>
					<div class="col-sm-9">
						<?php 
						$categories = [
							'core' => 'Core (Sistema)',
							'compras' => 'Compras',
							'ventas' => 'Ventas',
							'inventario' => 'Inventario',
							'finanzas' => 'Finanzas',
							'rrhh' => 'Recursos Humanos',
							'admin' => 'Administración',
							'reportes' => 'Reportes',
							'configuracion' => 'Configuración'
						];
						echo Form::select('category', Input::post('category', $module['category']), $categories, [
							'class' => 'form-select',
							'required' => 'required'
						]); 
						?>
					</div>
				</div>

				<!-- Posición de Orden -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Posición de Orden:</label>
					<div class="col-sm-9">
						<?php echo Form::input('order_position', Input::post('order_position', $module['order_position']), [
							'class' => 'form-control',
							'type' => 'number',
							'min' => 0,
							'max' => 999,
							'placeholder' => '0'
						]); ?>
						<small class="text-muted">Orden de aparición en el menú (menor número = aparece primero)</small>
					</div>
				</div>

				<!-- Estado Activo -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Estado:</label>
					<div class="col-sm-9">
						<div class="form-check form-switch">
							<?php 
							$is_checked = (bool) Input::post('is_active', $module['is_active']);
							echo Form::checkbox('is_active', 1, $is_checked, [
								'class' => 'form-check-input',
								'id' => 'is_active'
							]); 
							?>
							<label class="form-check-label" for="is_active">
								Módulo Activo
							</label>
							<br>
							<small class="text-muted">Los módulos inactivos no se muestran en el menú</small>
						</div>
					</div>
				</div>

			</div>

			<div class="card-footer">
				<div class="row">
					<div class="col-sm-9 offset-sm-3">
						<button type="submit" class="btn btn-success">
							<i class="fas fa-save"></i> Guardar Cambios
						</button>
						<a href="<?php echo Uri::create('admin/system_modules'); ?>" class="btn btn-secondary">
							<i class="fas fa-times"></i> Cancelar
						</a>
					</div>
				</div>
			</div>
			
			<?php echo Form::close(); ?>
		</div>
	</div>
</div>

<script>
// Vista previa del icono en tiempo real
document.addEventListener('DOMContentLoaded', function() {
	const iconInput = document.getElementById('icon-input');
	const iconPreview = document.getElementById('icon-preview');
	
	if (iconInput && iconPreview) {
		iconInput.addEventListener('input', function() {
			// Limpiar clases anteriores
			iconPreview.className = '';
			// Agregar nuevas clases
			iconPreview.className = this.value;
		});
	}
});
</script>

<style>
.card-header {
	background-color: #f8f9fa;
	border-bottom: 2px solid #007bff;
}

.text-danger {
	font-weight: bold;
}

.form-check-input:checked {
	background-color: #28a745;
	border-color: #28a745;
}

#icon-preview {
	font-size: 1.2em;
}
</style>
