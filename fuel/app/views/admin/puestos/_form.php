<?php
$is_edit = isset($position);
$action_url = $is_edit ? Uri::create('admin/puestos/edit/' . $position->id) : Uri::create('admin/puestos/create');
$title = $is_edit ? 'Editar Puesto' : 'Nuevo Puesto';
?>

<div class="container-fluid">
	<div class="mb-4">
		<h2 class="mb-1"><i class="fa fa-user-tag me-2"></i><?php echo $title; ?></h2>
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb mb-0">
				<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin'); ?>">Inicio</a></li>
				<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin/puestos'); ?>">Puestos</a></li>
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
								<input type="text" name="name" class="form-control" required value="<?php echo $is_edit ? htmlspecialchars($position->name, ENT_QUOTES, 'UTF-8') : ''; ?>">
							</div>
							<div class="col-md-4">
								<label class="form-label">Código</label>
								<input type="text" name="code" class="form-control" value="<?php echo $is_edit ? htmlspecialchars($position->code, ENT_QUOTES, 'UTF-8') : ''; ?>">
							</div>

							<div class="col-12">
								<label class="form-label">Descripción</label>
								<textarea name="description" class="form-control" rows="3"><?php echo $is_edit ? htmlspecialchars($position->description, ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
							</div>

							<div class="col-md-6">
								<label class="form-label">Salario Mínimo</label>
								<input type="number" step="0.01" name="salary_min" class="form-control" placeholder="0.00" value="<?php echo $is_edit ? $position->salary_min : ''; ?>">
							</div>
							<div class="col-md-6">
								<label class="form-label">Salario Máximo</label>
								<input type="number" step="0.01" name="salary_max" class="form-control" placeholder="0.00" value="<?php echo $is_edit ? $position->salary_max : ''; ?>">
							</div>

							<?php if ($is_edit): ?>
							<div class="col-12">
								<div class="form-check">
									<input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" <?php echo $position->is_active ? 'checked' : ''; ?>>
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
						<a href="<?php echo Uri::create('admin/puestos'); ?>" class="btn btn-secondary">
							<i class="fa fa-times me-2"></i>Cancelar
						</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
