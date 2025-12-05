<div class="row">
	<div class="col-12">
		<!-- Header con acciones -->
		<div class="card shadow-sm mb-4">
			<div class="card-body">
				<div class="row align-items-center">
					<div class="col">
						<h2 class="mb-1">
							<i class="fas fa-building text-primary"></i>
							<?php echo htmlspecialchars($provider->company_name, ENT_QUOTES, 'UTF-8'); ?>
						</h2>
						<div class="d-flex gap-2 align-items-center">
							<span class="badge bg-secondary"><?php echo htmlspecialchars($provider->code, ENT_QUOTES, 'UTF-8'); ?></span>
							<?php if ($provider->is_suspended): ?>
								<span class="badge bg-warning text-dark">
									<i class="fas fa-pause-circle"></i> Suspendido
								</span>
							<?php elseif ($provider->is_active): ?>
								<span class="badge bg-success">
									<i class="fas fa-check-circle"></i> Activo
								</span>
							<?php else: ?>
								<span class="badge bg-danger">
									<i class="fas fa-times-circle"></i> Inactivo
								</span>
							<?php endif; ?>
						</div>
					</div>
					<div class="col-auto">
						<div class="btn-group">
							<a href="<?php echo Uri::create('admin/proveedores/edit/' . $provider->id); ?>" class="btn btn-primary">
								<i class="fas fa-edit"></i> Editar
							</a>
							<?php if ($provider->is_suspended): ?>
								<form method="post" action="<?php echo Uri::create('admin/proveedores/activate/' . $provider->id); ?>" style="display: inline;">
									<button type="submit" class="btn btn-success">
										<i class="fas fa-play-circle"></i> Activar
									</button>
								</form>
							<?php else: ?>
								<button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#suspendModal">
									<i class="fas fa-pause-circle"></i> Suspender
								</button>
							<?php endif; ?>
							<a href="<?php echo Uri::create('admin/proveedores'); ?>" class="btn btn-secondary">
								<i class="fas fa-arrow-left"></i> Volver
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Tabs de Información -->
		<div class="card shadow-sm">
			<div class="card-header bg-white border-0">
				<ul class="nav nav-tabs card-header-tabs" role="tablist">
					<li class="nav-item" role="presentation">
						<button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-general" type="button">
							<i class="fas fa-info-circle"></i> Información General
						</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-contacto" type="button">
							<i class="fas fa-address-card"></i> Contacto
						</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-financiero" type="button">
							<i class="fas fa-dollar-sign"></i> Información Financiera
						</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-notas" type="button">
							<i class="fas fa-sticky-note"></i> Notas
						</button>
					</li>
				</ul>
			</div>

			<div class="card-body">
				<div class="tab-content">
					<!-- Información General -->
					<div class="tab-pane fade show active" id="tab-general" role="tabpanel">
						<div class="row">
							<div class="col-md-6">
								<div class="card bg-light mb-3">
									<div class="card-body">
										<h5 class="card-title border-bottom pb-2 mb-3">
											<i class="fas fa-building text-primary"></i> Datos de la Empresa
										</h5>
										<table class="table table-sm table-borderless mb-0">
											<tr>
												<th width="150">Código:</th>
												<td><span class="badge bg-secondary"><?php echo htmlspecialchars($provider->code, ENT_QUOTES, 'UTF-8'); ?></span></td>
											</tr>
											<tr>
												<th>Razón Social:</th>
												<td><strong><?php echo htmlspecialchars($provider->company_name, ENT_QUOTES, 'UTF-8'); ?></strong></td>
											</tr>
											<tr>
												<th>RFC:</th>
												<td><span class="font-monospace"><?php echo htmlspecialchars($provider->tax_id, ENT_QUOTES, 'UTF-8'); ?></span></td>
											</tr>
											<?php if (!empty($provider->website)): ?>
												<tr>
													<th>Sitio Web:</th>
													<td>
														<a href="<?php echo htmlspecialchars($provider->website, ENT_QUOTES, 'UTF-8'); ?>" target="_blank">
															<?php echo htmlspecialchars($provider->website, ENT_QUOTES, 'UTF-8'); ?>
															<i class="fas fa-external-link-alt"></i>
														</a>
													</td>
												</tr>
											<?php endif; ?>
										</table>
									</div>
								</div>
							</div>

							<div class="col-md-6">
								<div class="card bg-light mb-3">
									<div class="card-body">
										<h5 class="card-title border-bottom pb-2 mb-3">
											<i class="fas fa-map-marker-alt text-danger"></i> Dirección
										</h5>
										<?php if (!empty($provider->address) || !empty($provider->city)): ?>
											<table class="table table-sm table-borderless mb-0">
												<?php if (!empty($provider->address)): ?>
													<tr>
														<th width="80">Dirección:</th>
														<td><?php echo nl2br(htmlspecialchars($provider->address, ENT_QUOTES, 'UTF-8')); ?></td>
													</tr>
												<?php endif; ?>
												<?php if (!empty($provider->city)): ?>
													<tr>
														<th>Ciudad:</th>
														<td><?php echo htmlspecialchars($provider->city, ENT_QUOTES, 'UTF-8'); ?></td>
													</tr>
												<?php endif; ?>
												<?php if (!empty($provider->state)): ?>
													<tr>
														<th>Estado:</th>
														<td><?php echo htmlspecialchars($provider->state, ENT_QUOTES, 'UTF-8'); ?></td>
													</tr>
												<?php endif; ?>
												<?php if (!empty($provider->postal_code)): ?>
													<tr>
														<th>C.P.:</th>
														<td><?php echo htmlspecialchars($provider->postal_code, ENT_QUOTES, 'UTF-8'); ?></td>
													</tr>
												<?php endif; ?>
												<?php if (!empty($provider->country)): ?>
													<tr>
														<th>País:</th>
														<td><?php echo htmlspecialchars($provider->country, ENT_QUOTES, 'UTF-8'); ?></td>
													</tr>
												<?php endif; ?>
											</table>
										<?php else: ?>
											<p class="text-muted mb-0"><i class="fas fa-info-circle"></i> Sin dirección registrada</p>
										<?php endif; ?>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-12">
								<div class="card bg-light">
									<div class="card-body">
										<h5 class="card-title border-bottom pb-2 mb-3">
											<i class="fas fa-clock text-info"></i> Historial
										</h5>
										<div class="row">
											<div class="col-md-6">
												<small class="text-muted">Fecha de Registro:</small><br>
												<strong><?php echo date('d/m/Y H:i', strtotime($provider->created_at)); ?></strong>
											</div>
											<div class="col-md-6">
												<small class="text-muted">Última Actualización:</small><br>
												<strong><?php echo date('d/m/Y H:i', strtotime($provider->updated_at)); ?></strong>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Contacto -->
					<div class="tab-pane fade" id="tab-contacto" role="tabpanel">
						<div class="card bg-light">
							<div class="card-body">
								<h5 class="card-title border-bottom pb-2 mb-3">
									<i class="fas fa-address-card text-success"></i> Información de Contacto
								</h5>
								<table class="table table-sm table-borderless mb-0">
									<tr>
										<th width="200">Nombre de Contacto:</th>
										<td>
											<?php if (!empty($provider->contact_name)): ?>
												<i class="fas fa-user text-muted"></i>
												<?php echo htmlspecialchars($provider->contact_name, ENT_QUOTES, 'UTF-8'); ?>
											<?php else: ?>
												<span class="text-muted">No especificado</span>
											<?php endif; ?>
										</td>
									</tr>
									<tr>
										<th>Email:</th>
										<td>
											<?php if (!empty($provider->email)): ?>
												<i class="fas fa-envelope text-muted"></i>
												<a href="mailto:<?php echo htmlspecialchars($provider->email, ENT_QUOTES, 'UTF-8'); ?>">
													<?php echo htmlspecialchars($provider->email, ENT_QUOTES, 'UTF-8'); ?>
												</a>
											<?php else: ?>
												<span class="text-muted">No especificado</span>
											<?php endif; ?>
										</td>
									</tr>
									<tr>
										<th>Teléfono Principal:</th>
										<td>
											<?php if (!empty($provider->phone)): ?>
												<i class="fas fa-phone text-muted"></i>
												<a href="tel:<?php echo htmlspecialchars($provider->phone, ENT_QUOTES, 'UTF-8'); ?>">
													<?php echo htmlspecialchars($provider->phone, ENT_QUOTES, 'UTF-8'); ?>
												</a>
											<?php else: ?>
												<span class="text-muted">No especificado</span>
											<?php endif; ?>
										</td>
									</tr>
									<tr>
										<th>Teléfono Secundario:</th>
										<td>
											<?php if (!empty($provider->phone_secondary)): ?>
												<i class="fas fa-phone text-muted"></i>
												<a href="tel:<?php echo htmlspecialchars($provider->phone_secondary, ENT_QUOTES, 'UTF-8'); ?>">
													<?php echo htmlspecialchars($provider->phone_secondary, ENT_QUOTES, 'UTF-8'); ?>
												</a>
											<?php else: ?>
												<span class="text-muted">No especificado</span>
											<?php endif; ?>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>

					<!-- Información Financiera -->
					<div class="tab-pane fade" id="tab-financiero" role="tabpanel">
						<div class="row">
							<div class="col-md-6">
								<div class="card bg-light">
									<div class="card-body">
										<h5 class="card-title border-bottom pb-2 mb-3">
											<i class="fas fa-calendar-alt text-warning"></i> Términos de Pago
										</h5>
										<p class="mb-0">
											<?php if (!empty($provider->payment_terms)): ?>
												<span class="badge bg-info fs-6">
													<?php echo htmlspecialchars($provider->payment_terms, ENT_QUOTES, 'UTF-8'); ?>
												</span>
											<?php else: ?>
												<span class="text-muted">No especificado</span>
											<?php endif; ?>
										</p>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="card bg-light">
									<div class="card-body">
										<h5 class="card-title border-bottom pb-2 mb-3">
											<i class="fas fa-credit-card text-success"></i> Límite de Crédito
										</h5>
										<p class="mb-0">
											<?php if ($provider->credit_limit > 0): ?>
												<span class="badge bg-success fs-6">
													$ <?php echo number_format($provider->credit_limit, 2); ?> MXN
												</span>
											<?php else: ?>
												<span class="badge bg-secondary fs-6">Sin límite</span>
											<?php endif; ?>
										</p>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Notas -->
					<div class="tab-pane fade" id="tab-notas" role="tabpanel">
						<div class="card bg-light">
							<div class="card-body">
								<h5 class="card-title border-bottom pb-2 mb-3">
									<i class="fas fa-sticky-note text-warning"></i> Notas Internas
								</h5>
								<?php if (!empty($provider->notes)): ?>
									<div class="bg-white p-3 rounded">
										<?php echo nl2br(htmlspecialchars($provider->notes, ENT_QUOTES, 'UTF-8')); ?>
									</div>
								<?php else: ?>
									<p class="text-muted mb-0">
										<i class="fas fa-info-circle"></i> No hay notas registradas para este proveedor
									</p>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Alertas de Suspensión -->
		<?php if ($provider->is_suspended && !empty($provider->suspended_reason)): ?>
			<div class="card border-warning mt-4">
				<div class="card-body">
					<h5 class="text-warning">
						<i class="fas fa-exclamation-triangle"></i> Proveedor Suspendido
					</h5>
					<p class="mb-2"><strong>Razón:</strong></p>
					<p class="mb-2"><?php echo nl2br(htmlspecialchars($provider->suspended_reason, ENT_QUOTES, 'UTF-8')); ?></p>
					<?php if (!empty($provider->suspended_at)): ?>
						<small class="text-muted">
							Suspendido el: <?php echo date('d/m/Y H:i', strtotime($provider->suspended_at)); ?>
						</small>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>

