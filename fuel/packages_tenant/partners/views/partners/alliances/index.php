<div class="row">
	<div class="col-md-12">
		<h2><span class="glyphicon glyphicon-link"></span> <?php echo htmlspecialchars($module_name, ENT_QUOTES, 'UTF-8'); ?></h2>
		<hr>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default" style="border-color: #9b59b6;">
			<div class="panel-heading" style="background: #9b59b6; color: white;">
				<div class="row">
					<div class="col-md-6">
						<h3 class="panel-title">Listado de Alianzas</h3>
					</div>
					<div class="col-md-6 text-right">
						<a href="<?php echo Uri::base(); ?>partners/alliances/agregar" class="btn btn-success btn-sm">
							<span class="glyphicon glyphicon-plus"></span> Nueva Alianza
						</a>
					</div>
				</div>
			</div>
			<div class="panel-body">
				<table class="table table-striped table-hover">
					<thead>
						<tr>
							<th>ID</th>
							<th>Empresa</th>
							<th>Tipo</th>
							<th>Estado</th>
							<th>Fecha Inicio</th>
							<th>Acciones</th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($alliances)): ?>
						<tr>
							<td colspan="6" class="text-center text-muted">
								<span class="glyphicon glyphicon-link" style="font-size: 32px;"></span>
								<p><em>No tienes alianzas registradas.</em></p>
								<a href="<?php echo Uri::base(); ?>partners/alliances/agregar" class="btn btn-default" style="background: #9b59b6; color: white;">
									<span class="glyphicon glyphicon-plus"></span> Crear Nueva Alianza
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
