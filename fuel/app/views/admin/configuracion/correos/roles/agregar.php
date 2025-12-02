<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Correos - Agregar Rol</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin','<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/configuracion/correos','Correos'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/configuracion/correos/roles','Roles'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">Agregar</li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
	<div class="row">
		<div class="col">
			<div class="card">
				<div class="card-header">
					<h3 class="mb-0">Nuevo Rol de Correo</h3>
				</div>
				<div class="card-body">
					<?php echo Form::open(['method'=>'post']); ?>

						<div class="form-group <?= $classes['role']['form-group'] ?>">
							<?php echo Form::label('Nombre del Rol','role'); ?>
							<?php echo Form::input('role',$role,['class'=>'form-control '.$classes['role']['form-control']]); ?>
							<?php if(isset($errors['role'])): ?><div class="invalid-feedback"><?= $errors['role']; ?></div><?php endif; ?>
						</div>

						<div class="form-group <?= $classes['from_email']['form-group'] ?>">
							<?php echo Form::label('Correo remitente','from_email'); ?>
							<?php echo Form::input('from_email',$from_email,['class'=>'form-control '.$classes['from_email']['form-control']]); ?>
							<?php if(isset($errors['from_email'])): ?><div class="invalid-feedback"><?= $errors['from_email']; ?></div><?php endif; ?>
						</div>

						<div class="form-group">
							<?php echo Form::label('Nombre remitente','from_name'); ?>
							<?php echo Form::input('from_name',$from_name,['class'=>'form-control']); ?>
						</div>

						<div class="form-group <?= $classes['reply_to_email']['form-group'] ?>">
							<?php echo Form::label('Correo reply-to','reply_to_email'); ?>
							<?php echo Form::input('reply_to_email',$reply_to_email,['class'=>'form-control '.$classes['reply_to_email']['form-control']]); ?>
							<?php if(isset($errors['reply_to_email'])): ?><div class="invalid-feedback"><?= $errors['reply_to_email']; ?></div><?php endif; ?>
						</div>

						<div class="form-group">
							<?php echo Form::label('Nombre reply-to','reply_to_name'); ?>
							<?php echo Form::input('reply_to_name',$reply_to_name,['class'=>'form-control']); ?>
						</div>

						<div class="form-group">
							<?php echo Form::label('Destinatarios (separados por coma)','to_emails'); ?>
							<?php echo Form::textarea('to_emails',$to_emails,['class'=>'form-control','rows'=>3]); ?>
						</div>

						<button type="submit" class="btn btn-primary">Guardar</button>
						<?php echo Html::anchor('admin/configuracion/correos/roles','Cancelar',['class'=>'btn btn-secondary']); ?>

					<?php echo Form::close(); ?>
				</div>
			</div>
		</div>
	</div>
</div>
