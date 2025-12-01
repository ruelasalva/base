<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2 text-center">
			<div class="panel panel-success">
				<div class="panel-heading">
					<h1><span class="glyphicon glyphicon-ok-circle"></span></h1>
				</div>
				<div class="panel-body">
					<h2 class="text-success">¡Pedido Realizado con Éxito!</h2>
					<hr>
					<p class="lead">
						Tu pedido ha sido procesado correctamente.
					</p>
					<?php if ($order_id): ?>
					<div class="alert alert-info">
						<strong>Número de Pedido:</strong> #<?php echo htmlspecialchars($order_id, ENT_QUOTES, 'UTF-8'); ?>
					</div>
					<?php endif; ?>
					<p>
						Te hemos enviado un correo electrónico con los detalles de tu pedido.
					</p>
					<p class="text-muted">
						Recibirás actualizaciones sobre el estado de tu envío.
					</p>
					<hr>
					<div class="row">
						<div class="col-md-6">
							<a href="<?php echo Uri::base(); ?>tienda" class="btn btn-primary btn-block btn-lg">
								<span class="glyphicon glyphicon-home"></span> Volver a la Tienda
							</a>
						</div>
						<div class="col-md-6">
							<a href="<?php echo Uri::base(); ?>clients/orders" class="btn btn-default btn-block btn-lg">
								<span class="glyphicon glyphicon-list"></span> Ver Mis Pedidos
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
