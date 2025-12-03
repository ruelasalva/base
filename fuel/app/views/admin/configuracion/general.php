<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">
					<i class="fas fa-sliders-h"></i> Configuración General
				</h3>
			</div>

			<?php echo Form::open(['class' => 'form-horizontal']); ?>
			
			<div class="card-body">
				
				<!-- Nombre de la Aplicación -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">
						<span class="text-danger">*</span> Nombre de la Aplicación:
					</label>
					<div class="col-sm-9">
						<?php echo Form::input('app_name', isset($settings['app_name']) ? $settings['app_name'] : '', [
							'class' => 'form-control',
							'required' => 'required',
							'placeholder' => 'Ej: Mi Sistema ERP'
						]); ?>
						<small class="text-muted">Nombre que aparecerá en el navegador y encabezados</small>
					</div>
				</div>

				<!-- Descripción -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Descripción:</label>
					<div class="col-sm-9">
						<?php echo Form::textarea('app_description', isset($settings['app_description']) ? $settings['app_description'] : '', [
							'class' => 'form-control',
							'rows' => 3,
							'placeholder' => 'Breve descripción del sistema'
						]); ?>
					</div>
				</div>

				<!-- URL de la Aplicación -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">
						<span class="text-danger">*</span> URL de la Aplicación:
					</label>
					<div class="col-sm-9">
						<?php echo Form::input('app_url', isset($settings['app_url']) ? $settings['app_url'] : '', [
							'class' => 'form-control',
							'type' => 'url',
							'required' => 'required',
							'placeholder' => 'https://ejemplo.com'
						]); ?>
						<small class="text-muted">URL base del sistema (incluir https://)</small>
					</div>
				</div>

				<hr class="my-4">

				<!-- Zona Horaria -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Zona Horaria:</label>
					<div class="col-sm-9">
						<?php 
						$timezones = [
							'America/Mexico_City' => 'Ciudad de México (GMT-6)',
							'America/Monterrey' => 'Monterrey (GMT-6)',
							'America/Cancun' => 'Cancún (GMT-5)',
							'America/Tijuana' => 'Tijuana (GMT-8)',
							'America/New_York' => 'Nueva York (GMT-5)',
							'America/Los_Angeles' => 'Los Ángeles (GMT-8)',
							'Europe/Madrid' => 'Madrid (GMT+1)',
							'UTC' => 'UTC (GMT+0)'
						];
						echo Form::select('timezone', isset($settings['timezone']) ? $settings['timezone'] : 'America/Mexico_City', $timezones, [
							'class' => 'form-select'
						]); 
						?>
					</div>
				</div>

				<!-- Formato de Fecha -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Formato de Fecha:</label>
					<div class="col-sm-9">
						<?php 
						$date_formats = [
							'Y-m-d' => '2025-12-03 (YYYY-MM-DD)',
							'd/m/Y' => '03/12/2025 (DD/MM/YYYY)',
							'm/d/Y' => '12/03/2025 (MM/DD/YYYY)',
							'd-m-Y' => '03-12-2025 (DD-MM-YYYY)'
						];
						echo Form::select('date_format', isset($settings['date_format']) ? $settings['date_format'] : 'Y-m-d', $date_formats, [
							'class' => 'form-select'
						]); 
						?>
					</div>
				</div>

				<!-- Formato de Hora -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Formato de Hora:</label>
					<div class="col-sm-9">
						<?php 
						$time_formats = [
							'H:i:s' => '23:45:30 (24 horas)',
							'H:i' => '23:45 (24 horas sin segundos)',
							'h:i A' => '11:45 PM (12 horas)'
						];
						echo Form::select('time_format', isset($settings['time_format']) ? $settings['time_format'] : 'H:i:s', $time_formats, [
							'class' => 'form-select'
						]); 
						?>
					</div>
				</div>

				<!-- Idioma -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Idioma:</label>
					<div class="col-sm-9">
						<?php 
						$languages = [
							'es' => 'Español',
							'en' => 'English',
							'fr' => 'Français',
							'de' => 'Deutsch'
						];
						echo Form::select('language', isset($settings['language']) ? $settings['language'] : 'es', $languages, [
							'class' => 'form-select'
						]); 
						?>
					</div>
				</div>

				<hr class="my-4">

				<!-- Items por Página -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Items por Página:</label>
					<div class="col-sm-9">
						<?php echo Form::input('items_per_page', isset($settings['items_per_page']) ? $settings['items_per_page'] : 20, [
							'class' => 'form-control',
							'type' => 'number',
							'min' => 10,
							'max' => 100,
							'step' => 10
						]); ?>
						<small class="text-muted">Cantidad de registros a mostrar en listados paginados</small>
					</div>
				</div>

				<!-- Modo Mantenimiento -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Modo Mantenimiento:</label>
					<div class="col-sm-9">
						<div class="form-check form-switch">
							<?php 
							$is_maintenance = isset($settings['maintenance_mode']) && $settings['maintenance_mode'] == 1;
							echo Form::checkbox('maintenance_mode', 1, $is_maintenance, [
								'class' => 'form-check-input',
								'id' => 'maintenance_mode'
							]); 
							?>
							<label class="form-check-label" for="maintenance_mode">
								Activar modo mantenimiento
							</label>
							<br>
							<small class="text-muted">Cuando está activado, solo los administradores pueden acceder</small>
						</div>
					</div>
				</div>

			</div>

			<div class="card-footer">
				<div class="row">
					<div class="col-sm-9 offset-sm-3">
						<button type="submit" class="btn btn-success">
							<i class="fas fa-save"></i> Guardar Configuración
						</button>
						<a href="<?php echo Uri::create('admin/configuracion'); ?>" class="btn btn-secondary">
							<i class="fas fa-times"></i> Cancelar
						</a>
					</div>
				</div>
			</div>

			<?php echo Form::close(); ?>
		</div>
	</div>
</div>

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

hr.my-4 {
	border-top: 2px solid #e9ecef;
	margin: 2rem 0;
}
</style>
