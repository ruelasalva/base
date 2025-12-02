<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Procesadores de pago</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/procesadores_pago', 'Procesadores de pago'); ?>
							</li>
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
			<div class="card-wrapper">
				<!-- CUSTOM FORM VALIDATION -->
				<div class="card">
					<!-- CARD HEADER -->
					<div class="card-header">
						<h3 class="mb-0">Editar</h3>
					</div>
					<!-- CARD BODY -->
					<div class="card-body">
						<?php echo Form::open(array('method' => 'post')); ?>
							<fieldset>
								<div class="form-row">
									<div class="col-md-12 mt-0 mb-3">
										<legend class="mb-0 heading">Información de los procesadores de pago</legend>
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['bbva']['form-group']; ?>">
											<?php echo Form::label('BBVA', 'bbva', array('class' => 'form-control-label', 'for' => 'bbva')); ?>
                                            <?php
												echo Form::select('bbva', (isset($bbva) ? $bbva : 0), array(
                                                    '1' => 'Activado',
													'0' => 'Desactivado',
												), array('id' => 'bbva', 'class' => 'form-control '.$classes['bbva']['form-control'], 'data-toggle' => 'select'));
											?>
											<?php if(isset($errors['bbva'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['bbva']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
                                    <div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['transfer']['form-group']; ?>">
											<?php echo Form::label('Transferencia/Depósito', 'transfer', array('class' => 'form-control-label', 'for' => 'transfer')); ?>
                                            <?php
												echo Form::select('transfer', (isset($transfer) ? $transfer : 0), array(
                                                    '1' => 'Activado',
													'0' => 'Desactivado',
												), array('id' => 'transfer', 'class' => 'form-control '.$classes['transfer']['form-control'], 'data-toggle' => 'select'));
											?>
											<?php if(isset($errors['transfer'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['transfer']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
								</div>
							</fieldset>
							<?php echo Form::submit(array('value'=> 'Editar', 'name'=>'submit', 'class' => 'btn btn-primary')); ?>
						<?php echo Form::close(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
