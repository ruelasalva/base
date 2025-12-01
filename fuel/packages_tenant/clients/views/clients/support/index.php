<div class="row">
	<div class="col-md-12">
		<h2><span class="glyphicon glyphicon-headphones"></span> <?php echo htmlspecialchars($module_name, ENT_QUOTES, 'UTF-8'); ?></h2>
		<hr>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-warning">
			<div class="panel-heading">
				<div class="row">
					<div class="col-md-6">
						<h3 class="panel-title">Mis Tickets de Soporte</h3>
					</div>
					<div class="col-md-6 text-right">
						<a href="<?php echo Uri::base(); ?>clients/support/agregar" class="btn btn-success btn-sm">
							<span class="glyphicon glyphicon-plus"></span> Nuevo Ticket
						</a>
					</div>
				</div>
			</div>
			<div class="panel-body">
				<table class="table table-striped table-hover">
					<thead>
						<tr>
							<th>Ticket #</th>
							<th>Asunto</th>
							<th>Estado</th>
							<th>Fecha</th>
							<th>Acciones</th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($tickets)): ?>
						<tr>
							<td colspan="5" class="text-center text-muted">
								<span class="glyphicon glyphicon-ok-circle" style="font-size: 32px;"></span>
								<p><em>No tienes tickets de soporte abiertos.</em></p>
								<a href="<?php echo Uri::base(); ?>clients/support/agregar" class="btn btn-warning">
									<span class="glyphicon glyphicon-plus"></span> Crear Nuevo Ticket
								</a>
							</td>
						</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
