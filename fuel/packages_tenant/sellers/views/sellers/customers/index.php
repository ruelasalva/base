<div class="row">
	<div class="col-md-12">
		<h2><span class="glyphicon glyphicon-user"></span> <?php echo htmlspecialchars($module_name, ENT_QUOTES, 'UTF-8'); ?></h2>
		<hr>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-info">
			<div class="panel-heading">
				<div class="row">
					<div class="col-md-6">
						<h3 class="panel-title">Cartera de Clientes</h3>
					</div>
					<div class="col-md-6 text-right">
						<a href="<?php echo Uri::base(); ?>sellers/customers/agregar" class="btn btn-success btn-sm">
							<span class="glyphicon glyphicon-plus"></span> Agregar Cliente
						</a>
					</div>
				</div>
			</div>
			<div class="panel-body">
				<table class="table table-striped table-hover">
					<thead>
						<tr>
							<th>ID</th>
							<th>Nombre</th>
							<th>Email</th>
							<th>Teléfono</th>
							<th>Total Compras</th>
							<th>Última Compra</th>
							<th>Acciones</th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($customers)): ?>
						<tr>
							<td colspan="7" class="text-center text-muted">
								<span class="glyphicon glyphicon-users" style="font-size: 32px;"></span>
								<p><em>No tienes clientes registrados.</em></p>
								<a href="<?php echo Uri::base(); ?>sellers/customers/agregar" class="btn btn-info">
									<span class="glyphicon glyphicon-plus"></span> Agregar Primer Cliente
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
