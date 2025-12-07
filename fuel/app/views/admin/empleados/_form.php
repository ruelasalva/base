<?php
$is_edit = isset($employee);
$action_url = $is_edit ? Uri::create('admin/empleados/edit/' . $employee->id) : Uri::create('admin/empleados/create');
$title = $is_edit ? 'Editar Empleado' : 'Nuevo Empleado';
?>

<div class="container-fluid">
	<!-- Header -->
	<div class="mb-4">
		<h2 class="mb-1"><i class="fa fa-user-<?php echo $is_edit ? 'edit' : 'plus'; ?> me-2"></i><?php echo $title; ?></h2>
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb mb-0">
				<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin'); ?>">Inicio</a></li>
				<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin/empleados'); ?>">Empleados</a></li>
				<li class="breadcrumb-item active"><?php echo $title; ?></li>
			</ol>
		</nav>
	</div>

	<form method="POST" action="<?php echo $action_url; ?>" class="needs-validation" novalidate>
		<div class="row">
			<!-- Columna Principal -->
			<div class="col-lg-8">
				<!-- Información Personal -->
				<div class="card mb-4">
					<div class="card-header bg-primary text-white">
						<i class="fa fa-user me-2"></i>Información Personal
					</div>
					<div class="card-body">
						<div class="row g-3">
							<div class="col-md-6">
								<label class="form-label">Nombre <span class="text-danger">*</span></label>
								<input type="text" name="first_name" class="form-control" required value="<?php echo $is_edit ? htmlspecialchars($employee->first_name, ENT_QUOTES, 'UTF-8') : ''; ?>">
							</div>
							<div class="col-md-3">
								<label class="form-label">Apellido Paterno <span class="text-danger">*</span></label>
								<input type="text" name="last_name" class="form-control" required value="<?php echo $is_edit ? htmlspecialchars($employee->last_name, ENT_QUOTES, 'UTF-8') : ''; ?>">
							</div>
							<div class="col-md-3">
								<label class="form-label">Apellido Materno</label>
								<input type="text" name="second_last_name" class="form-control" value="<?php echo $is_edit ? htmlspecialchars($employee->second_last_name, ENT_QUOTES, 'UTF-8') : ''; ?>">
							</div>

							<div class="col-md-4">
								<label class="form-label">Código Empleado</label>
								<input type="text" name="code" class="form-control" placeholder="EMP001" value="<?php echo $is_edit ? htmlspecialchars($employee->code, ENT_QUOTES, 'UTF-8') : ''; ?>">
							</div>
							<div class="col-md-4">
								<label class="form-label">Género</label>
								<select name="gender" class="form-select">
									<option value="">Seleccionar...</option>
									<option value="M" <?php echo ($is_edit && $employee->gender == 'M') ? 'selected' : ''; ?>>Masculino</option>
									<option value="F" <?php echo ($is_edit && $employee->gender == 'F') ? 'selected' : ''; ?>>Femenino</option>
									<option value="O" <?php echo ($is_edit && $employee->gender == 'O') ? 'selected' : ''; ?>>Otro</option>
								</select>
							</div>
							<div class="col-md-4">
								<label class="form-label">Fecha de Nacimiento</label>
								<input type="date" name="birthdate" class="form-control" value="<?php echo $is_edit ? $employee->birthdate : ''; ?>">
							</div>

							<div class="col-md-4">
								<label class="form-label">CURP</label>
								<input type="text" name="curp" class="form-control" maxlength="18" placeholder="CURP" style="text-transform: uppercase;" value="<?php echo $is_edit ? htmlspecialchars($employee->curp, ENT_QUOTES, 'UTF-8') : ''; ?>">
							</div>
							<div class="col-md-4">
								<label class="form-label">RFC</label>
								<input type="text" name="rfc" class="form-control" maxlength="13" placeholder="RFC" style="text-transform: uppercase;" value="<?php echo $is_edit ? htmlspecialchars($employee->rfc, ENT_QUOTES, 'UTF-8') : ''; ?>">
							</div>
							<div class="col-md-4">
								<label class="form-label">NSS</label>
								<input type="text" name="nss" class="form-control" maxlength="11" placeholder="Núm. Seguro Social" value="<?php echo $is_edit ? htmlspecialchars($employee->nss, ENT_QUOTES, 'UTF-8') : ''; ?>">
							</div>
						</div>
					</div>
				</div>

				<!-- Contacto -->
				<div class="card mb-4">
					<div class="card-header bg-info text-white">
						<i class="fa fa-address-book me-2"></i>Información de Contacto
					</div>
					<div class="card-body">
						<div class="row g-3">
							<div class="col-md-6">
								<label class="form-label">Email <span class="text-danger">*</span></label>
								<input type="email" name="email" class="form-control" required value="<?php echo $is_edit ? htmlspecialchars($employee->email, ENT_QUOTES, 'UTF-8') : ''; ?>">
							</div>
							<div class="col-md-6">
								<label class="form-label">Teléfono</label>
								<input type="tel" name="phone" class="form-control" placeholder="(555) 123-4567" value="<?php echo $is_edit ? htmlspecialchars($employee->phone, ENT_QUOTES, 'UTF-8') : ''; ?>">
							</div>

							<div class="col-md-6">
								<label class="form-label">Teléfono de Emergencia</label>
								<input type="tel" name="phone_emergency" class="form-control" value="<?php echo $is_edit ? htmlspecialchars($employee->phone_emergency, ENT_QUOTES, 'UTF-8') : ''; ?>">
							</div>
							<div class="col-md-6">
								<label class="form-label">Nombre Contacto de Emergencia</label>
								<input type="text" name="emergency_contact_name" class="form-control" value="<?php echo $is_edit ? htmlspecialchars($employee->emergency_contact_name, ENT_QUOTES, 'UTF-8') : ''; ?>">
							</div>

							<div class="col-12">
								<label class="form-label">Dirección</label>
								<textarea name="address" class="form-control" rows="2"><?php echo $is_edit ? htmlspecialchars($employee->address, ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
							</div>

							<div class="col-md-4">
								<label class="form-label">Ciudad</label>
								<input type="text" name="city" class="form-control" value="<?php echo $is_edit ? htmlspecialchars($employee->city, ENT_QUOTES, 'UTF-8') : ''; ?>">
							</div>
							<div class="col-md-4">
								<label class="form-label">Estado</label>
								<input type="text" name="state" class="form-control" value="<?php echo $is_edit ? htmlspecialchars($employee->state, ENT_QUOTES, 'UTF-8') : ''; ?>">
							</div>
							<div class="col-md-2">
								<label class="form-label">C.P.</label>
								<input type="text" name="postal_code" class="form-control" maxlength="10" value="<?php echo $is_edit ? htmlspecialchars($employee->postal_code, ENT_QUOTES, 'UTF-8') : ''; ?>">
							</div>
							<div class="col-md-2">
								<label class="form-label">País</label>
								<input type="text" name="country" class="form-control" value="<?php echo $is_edit ? htmlspecialchars($employee->country, ENT_QUOTES, 'UTF-8') : 'México'; ?>">
							</div>
						</div>
					</div>
				</div>

				<!-- Información Financiera -->
				<div class="card mb-4">
					<div class="card-header bg-success text-white">
						<i class="fa fa-money-bill me-2"></i>Información Financiera
					</div>
					<div class="card-body">
						<div class="row g-3">
							<div class="col-md-6">
								<label class="form-label">Salario</label>
								<input type="number" step="0.01" name="salary" class="form-control" placeholder="0.00" value="<?php echo $is_edit ? $employee->salary : ''; ?>">
							</div>
							<div class="col-md-6">
								<label class="form-label">Tipo de Salario</label>
								<select name="salary_type" class="form-select">
									<option value="monthly" <?php echo ($is_edit && $employee->salary_type == 'monthly') ? 'selected' : ''; ?>>Mensual</option>
									<option value="biweekly" <?php echo ($is_edit && $employee->salary_type == 'biweekly') ? 'selected' : ''; ?>>Quincenal</option>
									<option value="weekly" <?php echo ($is_edit && $employee->salary_type == 'weekly') ? 'selected' : ''; ?>>Semanal</option>
									<option value="daily" <?php echo ($is_edit && $employee->salary_type == 'daily') ? 'selected' : ''; ?>>Diario</option>
									<option value="hourly" <?php echo ($is_edit && $employee->salary_type == 'hourly') ? 'selected' : ''; ?>>Por Hora</option>
								</select>
							</div>

							<div class="col-md-4">
								<label class="form-label">Banco</label>
								<input type="text" name="bank_name" class="form-control" value="<?php echo $is_edit ? htmlspecialchars($employee->bank_name, ENT_QUOTES, 'UTF-8') : ''; ?>">
							</div>
							<div class="col-md-4">
								<label class="form-label">Cuenta Bancaria</label>
								<input type="text" name="bank_account" class="form-control" value="<?php echo $is_edit ? htmlspecialchars($employee->bank_account, ENT_QUOTES, 'UTF-8') : ''; ?>">
							</div>
							<div class="col-md-4">
								<label class="form-label">CLABE</label>
								<input type="text" name="clabe" class="form-control" maxlength="18" value="<?php echo $is_edit ? htmlspecialchars($employee->clabe, ENT_QUOTES, 'UTF-8') : ''; ?>">
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Columna Lateral -->
			<div class="col-lg-4">
				<!-- Información Laboral -->
				<div class="card mb-4">
					<div class="card-header bg-warning text-dark">
						<i class="fa fa-briefcase me-2"></i>Información Laboral
					</div>
					<div class="card-body">
						<div class="mb-3">
							<label class="form-label">Departamento</label>
							<select name="department_id" class="form-select">
								<option value="">Seleccionar...</option>
								<?php foreach ($departments as $dept): ?>
									<option value="<?php echo $dept->id; ?>" <?php echo ($is_edit && $employee->department_id == $dept->id) ? 'selected' : ''; ?>>
										<?php echo htmlspecialchars($dept->name, ENT_QUOTES, 'UTF-8'); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="mb-3">
							<label class="form-label">Puesto</label>
							<select name="position_id" class="form-select">
								<option value="">Seleccionar...</option>
								<?php foreach ($positions as $pos): ?>
									<option value="<?php echo $pos->id; ?>" <?php echo ($is_edit && $employee->position_id == $pos->id) ? 'selected' : ''; ?>>
										<?php echo htmlspecialchars($pos->name, ENT_QUOTES, 'UTF-8'); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="mb-3">
							<label class="form-label">Fecha de Contratación <span class="text-danger">*</span></label>
							<input type="date" name="hire_date" class="form-control" required value="<?php echo $is_edit ? $employee->hire_date : ''; ?>">
						</div>

						<?php if ($is_edit): ?>
						<div class="mb-3">
							<label class="form-label">Fecha de Baja</label>
							<input type="date" name="termination_date" class="form-control" value="<?php echo $employee->termination_date; ?>">
						</div>
						<?php endif; ?>

						<div class="mb-3">
							<label class="form-label">Tipo de Empleo</label>
							<select name="employment_type" class="form-select">
								<option value="full_time" <?php echo ($is_edit && $employee->employment_type == 'full_time') ? 'selected' : 'selected'; ?>>Tiempo Completo</option>
								<option value="part_time" <?php echo ($is_edit && $employee->employment_type == 'part_time') ? 'selected' : ''; ?>>Medio Tiempo</option>
								<option value="contract" <?php echo ($is_edit && $employee->employment_type == 'contract') ? 'selected' : ''; ?>>Contrato</option>
								<option value="intern" <?php echo ($is_edit && $employee->employment_type == 'intern') ? 'selected' : ''; ?>>Practicante</option>
								<option value="temporary" <?php echo ($is_edit && $employee->employment_type == 'temporary') ? 'selected' : ''; ?>>Temporal</option>
							</select>
						</div>

						<div class="mb-3">
							<label class="form-label">Estatus</label>
							<select name="employment_status" class="form-select">
								<option value="active" <?php echo ($is_edit && $employee->employment_status == 'active') ? 'selected' : 'selected'; ?>>Activo</option>
								<option value="inactive" <?php echo ($is_edit && $employee->employment_status == 'inactive') ? 'selected' : ''; ?>>Inactivo</option>
								<option value="suspended" <?php echo ($is_edit && $employee->employment_status == 'suspended') ? 'selected' : ''; ?>>Suspendido</option>
								<option value="on_leave" <?php echo ($is_edit && $employee->employment_status == 'on_leave') ? 'selected' : ''; ?>>Con Permiso</option>
								<option value="terminated" <?php echo ($is_edit && $employee->employment_status == 'terminated') ? 'selected' : ''; ?>>Terminado</option>
							</select>
						</div>
					</div>
				</div>

				<!-- Notas -->
				<div class="card mb-4">
					<div class="card-header">
						<i class="fa fa-sticky-note me-2"></i>Notas
					</div>
					<div class="card-body">
						<textarea name="notes" class="form-control" rows="6" placeholder="Información adicional..."><?php echo $is_edit ? htmlspecialchars($employee->notes, ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
					</div>
				</div>

				<!-- Acciones -->
				<div class="card">
					<div class="card-body">
						<button type="submit" class="btn btn-primary w-100 mb-2">
							<i class="fa fa-save me-2"></i><?php echo $is_edit ? 'Actualizar' : 'Guardar'; ?> Empleado
						</button>
						<a href="<?php echo Uri::create('admin/empleados'); ?>" class="btn btn-secondary w-100">
							<i class="fa fa-times me-2"></i>Cancelar
						</a>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<script>
// Validación del formulario
(function() {
	'use strict';
	var forms = document.querySelectorAll('.needs-validation');
	Array.prototype.slice.call(forms).forEach(function(form) {
		form.addEventListener('submit', function(event) {
			if (!form.checkValidity()) {
				event.preventDefault();
				event.stopPropagation();
			}
			form.classList.add('was-validated');
		}, false);
	});
})();

// Convertir a mayúsculas
document.querySelectorAll('input[name="curp"], input[name="rfc"]').forEach(function(input) {
	input.addEventListener('input', function() {
		this.value = this.value.toUpperCase();
	});
});
</script>
