<div class="row">
	<div class="col-md-12">
		<h2><span class="glyphicon glyphicon-shopping-bag"></span> <?php echo htmlspecialchars($module_name, ENT_QUOTES, 'UTF-8'); ?></h2>
		<hr>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-warning">
			<div class="panel-heading">
				<h3 class="panel-title">Historial de Pedidos</h3>
			</div>
			<div class="panel-body">
				<table class="table table-striped table-hover">
					<thead>
						<tr>
							<th>Pedido #</th>
							<th>Fecha</th>
							<th>Estado</th>
							<th>Total</th>
							<th>Acciones</th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($orders)): ?>
						<tr>
							<td colspan="5" class="text-center text-muted">
								<span class="glyphicon glyphicon-shopping-bag" style="font-size: 32px;"></span>
								<p><em>No tienes pedidos realizados aún.</em></p>
								<a href="<?php echo Uri::base(); ?>tienda" class="btn btn-warning">
									<span class="glyphicon glyphicon-shopping-cart"></span> Ir a la Tienda
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
