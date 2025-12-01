<div class="container">
	<div class="row">
		<div class="col-md-12">
			<h1><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></h1>
			<hr>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-8">
			<div class="panel panel-default">
				<div class="panel-heading"><h3 class="panel-title">Sobre Nosotros</h3></div>
				<div class="panel-body">
					<h2><?php echo htmlspecialchars($company_name, ENT_QUOTES, 'UTF-8'); ?></h2>
					<p><?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></p>
				</div>
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="panel panel-info">
				<div class="panel-heading"><h3 class="panel-title">Información</h3></div>
				<div class="panel-body">
					<p><strong>Fundada:</strong> 2024</p>
					<p><strong>Industria:</strong> Software Empresarial</p>
				</div>
			</div>
			<a href="<?php echo Uri::base(); ?>contacto" class="btn btn-primary btn-lg btn-block">Contáctanos</a>
		</div>
	</div>
</div>
