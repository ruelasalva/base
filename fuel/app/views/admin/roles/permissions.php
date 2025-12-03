<!-- HEADER -->
<div class="row mb-4">
	<div class="col-md-12">
		<div class="d-flex justify-content-between align-items-center">
			<div>
				<h2 class="mb-1">
					<i class="fas fa-key me-2"></i>Permisos del Rol: 
					<span class="text-primary"><?php echo htmlspecialchars($role['display_name']); ?></span>
				</h2>
				<p class="text-muted mb-0">Selecciona los permisos que tendrá este rol</p>
			</div>
			<div>
				<a href="<?php echo Uri::create('admin/roles/view/' . $role['id']); ?>" class="btn btn-secondary">
					<i class="fas fa-arrow-left me-2"></i>Volver
				</a>
			</div>
		</div>
	</div>
</div>

<!-- FORMULARIO DE PERMISOS -->
<form method="POST" action="<?php echo Uri::current(); ?>">
	<div class="row">
		<div class="col-12 mb-4">
			<div class="card">
				<div class="card-header bg-primary text-white">
					<div class="d-flex justify-content-between align-items-center">
						<h5 class="mb-0"><i class="fas fa-check-double me-2"></i>Selección de Permisos</h5>
						<div>
							<button type="button" class="btn btn-sm btn-light" onclick="selectAll()">
								<i class="fas fa-check-square me-1"></i>Marcar Todos
							</button>
							<button type="button" class="btn btn-sm btn-light" onclick="deselectAll()">
								<i class="fas fa-square me-1"></i>Desmarcar Todos
							</button>
						</div>
					</div>
				</div>
				<div class="card-body">
					<?php
					// Nombres de módulos en español
					$module_names = [
						'dashboard' => 'Dashboard',
						'users' => 'Usuarios',
						'roles' => 'Roles',
						'config' => 'Configuración',
						'inventory' => 'Inventario',
						'crm' => 'CRM - Clientes',
						'sales' => 'Ventas',
						'products' => 'Productos',
						'customers' => 'Clientes',
						'reports' => 'Reportes',
						'facturacion' => 'Facturación',
						'contabilidad' => 'Contabilidad',
						'nomina' => 'Nómina',
						'rrhh' => 'Recursos Humanos',
						'bi' => 'Business Intelligence'
					];

					// Iconos de módulos
					$module_icons = [
						'dashboard' => 'fa-home',
						'users' => 'fa-users',
						'roles' => 'fa-user-shield',
						'config' => 'fa-cog',
						'inventory' => 'fa-boxes',
						'crm' => 'fa-user-friends',
						'sales' => 'fa-shopping-cart',
						'products' => 'fa-box',
						'customers' => 'fa-address-book',
						'reports' => 'fa-chart-bar',
						'facturacion' => 'fa-file-invoice',
						'contabilidad' => 'fa-calculator',
						'nomina' => 'fa-money-check',
						'rrhh' => 'fa-id-badge',
						'bi' => 'fa-chart-line'
					];

					$count = 0;
					foreach ($permissions_by_module as $module => $permissions):
						$module_name = $module_names[$module] ?? ucfirst($module);
						$module_icon = $module_icons[$module] ?? 'fa-folder';
					?>
					<div class="module-section mb-4">
						<div class="module-header d-flex align-items-center mb-3 pb-2 border-bottom">
							<i class="fas <?php echo $module_icon; ?> fa-lg text-primary me-2"></i>
							<h5 class="mb-0"><?php echo $module_name; ?></h5>
							<span class="badge bg-secondary ms-2"><?php echo count($permissions); ?> permisos</span>
							<div class="ms-auto">
								<input type="checkbox" class="form-check-input module-toggle" data-module="<?php echo $module; ?>" id="toggle-<?php echo $module; ?>">
								<label class="form-check-label small text-muted ms-1" for="toggle-<?php echo $module; ?>">
									Todos
								</label>
							</div>
						</div>
						
						<div class="row">
							<?php foreach ($permissions as $perm): ?>
							<div class="col-md-6 col-lg-4 mb-2">
								<div class="form-check">
									<input 
										class="form-check-input permission-checkbox" 
										type="checkbox" 
										name="permissions[]" 
										value="<?php echo $perm['id']; ?>" 
										id="perm-<?php echo $perm['id']; ?>"
										data-module="<?php echo $module; ?>"
										<?php echo in_array($perm['id'], $current_permission_ids) ? 'checked' : ''; ?>
									>
									<label class="form-check-label" for="perm-<?php echo $perm['id']; ?>">
										<strong><?php echo htmlspecialchars($perm['name']); ?></strong>
										<br><small class="text-muted"><?php echo htmlspecialchars($perm['action']); ?></small>
									</label>
								</div>
							</div>
							<?php endforeach; ?>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>

	<!-- BOTONES -->
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<div class="d-flex justify-content-between">
						<a href="<?php echo Uri::create('admin/roles/view/' . $role['id']); ?>" class="btn btn-secondary">
							<i class="fas fa-times me-2"></i>Cancelar
						</a>
						<button type="submit" class="btn btn-primary">
							<i class="fas fa-save me-2"></i>Guardar Permisos
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<!-- SCRIPTS -->
<script>
// Seleccionar todos los permisos
function selectAll() {
	document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = true);
	document.querySelectorAll('.module-toggle').forEach(cb => cb.checked = true);
}

// Deseleccionar todos los permisos
function deselectAll() {
	document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
	document.querySelectorAll('.module-toggle').forEach(cb => cb.checked = false);
}

// Toggle por módulo
document.addEventListener('DOMContentLoaded', function() {
	// Actualizar estado inicial de toggles de módulo
	document.querySelectorAll('.module-toggle').forEach(toggle => {
		updateModuleToggle(toggle);
	});

	// Manejar toggle de módulo
	document.querySelectorAll('.module-toggle').forEach(toggle => {
		toggle.addEventListener('change', function() {
			const module = this.dataset.module;
			const checkboxes = document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`);
			checkboxes.forEach(cb => cb.checked = this.checked);
		});
	});

	// Actualizar toggle de módulo cuando cambian permisos individuales
	document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
		checkbox.addEventListener('change', function() {
			const module = this.dataset.module;
			const toggle = document.querySelector(`.module-toggle[data-module="${module}"]`);
			updateModuleToggle(toggle);
		});
	});
});

function updateModuleToggle(toggle) {
	const module = toggle.dataset.module;
	const checkboxes = document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`);
	const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
	
	toggle.checked = checkedCount === checkboxes.length;
	toggle.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
}
</script>

<style>
.module-section {
	background: #f8f9fa;
	padding: 1rem;
	border-radius: 0.5rem;
}

.module-header {
	background: white;
	padding: 0.75rem;
	border-radius: 0.375rem;
	margin: -1rem -1rem 1rem -1rem;
}

.form-check {
	padding: 0.5rem;
	background: white;
	border-radius: 0.25rem;
	border: 1px solid #dee2e6;
	transition: all 0.2s;
}

.form-check:hover {
	border-color: #0d6efd;
	box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
}

.form-check-input:checked ~ .form-check-label {
	color: #0d6efd;
}
</style>
