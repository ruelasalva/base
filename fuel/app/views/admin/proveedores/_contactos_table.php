<!-- TAB: CONTACTOS (VERSIÓN TABLA MODERNA) -->
<div class="card shadow-lg border-0 mb-4">
	<div class="card-header" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border: none;">
		<div class="row align-items-center py-2">
			<div class="col">
				<h3 class="mb-0 text-white font-weight-bold">
					<i class="fas fa-address-book mr-2"></i> Contactos del Proveedor
				</h3>
				<small class="text-white-50">Gestiona los contactos y sus accesos al portal</small>
			</div>
			<div class="col text-right">
				<button class="btn btn-light btn-sm shadow-sm btn-add-contacto-provider" data-provider-id="<?php echo $provider_id; ?>">
					<i class="fa fa-plus mr-1"></i> Agregar Contacto
				</button>
			</div>
		</div>
	</div>

	<div class="card-body p-0">
		<?php if (!empty($contact)): ?>
		<div class="table-responsive">
			<table class="table table-hover mb-0" style="font-size: 0.95rem;">
				<thead style="background: #f8f9fe;">
					<tr>
						<th class="border-0 py-3 px-4" style="width: 50px; color: #6c757d; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">#</th>
						<th class="border-0 py-3 px-4" style="color: #6c757d; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">Contacto</th>
						<th class="border-0 py-3 px-4" style="color: #6c757d; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">Datos de Contacto</th>
						<th class="border-0 py-3 px-4" style="color: #6c757d; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">Departamentos</th>
						<th class="border-0 py-3 px-4 text-center" style="color: #6c757d; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">Estado de Acceso</th>
						<th class="border-0 py-3 px-4 text-center" style="width: 200px; color: #6c757d; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">Acciones</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($contact as $index => $c): ?>
					<tr style="border-left: 3px solid <?php echo $c->has_user() ? '#22c55e' : '#e5e7eb'; ?>;">
						<!-- NÚMERO -->
						<td class="align-middle px-4 py-4">
							<div class="d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border-radius: 8px;">
								<span class="text-white font-weight-bold" style="font-size: 0.875rem;"><?php echo ($index + 1); ?></span>
							</div>
						</td>

						<!-- NOMBRE COMPLETO -->
						<td class="align-middle px-4 py-4">
							<div class="d-flex align-items-center">
								<div class="d-flex align-items-center justify-content-center mr-3" style="width: 48px; height: 48px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border-radius: 12px; flex-shrink: 0;">
									<span class="text-white font-weight-bold" style="font-size: 1.25rem;"><?php echo strtoupper(substr($c->name, 0, 1)); ?></span>
								</div>
								<div>
									<h5 class="mb-1 font-weight-bold" style="color: #000000; font-size: 1rem;"><?php echo $c->get_full_name(); ?></h5>
									<small class="text-muted" style="font-size: 0.8125rem;">
										<i class="fas fa-id-badge mr-1" style="color: #6c757d;"></i> ID: <?php echo $c->idcontact; ?>
									</small>
								</div>
							</div>
						</td>

						<!-- CONTACTO -->
						<td class="align-middle px-4 py-4">
							<div class="mb-2">
								<i class="fas fa-envelope mr-2" style="color: #6366f1; font-size: 0.875rem;"></i>
								<a href="mailto:<?php echo $c->email; ?>" class="text-decoration-none" style="color: #000000; font-size: 0.875rem;">
									<?php echo $c->email; ?>
								</a>
							</div>
							<?php if ($c->phone): ?>
							<div class="mb-1">
								<i class="fas fa-phone mr-2" style="color: #22c55e; font-size: 0.875rem;"></i>
								<span style="color: #000000; font-size: 0.875rem;"><?php echo $c->phone; ?></span>
							</div>
							<?php endif; ?>
							<?php if ($c->cel): ?>
							<div>
								<i class="fas fa-mobile-alt mr-2" style="color: #3b82f6; font-size: 0.875rem;"></i>
								<span style="color: #000000; font-size: 0.875rem;"><?php echo $c->cel; ?></span>
							</div>
							<?php endif; ?>
						</td>

						<!-- DEPARTAMENTOS -->
						<td class="align-middle px-4 py-4">
							<?php if (!empty($c->departments)): ?>
								<?php 
								$depts = explode(',', $c->departments);
								foreach ($depts as $dept): 
								?>
									<span class="badge mr-1 mb-1 px-3 py-2" style="background: #e0e7ff; color: #000000; font-weight: 600; font-size: 0.75rem; border-radius: 6px;">
										<?php echo trim($dept); ?>
									</span>
								<?php endforeach; ?>
							<?php else: ?>
								<span style="color: #000000; font-size: 0.875rem;">Sin departamentos</span>
							<?php endif; ?>
						</td>

						<!-- ESTADO DE USUARIO -->
						<td class="align-middle px-4 py-4 text-center">
							<?php if ($c->has_user() && $c->user): ?>
								<div class="mb-3">
									<span class="badge px-3 py-2" style="background: #dcfce7; color: #166534; font-weight: 600; font-size: 0.8125rem; border-radius: 8px;">
										<i class="fas fa-check-circle mr-1"></i> Con Acceso al Portal
									</span>
								</div>
								<div class="p-3" style="background: #f8fafc; border-radius: 8px; border-left: 3px solid #22c55e;">
									<div class="mb-2">
										<small class="text-muted d-block mb-1" style="font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Usuario</small>
										<strong style="color: #000000; font-size: 0.875rem;"><?php echo $c->user->username; ?></strong>
									</div>
									<div>
										<small class="text-muted d-block mb-1" style="font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Estado</small>
										<?php if ($c->user->is_active): ?>
											<span class="badge px-2 py-1" style="background: #22c55e; color: white; font-size: 0.7rem; border-radius: 4px;">
												<i class="fas fa-circle mr-1" style="font-size: 0.5rem;"></i> Activo
											</span>
										<?php else: ?>
											<span class="badge px-2 py-1" style="background: #ef4444; color: white; font-size: 0.7rem; border-radius: 4px;">
												<i class="fas fa-circle mr-1" style="font-size: 0.5rem;"></i> Inactivo
											</span>
										<?php endif; ?>
									</div>
								</div>
							<?php else: ?>
								<span class="badge px-3 py-2" style="background: #e5e7eb; color: #000000; font-weight: 600; font-size: 0.8125rem; border-radius: 8px;">
									<i class="fas fa-user-slash mr-1"></i> Sin Acceso
								</span>
								<div class="mt-2">
									<small style="color: #000000; font-size: 0.75rem;">No puede ingresar al portal</small>
								</div>
							<?php endif; ?>
						</td>

						<!-- ACCIONES -->
						<td class="align-middle px-4 py-4 text-center">
							<div class="btn-group-vertical btn-group-sm w-100" role="group" style="gap: 8px;">
			<!-- Editar Contacto -->
			<button class="btn btn-sm shadow-sm btn-edit-contacto-provider" 
					data-id="<?php echo $c->id; ?>" 
					data-provider-id="<?php echo $provider_id; ?>"
					style="background: #6366f1; color: white; border: none; border-radius: 6px; padding: 8px 16px; font-weight: 500; font-size: 0.8125rem;">
				<i class="fa fa-edit mr-1"></i> Editar
			</button>								<?php if ($c->has_user()): ?>
					<!-- Ya tiene usuario: Gestionar Backends -->
					<a href="<?php echo Uri::create('admin/proveedores/manage_contact_tenants/'.$c->id); ?>" 
					   class="btn btn-sm shadow-sm"
					   style="background: #f59e0b; color: white; border: none; border-radius: 6px; padding: 8px 16px; font-weight: 500; font-size: 0.8125rem; text-decoration: none; display: block;">
						<i class="fas fa-network-wired mr-1"></i> Backends
					</a>									<!-- Eliminar Usuario -->
								<button class="btn btn-sm shadow-sm btn-delete-contact-user" 
										data-contact-id="<?php echo $c->id; ?>"
										data-provider-id="<?php echo $provider_id; ?>"
										style="background: #ef4444; color: white; border: none; border-radius: 6px; padding: 8px 16px; font-weight: 500; font-size: 0.8125rem;">
									<i class="fas fa-user-times mr-1"></i> Eliminar Usuario
								</button>
								<?php else: ?>
									<!-- No tiene usuario: Crear -->
								<button class="btn btn-sm shadow-sm btn-create-contact-user"
										data-contact-id="<?php echo $c->id; ?>"
										data-contact-name="<?php echo $c->get_full_name(); ?>"
										data-contact-email="<?php echo $c->email; ?>"
										data-contact-phone="<?php echo $c->phone; ?>"
										style="background: #22c55e; color: white; border: none; border-radius: 6px; padding: 10px 16px; font-weight: 500; font-size: 0.8125rem;">
									<i class="fas fa-user-plus mr-1"></i> Crear Usuario
								</button>
								<?php endif; ?>
							</div>

							<?php if (!empty($c->updated_at)): ?>
							<div class="mt-3 pt-3" style="border-top: 1px solid #e5e7eb;">
							<small style="color: #000000; font-size: 0.75rem;">
								<i class="far fa-clock mr-1"></i>
									<?php echo date('d/m/Y H:i', $c->updated_at); ?>
								</small>
							</div>
							<?php endif; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php else: ?>
		<div class="text-center py-5" style="background: linear-gradient(to bottom, #f8f9fe 0%, white 100%);">
			<div class="mb-4" style="width: 80px; height: 80px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border-radius: 20px; display: inline-flex; align-items: center; justify-content: center;">
				<i class="fas fa-user-times fa-2x text-white"></i>
			</div>
		<h4 class="font-weight-bold mb-2" style="color: #000000;">No hay contactos registrados</h4>
		<p class="mb-4" style="color: #000000; font-size: 0.9375rem;">Agrega el primer contacto para este proveedor</p>
			<button class="btn shadow-sm btn-add-contacto-provider" data-provider-id="<?php echo $provider_id; ?>" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; border: none; border-radius: 8px; padding: 12px 32px; font-weight: 600; font-size: 0.9375rem;">
				<i class="fa fa-plus mr-2"></i> Agregar Primer Contacto
			</button>
		</div>
		<?php endif; ?>
	</div>
</div>
