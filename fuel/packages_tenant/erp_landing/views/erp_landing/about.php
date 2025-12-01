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
				<div class="panel-heading">
					<h3 class="panel-title">Sobre Nosotros</h3>
				</div>
				<div class="panel-body">
					<h2><?php echo htmlspecialchars($company_name, ENT_QUOTES, 'UTF-8'); ?></h2>
					<p><?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></p>
					
					<h4>Nuestra Misión</h4>
					<p>Proporcionar soluciones empresariales innovadoras que ayuden a nuestros clientes a optimizar sus procesos y alcanzar sus objetivos de negocio.</p>
					
					<h4>Nuestra Visión</h4>
					<p>Ser líderes en el mercado de soluciones ERP, reconocidos por la calidad de nuestros productos y la excelencia en el servicio al cliente.</p>
				</div>
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title"><span class="glyphicon glyphicon-info-sign"></span> Información</h3>
				</div>
				<div class="panel-body">
					<p><strong>Fundada:</strong> 2024</p>
					<p><strong>Industria:</strong> Software Empresarial</p>
					<p><strong>Especialidad:</strong> Soluciones ERP Multi-tenant</p>
				</div>
			</div>
			
			<a href="<?php echo Uri::base(); ?>contacto" class="btn btn-primary btn-lg btn-block">
				<span class="glyphicon glyphicon-envelope"></span> Contáctanos
			</a>
		</div>
	</div>
</div>
