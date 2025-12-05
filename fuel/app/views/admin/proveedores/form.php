<div class="row">
	<div class="col-12">
		<div class="card shadow-sm">
			<div class="card-header bg-white border-0 py-3">
				<h3 class="mb-0">
					<i class="fas fa-building text-primary"></i> 
					<?php echo isset($provider) && $provider ? 'Editar Proveedor' : 'Nuevo Proveedor'; ?>
				</h3>
			</div>

			<?php echo Form::open(['class' => 'needs-validation', 'novalidate' => 'novalidate']); ?>
			
			<div class="card-body">
				<!-- Tabs -->
				<ul class="nav nav-tabs mb-4" role="tablist">
					<li class="nav-item" role="presentation">
						<button class="nav-link active" data-bs-toggle="tab" data-bs-target="#info-basica" type="button">
							<i class="fas fa-info-circle"></i> Información Básica
						</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" data-bs-toggle="tab" data-bs-target="#direccion" type="button">
							<i class="fas fa-map-marker-alt"></i> Dirección
						</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" data-bs-toggle="tab" data-bs-target="#datos-financieros" type="button">
							<i class="fas fa-dollar-sign"></i> Datos Financieros
						</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" data-bs-toggle="tab" data-bs-target="#notas" type="button">
							<i class="fas fa-sticky-note"></i> Notas
						</button>
					</li>
				</ul>

				<!-- Tab Content -->
				<div class="tab-content">
					<!-- Información Básica -->
					<div class="tab-pane fade show active" id="info-basica" role="tabpanel">
						<div class="row">
							<div class="col-md-4">
								<div class="mb-3">
									<label class="form-label required">Código</label>
									<?php echo Form::input('code', Input::post('code', isset($provider) ? $provider->code : ''), [
										'class' => 'form-control' . (isset($errors['code']) ? ' is-invalid' : ''),
										'required' => 'required',
										'placeholder' => 'Ej: PROV001',
										'maxlength' => '50'
									]); ?>
									<?php if (isset($errors['code'])): ?>
										<div class="invalid-feedback"><?php echo $errors['code']->get_message(); ?></div>
									<?php endif; ?>
									<small class="text-muted">Código único del proveedor</small>
								</div>
							</div>
							<div class="col-md-8">
								<div class="mb-3">
									<label class="form-label required">Razón Social</label>
									<?php echo Form::input('company_name', Input::post('company_name', isset($provider) ? $provider->company_name : ''), [
										'class' => 'form-control' . (isset($errors['company_name']) ? ' is-invalid' : ''),
										'required' => 'required',
										'placeholder' => 'Nombre completo de la empresa',
										'maxlength' => '255'
									]); ?>
									<?php if (isset($errors['company_name'])): ?>
										<div class="invalid-feedback"><?php echo $errors['company_name']->get_message(); ?></div>
									<?php endif; ?>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-4">
								<div class="mb-3">
									<label class="form-label required">RFC</label>
									<?php echo Form::input('tax_id', Input::post('tax_id', isset($provider) ? $provider->tax_id : ''), [
										'class' => 'form-control font-monospace' . (isset($errors['tax_id']) ? ' is-invalid' : ''),
										'required' => 'required',
										'placeholder' => 'ABC123456XYZ',
										'maxlength' => '13',
										'id' => 'tax-id-input',
										'style' => 'text-transform: uppercase;'
									]); ?>
									<?php if (isset($errors['tax_id'])): ?>
										<div class="invalid-feedback"><?php echo $errors['tax_id']->get_message(); ?></div>
									<?php endif; ?>
									<div id="rfc-preview" class="mt-2"></div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="mb-3">
									<label class="form-label">Nombre de Contacto</label>
									<?php echo Form::input('contact_name', Input::post('contact_name', isset($provider) ? $provider->contact_name : ''), [
										'class' => 'form-control',
										'placeholder' => 'Persona de contacto principal',
										'maxlength' => '255'
									]); ?>
								</div>
							</div>
							<div class="col-md-4">
								<div class="mb-3">
									<label class="form-label">Sitio Web</label>
									<div class="input-group">
										<span class="input-group-text"><i class="fas fa-globe"></i></span>
										<?php echo Form::input('website', Input::post('website', isset($provider) ? $provider->website : ''), [
											'class' => 'form-control',
											'placeholder' => 'https://ejemplo.com',
											'maxlength' => '255'
										]); ?>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-4">
								<div class="mb-3">
									<label class="form-label">Email</label>
									<div class="input-group">
										<span class="input-group-text"><i class="fas fa-envelope"></i></span>
										<?php echo Form::input('email', Input::post('email', isset($provider) ? $provider->email : ''), [
											'class' => 'form-control' . (isset($errors['email']) ? ' is-invalid' : ''),
											'type' => 'email',
											'placeholder' => 'contacto@proveedor.com',
											'maxlength' => '255'
										]); ?>
										<?php if (isset($errors['email'])): ?>
											<div class="invalid-feedback"><?php echo $errors['email']->get_message(); ?></div>
										<?php endif; ?>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="mb-3">
									<label class="form-label">Teléfono Principal</label>
									<div class="input-group">
										<span class="input-group-text"><i class="fas fa-phone"></i></span>
										<?php echo Form::input('phone', Input::post('phone', isset($provider) ? $provider->phone : ''), [
											'class' => 'form-control',
											'placeholder' => '(999) 999-9999',
											'maxlength' => '20'
										]); ?>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="mb-3">
									<label class="form-label">Teléfono Secundario</label>
									<div class="input-group">
										<span class="input-group-text"><i class="fas fa-phone"></i></span>
										<?php echo Form::input('phone_secondary', Input::post('phone_secondary', isset($provider) ? $provider->phone_secondary : ''), [
											'class' => 'form-control',
											'placeholder' => '(999) 999-9999',
											'maxlength' => '20'
										]); ?>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Dirección -->
					<div class="tab-pane fade" id="direccion" role="tabpanel">
						<div class="row">
							<div class="col-12">
								<div class="mb-3">
									<label class="form-label">Dirección</label>
									<?php echo Form::textarea('address', Input::post('address', isset($provider) ? $provider->address : ''), [
										'class' => 'form-control',
										'rows' => '3',
										'placeholder' => 'Calle, número, colonia...',
										'maxlength' => '500'
									]); ?>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-4">
								<div class="mb-3">
									<label class="form-label">Ciudad</label>
									<?php echo Form::input('city', Input::post('city', isset($provider) ? $provider->city : ''), [
										'class' => 'form-control',
										'placeholder' => 'Ciudad',
										'maxlength' => '100'
									]); ?>
								</div>
							</div>
							<div class="col-md-4">
								<div class="mb-3">
									<label class="form-label">Estado</label>
									<?php echo Form::select('state', Input::post('state', isset($provider) ? $provider->state : ''), 
										array_merge(['' => '-- Seleccionar --'], array_combine($states, $states)),
										['class' => 'form-select']
									); ?>
								</div>
							</div>
							<div class="col-md-4">
								<div class="mb-3">
									<label class="form-label">Código Postal</label>
									<?php echo Form::input('postal_code', Input::post('postal_code', isset($provider) ? $provider->postal_code : ''), [
										'class' => 'form-control',
										'placeholder' => '00000',
										'maxlength' => '10'
									]); ?>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">País</label>
									<?php echo Form::input('country', Input::post('country', isset($provider) ? $provider->country : 'México'), [
										'class' => 'form-control',
										'maxlength' => '100'
									]); ?>
								</div>
							</div>
						</div>
					</div>

					<!-- Datos Financieros -->
					<div class="tab-pane fade" id="datos-financieros" role="tabpanel">
						<div class="alert alert-info">
							<i class="fas fa-info-circle"></i>
							<strong>Información Financiera:</strong> Términos de pago y límite de crédito para este proveedor.
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">Términos de Pago</label>
									<?php echo Form::select('payment_terms', Input::post('payment_terms', isset($provider) ? $provider->payment_terms : ''), [
										'' => '-- Seleccionar --',
										'Contado' => 'Contado',
										'7 días' => '7 días',
										'15 días' => '15 días',
										'30 días' => '30 días',
										'45 días' => '45 días',
										'60 días' => '60 días',
										'90 días' => '90 días'
									], ['class' => 'form-select']); ?>
									<small class="text-muted">Plazo de pago acordado con el proveedor</small>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">Límite de Crédito</label>
									<div class="input-group">
										<span class="input-group-text">$</span>
										<?php echo Form::input('credit_limit', Input::post('credit_limit', isset($provider) ? number_format($provider->credit_limit, 2, '.', '') : '0.00'), [
											'class' => 'form-control',
											'type' => 'number',
											'step' => '0.01',
											'min' => '0',
											'placeholder' => '0.00'
										]); ?>
										<span class="input-group-text">MXN</span>
									</div>
									<small class="text-muted">Monto máximo de crédito autorizado (0 = sin límite)</small>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-12">
								<div class="card bg-light">
									<div class="card-body">
										<h6 class="card-title"><i class="fas fa-lightbulb text-warning"></i> Consejos</h6>
										<ul class="mb-0 small">
											<li>Los términos de pago se utilizan para calcular fechas de vencimiento automáticas</li>
											<li>El límite de crédito ayuda a controlar el endeudamiento con el proveedor</li>
											<li>Puedes dejar el límite en 0 si no deseas restricciones</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Notas -->
					<div class="tab-pane fade" id="notas" role="tabpanel">
						<div class="mb-3">
							<label class="form-label">
								<i class="fas fa-sticky-note"></i> Notas Adicionales
							</label>
							<?php echo Form::textarea('notes', Input::post('notes', isset($provider) ? $provider->notes : ''), [
								'class' => 'form-control',
								'rows' => '8',
								'placeholder' => 'Información adicional sobre el proveedor, acuerdos especiales, condiciones, etc.'
							]); ?>
							<small class="text-muted">
								<i class="fas fa-info-circle"></i> Estas notas son visibles solo para uso interno
							</small>
						</div>

						<div class="card bg-light">
							<div class="card-body">
								<h6 class="mb-2"><i class="fas fa-question-circle text-info"></i> ¿Qué puedes incluir en las notas?</h6>
								<ul class="small mb-0">
									<li>Acuerdos especiales de precios</li>
									<li>Horarios de entrega preferidos</li>
									<li>Contactos adicionales importantes</li>
									<li>Requisitos especiales de facturación</li>
									<li>Historial de incidencias relevantes</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="card-footer bg-white d-flex justify-content-between py-3">
				<a href="<?php echo Uri::create('admin/proveedores'); ?>" class="btn btn-secondary">
					<i class="fas fa-arrow-left"></i> Cancelar
				</a>
				<button type="submit" class="btn btn-primary">
					<i class="fas fa-save"></i> 
					<?php echo isset($provider) && $provider ? 'Actualizar' : 'Guardar'; ?> Proveedor
				</button>
			</div>

			<?php echo Form::close(); ?>
		</div>
	</div>
