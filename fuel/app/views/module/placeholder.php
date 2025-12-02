<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default" style="border-color: <?php echo htmlspecialchars($module_info['color'], ENT_QUOTES, 'UTF-8'); ?>;">
			<div class="panel-heading text-center" style="background: <?php echo htmlspecialchars($module_info['color'], ENT_QUOTES, 'UTF-8'); ?>; color: white; padding: 30px;">
				<span class="glyphicon glyphicon-<?php echo htmlspecialchars($module_info['icon'], ENT_QUOTES, 'UTF-8'); ?>" style="font-size: 64px;"></span>
				<h2 style="margin-top: 15px; margin-bottom: 0;">
					<?php echo htmlspecialchars($module_info['name'], ENT_QUOTES, 'UTF-8'); ?>
				</h2>
			</div>
			<div class="panel-body">
				<div class="alert alert-info text-center">
					<span class="glyphicon glyphicon-info-sign"></span>
					<strong>Módulo en Desarrollo</strong>
				</div>

				<p class="lead text-center">
					<?php echo htmlspecialchars($module_info['description'], ENT_QUOTES, 'UTF-8'); ?>
				</p>

				<hr>

				<h4><span class="glyphicon glyphicon-list"></span> Características Planificadas</h4>
				<ul class="list-group">
					<?php foreach ($module_info['features'] as $feature): ?>
					<li class="list-group-item">
						<span class="glyphicon glyphicon-check text-success"></span>
						<?php echo htmlspecialchars($feature, ENT_QUOTES, 'UTF-8'); ?>
					</li>
					<?php endforeach; ?>
				</ul>

				<hr>

				<div class="alert alert-warning">
					<h4><span class="glyphicon glyphicon-wrench"></span> ¿Por qué ve esta página?</h4>
					<p>Este módulo es parte del sistema ERP Multi-tenant y está actualmente en desarrollo. 
					Pronto estará disponible con todas las funcionalidades listadas arriba.</p>
					<p>Si desea contribuir al desarrollo o tiene preguntas, por favor contacte al equipo de desarrollo.</p>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="panel panel-default">
							<div class="panel-heading">
								<span class="glyphicon glyphicon-time"></span> Estado
							</div>
							<div class="panel-body">
								<p><strong>Módulo:</strong> <?php echo htmlspecialchars($module_key, ENT_QUOTES, 'UTF-8'); ?></p>
								<p><strong>Estado:</strong> <span class="label label-warning">En Desarrollo</span></p>
								<p><strong>Prioridad:</strong> <span class="label label-info">Media</span></p>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="panel panel-default">
							<div class="panel-heading">
								<span class="glyphicon glyphicon-cog"></span> Configuración
							</div>
							<div class="panel-body">
								<p>Este módulo se cargará desde:</p>
								<code>fuel/packages_tenant/<?php echo htmlspecialchars($module_key, ENT_QUOTES, 'UTF-8'); ?>/</code>
								<p class="text-muted small" style="margin-top: 10px;">
									Los módulos personalizados se ubican en el directorio packages_tenant.
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel-footer text-center">
				<a href="<?php echo Uri::base(); ?>" class="btn btn-default">
					<span class="glyphicon glyphicon-home"></span> Volver al Inicio
				</a>
				<a href="<?php echo Uri::base(); ?>install" class="btn btn-primary">
					<span class="glyphicon glyphicon-cog"></span> Ir al Instalador
				</a>
			</div>
		</div>
	</div>
</div>
