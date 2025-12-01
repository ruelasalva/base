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
					<h3 class="panel-title"><span class="glyphicon glyphicon-envelope"></span> Envíanos un mensaje</h3>
				</div>
				<div class="panel-body">
					<form action="<?php echo Uri::base(); ?>contacto/enviar" method="POST">
						<div class="form-group">
							<label for="nombre">Nombre Completo</label>
							<input type="text" class="form-control" id="nombre" name="nombre" placeholder="Tu nombre" required>
						</div>
						<div class="form-group">
							<label for="email">Correo Electrónico</label>
							<input type="email" class="form-control" id="email" name="email" placeholder="tu@email.com" required>
						</div>
						<div class="form-group">
							<label for="telefono">Teléfono</label>
							<input type="tel" class="form-control" id="telefono" name="telefono" placeholder="+1 234 567 890">
						</div>
						<div class="form-group">
							<label for="asunto">Asunto</label>
							<input type="text" class="form-control" id="asunto" name="asunto" placeholder="Asunto del mensaje" required>
						</div>
						<div class="form-group">
							<label for="mensaje">Mensaje</label>
							<textarea class="form-control" id="mensaje" name="mensaje" rows="5" placeholder="Escribe tu mensaje aquí..." required></textarea>
						</div>
						<button type="submit" class="btn btn-primary">
							<span class="glyphicon glyphicon-send"></span> Enviar Mensaje
						</button>
					</form>
				</div>
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title"><span class="glyphicon glyphicon-phone-alt"></span> Información de Contacto</h3>
				</div>
				<div class="panel-body">
					<p>
						<span class="glyphicon glyphicon-envelope"></span>
						<strong>Email:</strong><br>
						<?php echo htmlspecialchars($contact_info['email'], ENT_QUOTES, 'UTF-8'); ?>
					</p>
					<p>
						<span class="glyphicon glyphicon-phone"></span>
						<strong>Teléfono:</strong><br>
						<?php echo htmlspecialchars($contact_info['phone'], ENT_QUOTES, 'UTF-8'); ?>
					</p>
					<p>
						<span class="glyphicon glyphicon-map-marker"></span>
						<strong>Dirección:</strong><br>
						<?php echo htmlspecialchars($contact_info['address'], ENT_QUOTES, 'UTF-8'); ?>
					</p>
				</div>
			</div>
			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><span class="glyphicon glyphicon-time"></span> Horarios de Atención</h3>
				</div>
				<div class="panel-body">
					<p><strong>Lunes - Viernes:</strong> 9:00 AM - 6:00 PM</p>
					<p><strong>Sábados:</strong> 9:00 AM - 1:00 PM</p>
					<p><strong>Domingos:</strong> Cerrado</p>
				</div>
			</div>
		</div>
	</div>
</div>
