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
				<div class="panel-heading"><h3 class="panel-title">Envíanos un mensaje</h3></div>
				<div class="panel-body">
					<form action="<?php echo Uri::base(); ?>contacto/enviar" method="POST">
						<div class="form-group">
							<label for="nombre">Nombre Completo</label>
							<input type="text" class="form-control" id="nombre" name="nombre" required>
						</div>
						<div class="form-group">
							<label for="email">Correo Electrónico</label>
							<input type="email" class="form-control" id="email" name="email" required>
						</div>
						<div class="form-group">
							<label for="mensaje">Mensaje</label>
							<textarea class="form-control" id="mensaje" name="mensaje" rows="5" required></textarea>
						</div>
						<button type="submit" class="btn btn-primary">Enviar Mensaje</button>
					</form>
				</div>
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="panel panel-info">
				<div class="panel-heading"><h3 class="panel-title">Información de Contacto</h3></div>
				<div class="panel-body">
					<p><strong>Email:</strong> <?php echo htmlspecialchars($contact_info['email'], ENT_QUOTES, 'UTF-8'); ?></p>
					<p><strong>Teléfono:</strong> <?php echo htmlspecialchars($contact_info['phone'], ENT_QUOTES, 'UTF-8'); ?></p>
					<p><strong>Dirección:</strong> <?php echo htmlspecialchars($contact_info['address'], ENT_QUOTES, 'UTF-8'); ?></p>
				</div>
			</div>
		</div>
	</div>
</div>
