<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Logs</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/logs', 'logs'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/logs/info/'.$id, '#'.$id); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/logs/editar/'.$id, 'Editar', array('class' => 'btn btn-sm btn-neutral')); ?>
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
									<legend class="mb-0 heading">Información de la categoría</legend>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Proveedor', 'provider', array('class' => 'form-control-label', 'for' => 'provider')); ?>
										<span class="form-control"><?php echo $provider; ?></span>
									</div>
								</div>
                                <div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Fecha', 'date', array('class' => 'form-control-label', 'for' => 'date')); ?>
										<span class="form-control"><?php echo $date; ?></span>
									</div>
								</div>
                                <div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Tipo', 'type', array('class' => 'form-control-label', 'for' => 'type')); ?>
										<span class="form-control"><?php echo $type; ?></span>
									</div>
								</div>
                                <div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Transacción', 'transaction', array('class' => 'form-control-label', 'for' => 'transaction')); ?>
										<span class="form-control"><?php echo $transaction; ?></span>
									</div>
								</div>
                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <?php echo Form::label('Respuesta', 'response', array('class' => 'form-control-label', 'for' => 'response')); ?>
                                        <span class="form-control from-table form-table-area"><pre><?php print_r($response); ?></pre></span>
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
