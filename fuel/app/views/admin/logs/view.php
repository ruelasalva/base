<div class="animated fadeIn">
	<div class="row">
		<div class="col-lg-8">
			<div class="card">
				<div class="card-header">
					<i class="fa fa-file-alt"></i> <?php echo $title; ?>
				</div>
				<div class="card-body">
					<div class="row mb-3">
						<div class="col-md-6">
							<dl class="row">
								<dt class="col-sm-4">ID:</dt>
								<dd class="col-sm-8"><code>#<?php echo $log['id']; ?></code></dd>

								<dt class="col-sm-4">Fecha/Hora:</dt>
								<dd class="col-sm-8">
									<?php echo date('d/m/Y H:i:s', strtotime($log['created_at'])); ?>
								</dd>

								<dt class="col-sm-4">Usuario:</dt>
								<dd class="col-sm-8">
									<strong><?php echo $log['username']; ?></strong>
									<?php if ($user): ?>
										<br>
										<small class="text-muted">
											<?php echo $user['first_name'] . ' ' . $user['last_name']; ?><br>
											<?php echo $user['email']; ?>
										</small>
									<?php endif; ?>
								</dd>

								<dt class="col-sm-4">Módulo:</dt>
								<dd class="col-sm-8">
									<span class="badge badge-primary"><?php echo $log['module']; ?></span>
								</dd>

								<dt class="col-sm-4">Acción:</dt>
								<dd class="col-sm-8">
									<?php
										$action_colors = [
											'create' => 'success',
											'edit' => 'info',
											'delete' => 'danger',
											'view' => 'secondary',
											'login_success' => 'success',
											'login_failed' => 'danger',
											'logout' => 'warning'
										];
										$color = isset($action_colors[$log['action']]) ? $action_colors[$log['action']] : 'dark';
									?>
									<span class="badge badge-<?php echo $color; ?>"><?php echo $log['action']; ?></span>
								</dd>

								<dt class="col-sm-4">Registro ID:</dt>
								<dd class="col-sm-8">
									<?php echo $log['record_id'] ? '<code>#' . $log['record_id'] . '</code>' : '<em class="text-muted">N/A</em>'; ?>
								</dd>
							</dl>
						</div>

						<div class="col-md-6">
							<dl class="row">
								<dt class="col-sm-4">IP Address:</dt>
								<dd class="col-sm-8">
									<code><?php echo $log['ip_address']; ?></code>
								</dd>

								<dt class="col-sm-4">User Agent:</dt>
								<dd class="col-sm-8">
									<small class="text-muted">
										<?php 
											$ua = $log['user_agent'];
											if (strlen($ua) > 100) {
												echo substr($ua, 0, 100) . '...';
											} else {
												echo $ua;
											}
										?>
									</small>
								</dd>
							</dl>
						</div>
					</div>

					<hr>

					<div class="row">
						<div class="col-md-12">
							<h6>Descripción:</h6>
							<div class="alert alert-light">
								<?php echo $log['description'] ?: '<em class="text-muted">Sin descripción</em>'; ?>
							</div>
						</div>
					</div>

					<?php if ($log['old_data']): ?>
						<div class="row mt-3">
							<div class="col-md-12">
								<h6>Datos Anteriores:</h6>
								<div class="card bg-light">
									<div class="card-body">
										<?php if (isset($log['old_data_decoded']) && is_array($log['old_data_decoded'])): ?>
											<pre><?php echo json_encode($log['old_data_decoded'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?></pre>
										<?php else: ?>
											<pre><?php echo $log['old_data']; ?></pre>
										<?php endif; ?>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>

					<?php if ($log['new_data']): ?>
						<div class="row mt-3">
							<div class="col-md-12">
								<h6>Datos Nuevos:</h6>
								<div class="card bg-light">
									<div class="card-body">
										<?php if (isset($log['new_data_decoded']) && is_array($log['new_data_decoded'])): ?>
											<pre><?php echo json_encode($log['new_data_decoded'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?></pre>
										<?php else: ?>
											<pre><?php echo $log['new_data']; ?></pre>
										<?php endif; ?>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>

					<hr>

					<a href="<?php echo Uri::create('admin/logs'); ?>" class="btn btn-secondary">
						<i class="fa fa-arrow-left"></i> Volver a Lista
					</a>

					<?php if ($user): ?>
						<a href="<?php echo Uri::create('admin/users/view/' . $user['id']); ?>" class="btn btn-primary">
							<i class="fa fa-user"></i> Ver Usuario
						</a>
					<?php endif; ?>

					<?php if ($log['record_id'] && $log['module'] != 'auth'): ?>
						<a href="<?php echo Uri::create('admin/' . $log['module'] . '/view/' . $log['record_id']); ?>" class="btn btn-info">
							<i class="fa fa-external-link-alt"></i> Ver Registro
						</a>
					<?php endif; ?>
				</div>
			</card>
		</div>

		<div class="col-lg-4">
			<div class="card">
				<div class="card-header">
					<i class="fa fa-info-circle"></i> Información Técnica
				</div>
				<div class="card-body">
					<dl class="mb-0">
						<dt>Tenant ID:</dt>
						<dd><?php echo $log['tenant_id']; ?></dd>

						<dt>User ID:</dt>
						<dd><?php echo $log['user_id'] ?: '<em class="text-muted">N/A</em>'; ?></dd>

						<dt>Timestamp:</dt>
						<dd><code><?php echo strtotime($log['created_at']); ?></code></dd>

						<dt>IP Completa:</dt>
						<dd><code><?php echo $log['ip_address']; ?></code></dd>
					</dl>
				</div>
			</div>

			<div class="card">
				<div class="card-header">
					<i class="fa fa-filter"></i> Filtros Rápidos
				</div>
				<div class="card-body">
					<a href="<?php echo Uri::create('admin/logs?module=' . $log['module']); ?>" class="btn btn-block btn-sm btn-outline-primary mb-2">
						<i class="fa fa-filter"></i> Ver todos de este módulo
					</a>
					<a href="<?php echo Uri::create('admin/logs?action=' . $log['action']); ?>" class="btn btn-block btn-sm btn-outline-info mb-2">
						<i class="fa fa-filter"></i> Ver todas estas acciones
					</a>
					<?php if ($log['user_id']): ?>
						<a href="<?php echo Uri::create('admin/logs?user_id=' . $log['user_id']); ?>" class="btn btn-block btn-sm btn-outline-success">
							<i class="fa fa-filter"></i> Ver todos de este usuario
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
