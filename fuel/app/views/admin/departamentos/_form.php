<?php
$is_edit = isset($department);
$action_url = $is_edit ? Uri::create('admin/departamentos/edit/' . $department->id) : Uri::create('admin/departamentos/create');
$title = $is_edit ? 'Editar Departamento' : 'Nuevo Departamento';
?>

<div class="container-fluid">
	<div class="mb-4">
		<h2 class="mb-1"><i class="fa fa-sitemap me-2"></i><?php echo $title; ?></h2>
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb mb-0">
				<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin'); ?>">Inicio</a></li>
				<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin/departamentos'); ?>">Departamentos</a></li>
				<li class="breadcrumb-item active"><?php echo $title; ?></li>
			</ol>
		</nav>
	</div>

	<div class="row">
		<div class="col-lg-8">
			<form method="POST" action="<?php echo $action_url; ?>">
				<div class="card mb-4">
					<div class="card-body">
						<div class="row g-3">
							<div class="col-md-8">
								<label class="form-label">Nombre <span class="text-danger">*</span></label>
								<input type="text" name="name" class="form-control" required value="<?php echo $is_edit ? htmlspecialchars($department->name, ENT_QUOTES, 'UTF-8') : ''; ?>">
							</div>
							<div class="col-md-4">
								<label class="form-label">Código</label>
								<input type="text" name="code" class="form-control" value="<?php echo $is_edit ? htmlspecialchars($department->code, ENT_QUOTES, 'UTF-8') : ''; ?>">
							</div>

							<div class="col-md-6">
								<label class="form-label">Departamento Padre</label>
								<select name="parent_id" class="form-select">
									<option value="">Ninguno</option>
									<?php foreach ($departments as $dept): ?>
										<option value="<?php echo $dept->id; ?>" <?php echo ($is_edit && $department->parent_id == $dept->id) ? 'selected' : ''; ?>>
											<?php echo htmlspecialchars($dept->name, ENT_QUOTES, 'UTF-8'); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>

							<div class="col-md-6">
							<label class="form-label">Responsable</label>
							<select name="manager_id" class="form-select">
								<option value="">Seleccionar...</option>
								<?php foreach ($employees as $emp): ?>
									<option value="<?php echo $emp->id; ?>" <?php echo ($is_edit && $department->manager_id == $emp->id) ? 'selected' : ''; ?>>
										<?php echo htmlspecialchars($emp->get_full_name(), ENT_QUOTES, 'UTF-8'); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>							<div class="col-12">
								<label class="form-label">Descripción</label>
								<textarea name="description" class="form-control" rows="3"><?php echo $is_edit ? htmlspecialchars($department->description, ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
							</div>

							<?php if ($is_edit): ?>
							<div class="col-12">
								<div class="form-check">
									<input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" <?php echo $department->is_active ? 'checked' : ''; ?>>
									<label class="form-check-label" for="is_active">Activo</label>
								</div>
							</div>
							<?php endif; ?>
						</div>
					</div>
					<div class="card-footer">
						<button type="submit" class="btn btn-primary">
							<i class="fa fa-save me-2"></i><?php echo $is_edit ? 'Actualizar' : 'Guardar'; ?>
						</button>
						<a href="<?php echo Uri::create('admin/departamentos'); ?>" class="btn btn-secondary">
							<i class="fa fa-times me-2"></i>Cancelar
						</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
