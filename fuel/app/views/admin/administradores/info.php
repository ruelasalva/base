<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Administradores</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/administradores', 'Administradores'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/administradores/info/'.$id, $full_name); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/administradores/editar/'.$id, 'Editar', array('class' => 'btn btn-sm btn-neutral')); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
	<div class="row">
		<div class="col">
			<div class="card-wrapper">
				<!-- CUSTOM FORM VALIDATION -->
				<div class="card">
					<!-- CARD HEADER -->
					<div class="card-header">
						<h3 class="mb-0">Ver información</h3>
					</div>
					<!-- CARD BODY -->
					<div class="card-body">
						<fieldset>
							<div class="form-row">
								<div class="col-md-12 mt-0 mb-3">
									<legend class="mb-0 heading">Información del usuario</legend>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Usuario', 'username', array('class' => 'form-control-label', 'for' => 'username')); ?>
										<span class="form-control"><?php echo $username; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Email', 'email', array('class' => 'form-control-label', 'for' => 'email')); ?>
										<span class="form-control"><?php echo $email; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Contraseña', 'password', array('class' => 'form-control-label', 'for' => 'password')); ?>
										<span class="form-control">*******</span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Tipo de usuario', 'group', array('class' => 'form-control-label', 'for' => 'group')); ?>
										<span class="form-control"><?php echo $group; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Baneado', 'banned', array('class' => 'form-control-label', 'for' => 'banned')); ?>
										<span class="form-control"><?php echo $banned; ?></span>
									</div>
								</div>
							</div>
						</fieldset>
						<fieldset>
							<div class="form-row">
								<div class="col-md-12 mt-4 mb-3">
									<legend class="mb-0 heading">Información del perfil</legend>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Nombre completo', 'full_name', array('class' => 'form-control-label', 'for' => 'full_name')); ?>
										<span class="form-control"><?php echo $full_name; ?></span>
									</div>
								</div>
							</div>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
