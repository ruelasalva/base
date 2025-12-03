<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">
					<i class="fas fa-puzzle-piece"></i> Módulos del Sistema
				</h3>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th width="5%">ID</th>
								<th width="15%">Nombre Interno</th>
								<th width="20%">Nombre Visible</th>
								<th width="25%">Descripción</th>
								<th width="10%">Categoría</th>
								<th width="5%">Orden</th>
								<th width="8%">Estado</th>
								<th width="12%">Acciones</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							$current_category = '';
							foreach ($modules as $module): 
								// Separador de categoría
								if ($current_category != $module['category']):
									$current_category = $module['category'];
							?>
								<tr class="table-secondary">
									<td colspan="8"><strong><?php echo strtoupper($current_category); ?></strong></td>
								</tr>
							<?php endif; ?>
							
							<tr>
								<td><?php echo $module['id']; ?></td>
								<td>
									<code><?php echo $module['name']; ?></code>
								</td>
								<td>
									<strong><?php echo $module['display_name']; ?></strong>
									<?php if (!empty($module['icon'])): ?>
										<i class="<?php echo $module['icon']; ?> text-muted ms-2"></i>
									<?php endif; ?>
								</td>
								<td>
									<small class="text-muted"><?php echo $module['description']; ?></small>
								</td>
								<td>
									<span class="badge bg-info"><?php echo $module['category']; ?></span>
								</td>
								<td class="text-center"><?php echo $module['order_position']; ?></td>
								<td>
									<?php if ($module['is_active']): ?>
										<span class="badge bg-success">Activo</span>
									<?php else: ?>
										<span class="badge bg-secondary">Inactivo</span>
									<?php endif; ?>
								</td>
								<td>
									<a href="<?php echo Uri::create('admin/system_modules/editar/' . $module['id']); ?>" 
									   class="btn btn-sm btn-primary" 
									   title="Editar">
										<i class="fas fa-edit"></i> Editar
									</a>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
.table-responsive {
	max-height: 800px;
	overflow-y: auto;
}

.table thead th {
	position: sticky;
	top: 0;
	background: #fff;
	z-index: 10;
	box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
}

.table-secondary td {
	background: #e9ecef !important;
	font-weight: bold;
	padding: 10px !important;
}
</style>