<!-- Modal de Suspensión -->
<div class="modal fade" id="suspendModal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-warning">
				<h5 class="modal-title">
					<i class="fas fa-pause-circle"></i> Suspender Proveedor
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<form method="post" action="<?php echo Uri::create('admin/proveedores/suspend/' . $provider->id); ?>">
				<div class="modal-body">
					<p>¿Estás seguro de que deseas suspender a <strong><?php echo htmlspecialchars($provider->company_name, ENT_QUOTES, 'UTF-8'); ?></strong>?</p>
					<div class="mb-3">
						<label class="form-label required">Razón de la suspensión:</label>
						<textarea name="reason" class="form-control" rows="4" required placeholder="Explica el motivo de la suspensión..."></textarea>
						<small class="text-muted">Esta información será registrada y visible en el historial.</small>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-warning">
						<i class="fas fa-pause-circle"></i> Suspender
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<style>
.nav-tabs .nav-link {
	color: #6c757d;
}

.nav-tabs .nav-link.active {
	font-weight: 600;
}

.table th {
	font-weight: 600;
	color: #6c757d;
}

.font-monospace {
	font-family: 'Courier New', monospace;
}

.card {
	border-radius: 0.5rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
	// Inicializar tabs
	if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
		var triggerTabList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tab"]'))
		triggerTabList.forEach(function (triggerEl) {
			new bootstrap.Tab(triggerEl)
		})
	}
});
</script>
