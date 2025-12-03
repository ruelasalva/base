<div class="animated fadeIn">
	<div class="row">
		<div class="col-lg-8">
			<div class="card">
				<div class="card-header">
					<i class="fa fa-paper-plane"></i> Enviar Notificación
					<div class="card-header-actions">
						<a href="<?php echo Uri::create('admin/notifications'); ?>" class="btn btn-sm btn-secondary">
							<i class="fa fa-arrow-left"></i> Volver
						</a>
					</div>
				</div>
				<div class="card-body">
					<form method="post" action="<?php echo Uri::create('admin/notifications/send'); ?>">
						<!-- Destinatario -->
						<div class="form-group">
							<label for="send_to">Enviar a: <span class="text-danger">*</span></label>
							<select name="send_to" id="send_to" class="form-control" required onchange="toggleRecipient()">
								<option value="">Seleccionar...</option>
								<option value="user">Usuario específico</option>
								<option value="role">Todos los usuarios de un rol</option>
								<option value="all">Todos los usuarios del tenant</option>
							</select>
							<small class="form-text text-muted">Selecciona a quién enviar la notificación</small>
						</div>

						<!-- Usuario específico -->
						<div class="form-group" id="user_select_group" style="display: none;">
							<label for="user_id">Usuario:</label>
							<select name="user_id" id="user_id" class="form-control">
								<option value="">Seleccionar usuario...</option>
								<?php foreach ($users as $user): ?>
									<option value="<?php echo $user['id']; ?>">
										<?php echo $user['username']; ?> (<?php echo $user['email']; ?>)
									</option>
								<?php endforeach; ?>
							</select>
						</div>

						<!-- Rol -->
						<div class="form-group" id="role_select_group" style="display: none;">
							<label for="role_id">Rol:</label>
							<select name="role_id" id="role_id" class="form-control">
								<option value="">Seleccionar rol...</option>
								<?php foreach ($roles as $role): ?>
									<option value="<?php echo $role['id']; ?>">
										<?php echo $role['display_name']; ?> (<?php echo $role['name']; ?>)
									</option>
								<?php endforeach; ?>
							</select>
						</div>

						<!-- Tipo -->
						<div class="form-group">
							<label for="type">Tipo: <span class="text-danger">*</span></label>
							<select name="type" id="type" class="form-control" required>
								<?php foreach ($types as $t): ?>
									<option value="<?php echo $t; ?>" <?php echo $t == 'info' ? 'selected' : ''; ?>>
										<?php echo ucfirst($t); ?>
									</option>
								<?php endforeach; ?>
							</select>
							<small class="form-text text-muted">
								<span class="badge badge-info">info</span> Información general | 
								<span class="badge badge-success">success</span> Éxito | 
								<span class="badge badge-warning">warning</span> Advertencia | 
								<span class="badge badge-danger">danger</span> Error/Urgente | 
								<span class="badge badge-secondary">system</span> Sistema
							</small>
						</div>

						<!-- Título -->
						<div class="form-group">
							<label for="title">Título: <span class="text-danger">*</span></label>
							<input type="text" name="title" id="title" class="form-control" required maxlength="255" placeholder="Título de la notificación">
							<small class="form-text text-muted">Máximo 255 caracteres</small>
						</div>

						<!-- Mensaje -->
						<div class="form-group">
							<label for="message">Mensaje: <span class="text-danger">*</span></label>
							<textarea name="message" id="message" class="form-control" rows="5" required placeholder="Contenido de la notificación"></textarea>
							<small class="form-text text-muted">Puedes usar saltos de línea</small>
						</div>

						<!-- Enlace (opcional) -->
						<div class="form-group">
							<label for="link">Enlace (opcional):</label>
							<input type="url" name="link" id="link" class="form-control" placeholder="https://...">
							<small class="form-text text-muted">URL a la que se redirigirá al hacer clic en la notificación</small>
						</div>

						<!-- Botones -->
						<div class="form-group">
							<button type="submit" class="btn btn-primary">
								<i class="fa fa-paper-plane"></i> Enviar Notificación
							</button>
							<a href="<?php echo Uri::create('admin/notifications'); ?>" class="btn btn-secondary">
								<i class="fa fa-times"></i> Cancelar
							</a>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!-- Sidebar con ayuda -->
		<div class="col-lg-4">
			<div class="card">
				<div class="card-header">
					<i class="fa fa-info-circle"></i> Ayuda
				</div>
				<div class="card-body">
					<h6>Tipos de Notificación:</h6>
					<ul class="small">
						<li><strong>Info:</strong> Información general, actualizaciones</li>
						<li><strong>Success:</strong> Operaciones exitosas, confirmaciones</li>
						<li><strong>Warning:</strong> Advertencias, acciones pendientes</li>
						<li><strong>Danger:</strong> Errores críticos, problemas urgentes</li>
						<li><strong>System:</strong> Mensajes del sistema, mantenimiento</li>
					</ul>

					<hr>

					<h6>Destinatarios:</h6>
					<ul class="small">
						<li><strong>Usuario específico:</strong> Solo un usuario recibirá la notificación</li>
						<li><strong>Rol:</strong> Todos los usuarios con ese rol la recibirán</li>
						<li><strong>Todos:</strong> Envía a todos los usuarios activos del tenant</li>
					</ul>

					<hr>

					<div class="alert alert-warning small mb-0">
						<i class="fa fa-exclamation-triangle"></i>
						<strong>Importante:</strong> Las notificaciones se envían inmediatamente y no se pueden deshacer.
					</div>
				</div>
			</div>

			<!-- Estadísticas -->
			<div class="card">
				<div class="card-header">
					<i class="fa fa-chart-bar"></i> Estadísticas
				</div>
				<div class="card-body">
					<p class="mb-2">
						<i class="fa fa-users text-primary"></i>
						<strong><?php echo count($users); ?></strong> usuarios activos
					</p>
					<p class="mb-0">
						<i class="fa fa-user-tag text-success"></i>
						<strong><?php echo count($roles); ?></strong> roles disponibles
					</p>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
function toggleRecipient() {
	var sendTo = document.getElementById('send_to').value;
	var userGroup = document.getElementById('user_select_group');
	var roleGroup = document.getElementById('role_select_group');
	var userId = document.getElementById('user_id');
	var roleId = document.getElementById('role_id');
	
	// Ocultar todos
	userGroup.style.display = 'none';
	roleGroup.style.display = 'none';
	
	// Limpiar required
	userId.removeAttribute('required');
	roleId.removeAttribute('required');
	
	// Mostrar según selección
	switch(sendTo) {
		case 'user':
			userGroup.style.display = 'block';
			userId.setAttribute('required', 'required');
			break;
		case 'role':
			roleGroup.style.display = 'block';
			roleId.setAttribute('required', 'required');
			break;
		case 'all':
			// No se necesita nada adicional
			break;
	}
}
</script>
