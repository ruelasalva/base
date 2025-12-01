<!-- Hero Section -->
<div class="jumbotron" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
	<div class="container">
		<h1><?php echo htmlspecialchars($hero_title, ENT_QUOTES, 'UTF-8'); ?></h1>
		<p><?php echo htmlspecialchars($hero_subtitle, ENT_QUOTES, 'UTF-8'); ?></p>
		<p>
			<a class="btn btn-primary btn-lg" href="<?php echo Uri::base(); ?>tienda" role="button">
				<span class="glyphicon glyphicon-shopping-cart"></span> Ir a la Tienda
			</a>
			<a class="btn btn-default btn-lg" href="<?php echo Uri::base(); ?>contacto" role="button">
				<span class="glyphicon glyphicon-envelope"></span> Contáctanos
			</a>
		</p>
	</div>
</div>

<!-- Features Section -->
<div class="container">
	<div class="row">
		<div class="col-md-12 text-center">
			<h2>¿Por qué elegirnos?</h2>
			<hr>
		</div>
	</div>
	
	<div class="row">
		<?php foreach ($features as $feature): ?>
		<div class="col-md-4 text-center">
			<div class="panel panel-default">
				<div class="panel-body">
					<span class="glyphicon glyphicon-<?php echo htmlspecialchars($feature['icon'], ENT_QUOTES, 'UTF-8'); ?>" style="font-size: 48px; color: #667eea;"></span>
					<h3><?php echo htmlspecialchars($feature['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
					<p><?php echo htmlspecialchars($feature['description'], ENT_QUOTES, 'UTF-8'); ?></p>
				</div>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
	
	<!-- Call to Action -->
	<div class="row" style="margin-top: 40px; margin-bottom: 40px;">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-body text-center">
					<h2>¿Listo para comenzar?</h2>
					<p>Únete a miles de empresas que ya confían en nuestra solución ERP.</p>
					<a href="<?php echo Uri::base(); ?>contacto" class="btn btn-primary btn-lg">
						<span class="glyphicon glyphicon-phone-alt"></span> Solicitar Demo
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
