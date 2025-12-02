<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Editar Plantilla</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item"><?php echo Html::anchor('admin','<i class="fas fa-home"></i>'); ?></li>
							<li class="breadcrumb-item"><?php echo Html::anchor('admin/configuracion/correos','Correos'); ?></li>
							<li class="breadcrumb-item"><?php echo Html::anchor('admin/configuracion/correos/templates','Plantillas'); ?></li>
							<li class="breadcrumb-item active" aria-current="page">Editar</li>
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
					<h3 class="mb-0">Editar Plantilla</h3>
				</div>
				<div class="card-body">
					<?php echo Form::open(['method'=>'post']); ?>

						<div class="form-group <?= $classes['code']['form-group'] ?>">
							<?php echo Form::label('Código único','code'); ?>
							<?php echo Form::input('code',$code,['class'=>'form-control '.$classes['code']['form-control']]); ?>
							<?php if(isset($errors['code'])): ?><div class="invalid-feedback"><?= $errors['code']; ?></div><?php endif; ?>
						</div>

						<div class="form-group <?= $classes['role']['form-group'] ?>">
							<?php echo Form::label('Rol asociado','role'); ?>
							<?php echo Form::input('role',$role,['class'=>'form-control '.$classes['role']['form-control']]); ?>
							<?php if(isset($errors['role'])): ?><div class="invalid-feedback"><?= $errors['role']; ?></div><?php endif; ?>
						</div>

						<div class="form-group <?= $classes['subject']['form-group'] ?>">
							<?php echo Form::label('Asunto','subject'); ?>
							<?php echo Form::input('subject',$subject,['class'=>'form-control '.$classes['subject']['form-control']]); ?>
							<?php if(isset($errors['subject'])): ?><div class="invalid-feedback"><?= $errors['subject']; ?></div><?php endif; ?>
						</div>

						<div class="form-group <?= $classes['view']['form-group'] ?>">
							<?php echo Form::label('Vista FuelPHP','view'); ?>
							<?php echo Form::input('view',$view,['class'=>'form-control '.$classes['view']['form-control']]); ?>
							<?php if(isset($errors['view'])): ?><div class="invalid-feedback"><?= $errors['view']; ?></div><?php endif; ?>
						</div>

						<div class="form-group">
    <label>Editar contenido HTML</label><br>
    <?php if (isset($id) && $id > 0): ?>
        <?php echo Html::anchor(
            'admin/configuracion/correos/templates/editor/'.$id, 
            '<i class="fas fa-code"></i> Abrir editor de contenido',
            array('class'=>'btn btn-sm btn-info', 'target'=>'_blank')
        ); ?>
    <?php else: ?>
        <span class="text-muted">Guarda primero la plantilla para habilitar el editor.</span>
    <?php endif; ?>
</div>

						<button type="submit" class="btn btn-primary">Actualizar</button>
						<?php echo Html::anchor('admin/configuracion/correos/templates','Cancelar',['class'=>'btn btn-secondary']); ?>

					<?php echo Form::close(); ?>
				</div>
			</div>
		</div>
	</div>
</div>