</div>

<style>
.required:after {
	content: " *";
	color: #dc3545;
}

.nav-tabs .nav-link {
	color: #6c757d;
}

.nav-tabs .nav-link.active {
	font-weight: 600;
}

.input-group-text {
	min-width: 45px;
	justify-content: center;
}

.font-monospace {
	font-family: 'Courier New', monospace;
}
</style>

<script>
// Inicializar tabs (compatible con Bootstrap 4 y 5)
document.addEventListener('DOMContentLoaded', function() {
	// Método para Bootstrap 5
	if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
		var triggerTabList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tab"]'))
		triggerTabList.forEach(function (triggerEl) {
			new bootstrap.Tab(triggerEl)
		})
	}
	// Método para Bootstrap 4 con jQuery
	else if (typeof $ !== 'undefined' && $.fn.tab) {
		$('[data-bs-toggle="tab"], [data-toggle="tab"]').on('click', function (e) {
			e.preventDefault()
			$(this).tab('show')
		})
	}
	// Método manual si Bootstrap no está disponible
	else {
		var tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]')
		tabButtons.forEach(function(button) {
			button.addEventListener('click', function(e) {
				e.preventDefault()
				
				// Remover active de todos los botones y panes
				var allButtons = document.querySelectorAll('[data-bs-toggle="tab"]')
				var allPanes = document.querySelectorAll('.tab-pane')
				
				allButtons.forEach(function(btn) {
					btn.classList.remove('active')
				})
				allPanes.forEach(function(pane) {
					pane.classList.remove('show', 'active')
				})
				
				// Activar el botón y pane actual
				button.classList.add('active')
				var targetId = button.getAttribute('data-bs-target')
				var targetPane = document.querySelector(targetId)
				if (targetPane) {
					targetPane.classList.add('show', 'active')
				}
			})
		})
	}
	
	// Vista previa de RFC
	var rfcInput = document.getElementById('tax-id-input')
	var rfcPreview = document.getElementById('rfc-preview')
	
	if (rfcInput && rfcPreview) {
		function validateRFC() {
			var rfc = rfcInput.value.toUpperCase()
			rfcInput.value = rfc
			
			if (rfc.length === 0) {
				rfcPreview.innerHTML = ''
				return
			}
			
			if (rfc.length === 12 || rfc.length === 13) {
				rfcPreview.innerHTML = '<span class="badge bg-success"><i class="fas fa-check"></i> RFC válido</span>'
			} else {
				rfcPreview.innerHTML = '<span class="badge bg-warning"><i class="fas fa-exclamation-triangle"></i> RFC debe tener 12 o 13 caracteres</span>'
			}
		}
		
		rfcInput.addEventListener('input', validateRFC)
		validateRFC() // Validar al cargar si hay valor
	}
})

// Validación Bootstrap
(function () {
	'use strict'
	var forms = document.querySelectorAll('.needs-validation')
	Array.prototype.slice.call(forms).forEach(function (form) {
		form.addEventListener('submit', function (event) {
			if (!form.checkValidity()) {
				event.preventDefault()
				event.stopPropagation()
			}
			form.classList.add('was-validated')
		}, false)
	})
})()
</script>
