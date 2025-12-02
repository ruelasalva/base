<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Configuración General</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/configuracion/general', 'Datos de la Empresa'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								Ver Información
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php if (!empty($id)): ?>
						<?php echo Html::anchor('admin/configuracion/general/editar/'.$id, 'Editar', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php else: ?>
						<?php echo Html::anchor('admin/configuracion/general/editar', 'Agregar', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php endif; ?>
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

				<!-- DATOS GENERALES DE LA EMPRESA -->
				<div class="card">
					<div class="card-header">
						<h3 class="mb-0">Información General</h3>
					</div>
					<div class="card-body">
						<div class="form-row">
							<div class="col-md-6 mb-3">
								<div class="form-group">
									<?php echo Form::label('Nombre de la empresa', 'name', array('class' => 'form-control-label')); ?>
									<span class="form-control"><?php echo $name; ?></span>
								</div>
							</div>
							<div class="col-md-6 mb-3">
								<div class="form-group">
									<?php echo Form::label('RFC', 'rfc', array('class' => 'form-control-label')); ?>
									<span class="form-control"><?php echo $rfc; ?></span>
								</div>
							</div>
							<div class="col-md-6 mb-3">
								<div class="form-group">
									<?php echo Form::label('CP', 'cp', array('class' => 'form-control-label')); ?>
									<span class="form-control"><?php echo $cp; ?></span>
								</div>
							</div>
							<div class="col-md-6 mb-3">
								<div class="form-group">
									<?php echo Form::label('Régimen Fiscal', 'id_sat_tax_regimes', array('class' => 'form-control-label')); ?>
									<span class="form-control"><?php echo $sat_tax_regime_name ?? 'Por capturar'; ?></span>
								</div>
							</div>
							<div class="col-md-6 mb-3">
								<div class="form-group">
									<?php echo Form::label('Última modificación', 'updated_at', array('class' => 'form-control-label')); ?>
									<span class="form-control"><?php echo  $updated_at; ?></span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- BLOQUE: CONFIGURACIÓN DE FACTURACIÓN Y PAGOS -->
				<div class="card mt-4">
					<div class="card-header">
						<h4 class="mb-0">Facturación y Pagos</h4>
					</div>
					<div class="card-body">
						<div class="form-row">
							<!-- DÍAS DE RECEPCIÓN DE FACTURAS -->
							<div class="col-md-6 mb-3">
								<div class="form-group">
									<?php echo Form::label('Días de Recepción de Facturas', '', array('class' => 'form-control-label')); ?>
									<span class="form-control">
										<?php
										$weekdays = [
											'sunday'    => 'Domingo',
											'monday'    => 'Lunes',
											'tuesday'   => 'Martes',
											'wednesday' => 'Miércoles',
											'thursday'  => 'Jueves',
											'friday'    => 'Viernes',
											'saturday'  => 'Sábado',
										];
										$dias = (isset($invoice_receive_days) ? (is_array($invoice_receive_days) ? $invoice_receive_days : explode(',', $invoice_receive_days)) : []);
										if (count($dias)) {
											$dias_legibles = [];
											foreach ($dias as $day) {
												if (isset($weekdays[$day])) $dias_legibles[] = $weekdays[$day];
											}
											echo implode(', ', $dias_legibles);
										} else {
											echo 'No capturado';
										}
										?>
									</span>
								</div>
							</div>
							<!-- HORA LÍMITE DE RECEPCIÓN DE FACTURAS -->
							<div class="col-md-6 mb-3">
								<div class="form-group">
									<?php echo Form::label('Hora límite de Recepción de Facturas', '', array('class' => 'form-control-label')); ?>
									<span class="form-control">
										<?php
										if (!empty($invoice_receive_limit_time)) {
											// Convierte a formato 12 horas
											$h = date('g:i a', strtotime($invoice_receive_limit_time));
											echo $h;
										} else {
											echo 'No capturado';
										}
										?>
									</span>

								</div>
							</div>
							<!-- DÍAS DE PAGO -->
							<div class="col-md-6 mb-3">
								<div class="form-group">
									<?php echo Form::label('Días de Pago', '', array('class' => 'form-control-label')); ?>
									<span class="form-control">
										<?php
										$dias_pago = (isset($payment_days) ? (is_array($payment_days) ? $payment_days : explode(',', $payment_days)) : []);
										if (count($dias_pago)) {
											$dias_legibles = [];
											foreach ($dias_pago as $day) {
												if (isset($weekdays[$day])) $dias_legibles[] = $weekdays[$day];
											}
											echo implode(', ', $dias_legibles);
										} else {
											echo 'No capturado';
										}
										?>
									</span>
								</div>
							</div>
							<!-- TÉRMINOS DE PAGO (DÍAS HÁBILES) -->
							<div class="col-md-6 mb-3">
								<div class="form-group">
									<?php echo Form::label('Días hábiles para pago después de recepción', '', array('class' => 'form-control-label')); ?>
									<span class="form-control"><?php echo !empty($payment_terms_days) ? $payment_terms_days : 'No capturado'; ?></span>
								</div>
							</div>
							<!-- CORREO DE CONTACTO -->
							<div class="col-md-6 mb-3">
								<div class="form-group">
									<?php echo Form::label('Correo de Contacto', '', array('class' => 'form-control-label')); ?>
									<span class="form-control"><?php echo !empty($contact_email) ? $contact_email : 'No capturado'; ?></span>
								</div>
							</div>
							<!-- TELÉFONO DE CONTACTO -->
							<div class="col-md-6 mb-3">
								<div class="form-group">
									<?php echo Form::label('Teléfono de Contacto', '', array('class' => 'form-control-label')); ?>
									<span class="form-control"><?php echo !empty($contact_phone) ? $contact_phone : 'No capturado'; ?></span>
								</div>
							</div>
							<!-- MENSAJE O AVISO GLOBAL -->
							<div class="col-md-12 mb-3">
								<div class="form-group">
									<?php echo Form::label('Mensaje o Aviso Global', '', array('class' => 'form-control-label')); ?>
									<span class="form-control"><?php echo !empty($announcement_message) ? $announcement_message : 'No capturado'; ?></span>
								</div>
							</div>
							<!-- BLOQUEO DE RECEPCIÓN DE FACTURAS -->
							<div class="col-md-6 mb-3">
								<div class="form-group">
									<?php echo Form::label('Bloquear Recepción de Facturas', '', array('class' => 'form-control-label')); ?>
									<span class="form-control"><?php echo isset($blocked_reception) && $blocked_reception == 1 ? 'Sí' : 'No'; ?></span>
								</div>
							</div>
							<!-- DÍAS FERIADOS -->
							<div class="col-md-6 mb-3">
								<div class="form-group">
									<?php echo Form::label('Días Feriados', '', array('class' => 'form-control-label')); ?>
									<span class="form-control">
										<?php
										if (!empty($holidays)) {
											$holidays_arr = explode(',', $holidays);
											echo implode(', ', $holidays_arr);
										} else {
											echo 'No capturado';
										}
										?>
									</span>
								</div>
							</div>
							<!-- ARCHIVO DE POLÍTICAS (PDF) -->
							<div class="col-md-6 mb-3">
								<div class="form-group">
									<?php echo Form::label('Archivo de Políticas (PDF)', '', array('class' => 'form-control-label')); ?>
									<span class="form-control">
										<?php if (!empty($policy_file)): ?>
											<a href="<?php echo Uri::create('uploads/config/'.$policy_file); ?>" target="_blank" class="btn btn-link p-0">Descargar archivo</a>
										<?php else: ?>
											No capturado
										<?php endif; ?>
									</span>
								</div>
							</div>
							<!-- FRECUENCIA DE PAGO Y DÍAS DEL MES -->
							<?php if (isset($payment_frequency) || isset($payment_days_of_month)): ?>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Frecuencia de Pago', '', array('class' => 'form-control-label')); ?>
										<span class="form-control">
											<?php
											$freqs = [
												'' => 'No capturado',
												'weekly'   => 'Semanal',
												'biweekly' => 'Quincenal',
												'monthly'  => 'Mensual'
											];
											echo !empty($payment_frequency) && isset($freqs[$payment_frequency]) ? $freqs[$payment_frequency] : 'No capturado';
											?>
										</span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Días del Mes para Pago', '', array('class' => 'form-control-label')); ?>
										<span class="form-control">
											<?php
											if (!empty($payment_days_of_month)) {
												echo $payment_days_of_month;
											} else {
												echo 'No capturado';
											}
											?>
										</span>
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>

			</div> <!-- end card-wrapper -->
		</div>
	</div>
</div>
