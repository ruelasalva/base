<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="glyphicon glyphicon-cog"></i>
					<?php echo htmlspecialchars($module_name, ENT_QUOTES, 'UTF-8'); ?>
				</h3>
			</div>
			<div class="panel-body text-center">
				<div style="padding: 40px 20px;">
					<!-- Development Icon -->
					<div style="margin-bottom: 30px;">
						<span class="glyphicon glyphicon-wrench" style="font-size: 72px; color: #667eea;"></span>
					</div>
					
					<!-- Status Badge -->
					<p>
						<span class="label label-warning" style="font-size: 14px; padding: 8px 16px;">
							<i class="glyphicon glyphicon-time"></i>
							En Desarrollo
						</span>
					</p>
					
					<!-- Module Description -->
					<p class="lead" style="margin-top: 20px; color: #666;">
						<?php echo htmlspecialchars($module_description, ENT_QUOTES, 'UTF-8'); ?>
					</p>
					
					<!-- Module Key -->
					<p class="text-muted">
						<small>Módulo: <code><?php echo htmlspecialchars($module_key, ENT_QUOTES, 'UTF-8'); ?></code></small>
					</p>
					
					<!-- Divider -->
					<hr style="margin: 30px 0;">
					
					<!-- Info Message -->
					<div class="alert alert-info" style="text-align: left;">
						<strong><i class="glyphicon glyphicon-info-sign"></i> Información:</strong>
						<p style="margin-top: 10px; margin-bottom: 0;">
							Este módulo se encuentra actualmente en desarrollo. 
							Pronto estará disponible con todas sus funcionalidades.
						</p>
					</div>
					
					<!-- Actions -->
					<div style="margin-top: 30px;">
						<a href="<?php echo Uri::base(); ?>" class="btn btn-primary">
							<i class="glyphicon glyphicon-home"></i>
							Volver al Inicio
						</a>
						<a href="<?php echo Uri::base(); ?>contacto" class="btn btn-default">
							<i class="glyphicon glyphicon-envelope"></i>
							Contactar Soporte
						</a>
					</div>
				</div>
			</div>
			<div class="panel-footer text-center text-muted">
				<small>
					<i class="glyphicon glyphicon-calendar"></i>
					<?php echo date('Y-m-d H:i:s'); ?>
				</small>
			</div>
		</div>
	</div>
</div>
