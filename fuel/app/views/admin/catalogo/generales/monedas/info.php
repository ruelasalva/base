<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Catálogo</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/catalogo/generales/monedas', 'Monedas'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/catalogo/generales/monedas/info/'.$id, $name); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/catalogo/generales/monedas/editar/'.$id, 'Editar', array('class' => 'btn btn-sm btn-neutral')); ?>
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
									<legend class="mb-0 heading">Información de la moneda</legend>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Nombre', 'name', array('class' => 'form-control-label')); ?>
										<span class="form-control"><?php echo $name; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Código', 'code', array('class' => 'form-control-label')); ?>
										<span class="form-control"><?php echo $code; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Símbolo', 'symbol', array('class' => 'form-control-label')); ?>
										<span class="form-control"><?php echo $symbol; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Tipo de cambio', 'type_exchange', array('class' => 'form-control-label')); ?>
										<span class="form-control"><?php echo $type_exchange; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Estatus', 'deleted', array('class' => 'form-control-label')); ?>
										<span class="form-control">
											<?php echo ($deleted == 0) ? 'Activo' : 'Inactivo'; ?>
										</span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Fecha de creación', 'created_at', array('class' => 'form-control-label')); ?>
										<span class="form-control">
											<?php echo (!empty($created_at)) ? date('d/m/Y H:i', $created_at) : '-'; ?>
										</span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Última modificación', 'updated_at', array('class' => 'form-control-label')); ?>
										<span class="form-control">
											<?php echo (!empty($updated_at)) ? date('d/m/Y H:i', $updated_at) : '-'; ?>
										</span>
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
