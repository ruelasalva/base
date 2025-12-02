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
							<li class="breadcrumb-item active" aria-current="page">
								Editar Información de la Empresa
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/configuracion/general/', 'Ver Datos', array('class' => 'btn btn-sm btn-neutral')); ?>
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
					<div class="card-header">
						<h3 class="mb-0">Editar Información</h3>
					</div>
					<div class="card-body">
						<?php echo Form::open(array('method' => 'post', 'enctype' => 'multipart/form-data')); ?>
							<fieldset>
								<div class="form-row">
									<div class="col-md-12 mt-0 mb-3">
										<legend class="mb-0 heading">Datos Generales</legend>
									</div>

									<!-- NOMBRE DE LA EMPRESA -->
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['name']['form-group']; ?>">
											<?php echo Form::label('Nombre de la empresa', 'name', array('class' => 'form-control-label')); ?>
											<?php echo Form::input('name', $name, array(
												'id' => 'name',
												'class' => 'form-control '.$classes['name']['form-control'],
												'placeholder' => 'Nombre de la empresa'
											)); ?>
											<?php if(isset($errors['name'])): ?>
												<div class="invalid-feedback">
													<?php echo $errors['name']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<!-- RFC -->
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['rfc']['form-group']; ?>">
											<?php echo Form::label('RFC', 'rfc', array('class' => 'form-control-label')); ?>
											<?php echo Form::input('rfc', $rfc, array(
												'id' => 'rfc',
												'class' => 'form-control '.$classes['rfc']['form-control'],
												'placeholder' => 'RFC'
											)); ?>
											<?php if(isset($errors['rfc'])): ?>
												<div class="invalid-feedback">
													<?php echo $errors['rfc']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<!-- CP -->
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['cp']['form-group']; ?>">
											<?php echo Form::label('Código Postal', 'cp', array('class' => 'form-control-label')); ?>
											<?php echo Form::input('cp', $cp, array(
												'id' => 'cp',
												'class' => 'form-control '.$classes['cp']['form-control'],
												'placeholder' => 'Código Postal'
											)); ?>
											<?php if(isset($errors['cp'])): ?>
												<div class="invalid-feedback">
													<?php echo $errors['cp']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<!-- RÉGIMEN FISCAL -->
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['id_sat_tax_regimes']['form-group']; ?>">
											<?php echo Form::label('Régimen Fiscal SAT', 'id_sat_tax_regimes', array('class' => 'form-control-label')); ?>
											<?php echo Form::select('id_sat_tax_regimes', $id_sat_tax_regimes, $regimen_opts, array(
												'class' => 'form-control '.$classes['id_sat_tax_regimes']['form-control']
											)); ?>
											<?php if(isset($errors['id_sat_tax_regimes'])): ?>
												<div class="invalid-feedback">
													<?php echo $errors['id_sat_tax_regimes']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
								</div>
							</fieldset>

							<!-- BLOQUE: CONFIGURACIÓN DE FACTURACIÓN Y PAGOS -->
							<fieldset>
								<div class="form-row mt-4">
									<div class="col-md-12 mb-3">
										<legend class="mb-0 heading">Configuración de Facturación y Pagos</legend>
									</div>
									<!-- RECEPCIÓN DE FACTURAS -->
									<div class="col-md-6 mb-3">
										<div class="card border bg-light">
											<div class="card-body py-2 px-3">
												<h6 class="mb-2 font-weight-bold text-primary"><i class="fas fa-file-invoice mr-1"></i> Recepción de Facturas</h6>
												<!-- DÍAS DE RECEPCIÓN -->
												<label class="mb-1 font-weight-normal">Días de Recepción</label>
												<div class="d-flex flex-wrap mb-2">
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
													$selected_days = (is_array($invoice_receive_days) ? $invoice_receive_days : explode(',', $invoice_receive_days));
													foreach ($weekdays as $day_val => $day_label): ?>
														<div class="form-check mr-3 mb-1">
															<input class="form-check-input" type="checkbox" name="invoice_receive_days[]" value="<?php echo $day_val; ?>"
																id="rcv_<?php echo $day_val; ?>" <?php echo (in_array($day_val, $selected_days)) ? 'checked' : ''; ?>>
															<label class="form-check-label small" for="rcv_<?php echo $day_val; ?>"><?php echo $day_label; ?></label>
														</div>
													<?php endforeach; ?>
												</div>
												<!-- HORA LÍMITE -->
												<div class="form-group <?php echo $classes['invoice_receive_limit_time']['form-group']; ?> mt-2 mb-0">
													<?php echo Form::label('Hora límite de Recepción', 'invoice_receive_limit_time', array('class' => 'form-control-label font-weight-normal mb-0')); ?>
													<?php echo Form::input('invoice_receive_limit_time', $invoice_receive_limit_time, array(
														'id' => 'invoice_receive_limit_time',
														'type' => 'time',
														'class' => 'form-control form-control-sm '.$classes['invoice_receive_limit_time']['form-control'],
														'placeholder' => '15:00'
													)); ?>
												</div>
											</div>
										</div>
									</div>
									<!-- DÍAS DE PAGO Y FRECUENCIA -->
									<div class="col-md-6 mb-3">
										<div class="card border bg-light">
											<div class="card-body py-2 px-3">
												<h6 class="mb-2 font-weight-bold text-primary"><i class="fas fa-calendar-check mr-1"></i> Días de Pago</h6>
												<!-- DÍAS DE LA SEMANA -->
												<label class="mb-1 font-weight-normal">Días de la Semana</label>
												<div class="d-flex flex-wrap mb-2">
													<?php
													$selected_payment_days = (is_array($payment_days) ? $payment_days : explode(',', $payment_days));
													foreach ($weekdays as $day_val => $day_label): ?>
														<div class="form-check mr-3 mb-1">
															<input class="form-check-input" type="checkbox" name="payment_days[]" value="<?php echo $day_val; ?>"
																id="pay_<?php echo $day_val; ?>" <?php echo (in_array($day_val, $selected_payment_days)) ? 'checked' : ''; ?>>
															<label class="form-check-label small" for="pay_<?php echo $day_val; ?>"><?php echo $day_label; ?></label>
														</div>
													<?php endforeach; ?>
												</div>
												<!-- FRECUENCIA Y DÍAS DEL MES -->
												<div class="form-row mb-0">
													<div class="col-md-6">
														<?php echo Form::label('Frecuencia de Pago', 'payment_frequency', array('class' => 'form-control-label font-weight-normal mb-0')); ?>
														<?php echo Form::select('payment_frequency', isset($payment_frequency) ? $payment_frequency : '', [
															''           => 'Seleccione...',
															'weekly'     => 'Semanal',
															'biweekly'   => 'Quincenal',
															'monthly'    => 'Mensual',
														], array('class' => 'form-control form-control-sm')); ?>
													</div>
													<div class="col-md-6">
														<?php echo Form::label('Días del Mes para Pago', 'payment_days_of_month', array('class' => 'form-control-label font-weight-normal mb-0')); ?>
														<?php echo Form::input('payment_days_of_month', isset($payment_days_of_month) ? $payment_days_of_month : '', array(
															'class' => 'form-control form-control-sm',
															'placeholder' => 'Ej: 10, 20, 30'
														)); ?>
														<small class="form-text text-muted">Separar por coma si son varios días (ej. 10, 20, 30)</small>
													</div>
												</div>
											</div>
										</div>
									</div>
									<!-- TÉRMINOS DE PAGO (DÍAS HÁBILES) -->
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['payment_terms_days']['form-group']; ?>">
											<?php echo Form::label('Días hábiles para pago después de recepción', 'payment_terms_days', array('class' => 'form-control-label')); ?>
											<?php echo Form::input('payment_terms_days', $payment_terms_days, array(
												'id' => 'payment_terms_days',
												'type' => 'number',
												'class' => 'form-control '.$classes['payment_terms_days']['form-control'],
												'placeholder' => 'Ejemplo: 5'
											)); ?>
										</div>
									</div>
									<!-- CORREO DE CONTACTO -->
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['contact_email']['form-group']; ?>">
											<?php echo Form::label('Correo de Contacto', 'contact_email', array('class' => 'form-control-label')); ?>
											<?php echo Form::input('contact_email', $contact_email, array(
												'id' => 'contact_email',
												'class' => 'form-control '.$classes['contact_email']['form-control'],
												'placeholder' => 'correo@dominio.com'
											)); ?>
											<?php if(isset($errors['contact_email'])): ?>
												<div class="invalid-feedback">
													<?php echo $errors['contact_email']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<!-- TELÉFONO DE CONTACTO -->
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['contact_phone']['form-group']; ?>">
											<?php echo Form::label('Teléfono de Contacto', 'contact_phone', array('class' => 'form-control-label')); ?>
											<?php echo Form::input('contact_phone', $contact_phone, array(
												'id' => 'contact_phone',
												'class' => 'form-control '.$classes['contact_phone']['form-control'],
												'placeholder' => 'Teléfono de contacto'
											)); ?>
										</div>
									</div>
									<!-- MENSAJE O AVISO GLOBAL -->
									<div class="col-md-12 mb-3">
										<div class="form-group <?php echo $classes['announcement_message']['form-group']; ?>">
											<?php echo Form::label('Mensaje o Aviso Global', 'announcement_message', array('class' => 'form-control-label')); ?>
											<?php echo Form::textarea('announcement_message', $announcement_message, array(
												'id' => 'announcement_message',
												'class' => 'form-control '.$classes['announcement_message']['form-control'],
												'rows' => 2,
												'placeholder' => 'Escriba un mensaje o aviso importante para mostrar a proveedores o administradores'
											)); ?>
										</div>
									</div>
									<!-- BLOQUEO DE RECEPCIÓN DE FACTURAS -->
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['blocked_reception']['form-group']; ?>">
											<?php echo Form::label('Bloquear Recepción de Facturas', 'blocked_reception', array('class' => 'form-control-label')); ?>
											<?php echo Form::select('blocked_reception', $blocked_reception, array(0=>'No', 1=>'Sí'), array(
												'id' => 'blocked_reception',
												'class' => 'form-control '.$classes['blocked_reception']['form-control']
											)); ?>
										</div>
									</div>
									<!-- DÍAS FERIADOS -->
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['holidays']['form-group']; ?>">
											<?php echo Form::label('Días Feriados', 'holidays', array('class' => 'form-control-label')); ?>
											<?php echo Form::input('holidays', $holidays, array(
												'id' => 'holidays',
												'class' => 'form-control '.$classes['holidays']['form-control'],
												'placeholder' => 'Ejemplo: 2025-01-01,2025-05-01'
											)); ?>
											<small class="form-text text-muted">Separar las fechas por coma, formato YYYY-MM-DD.</small>
										</div>
									</div>
									<!-- ARCHIVO DE POLÍTICAS (PDF) -->
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['policy_file']['form-group']; ?>">
											<?php echo Form::label('Archivo de Políticas (PDF)', 'policy_file', array('class' => 'form-control-label', 'for' => 'policy_file')); ?>
											<div class="custom-file">
												<?php echo Form::input('policy_file', '', array(
													'id'    => 'policy_file',
													'type'  => 'file',
													'class' => 'custom-file-input '.$classes['policy_file']['form-control'],
													'lang'  => 'es',
													'accept'=> 'application/pdf'
												)); ?>
												<label class="custom-file-label" for="policy_file">Archivo en formato PDF (Máximo 20MB)</label>
											</div>
											<?php if (!empty($policy_file)): ?>
												<a href="<?php echo Uri::create('uploads/config/'.$policy_file); ?>" target="_blank" class="btn btn-link mt-2">Ver archivo actual</a>
											<?php endif; ?>
											<?php if(isset($errors['policy_file'])): ?>
												<div class="invalid-feedback">
													<?php echo $errors['policy_file']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
								</div>
							</fieldset>


							<div class="text-right">
								<?php echo Form::submit(array('value'=> 'Guardar Cambios', 'name'=>'submit', 'class' => 'btn btn-primary')); ?>
							</div>
						<?php echo Form::close(); ?>
					</div><!-- card-body -->
				</div><!-- card -->
			</div>
		</div>
	</div>
</div>
