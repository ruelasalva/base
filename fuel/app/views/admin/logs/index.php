<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Logs</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/logs', 'Logs'); ?>
							</li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
	<!-- TABLE -->
	<div class="row">
		<div class="col">
			<div class="card">
				<!-- CARD HEADER -->
				<div class="card-header border-0">
					<?php echo Form::open(array('action' => 'admin/ventas/buscar', 'method' => 'post')); ?>
					<div class="form-row">
						<div class="col-md-9">
							<h3 class="mb-0">Logs</h3>
						</div>
						<div class="col-md-3 mb-0"></div>
					</div>
					<?php echo Form::close(); ?>
				</div>
				<!-- LIGHT TABLE -->
				<div class="table-responsive" data-toggle="lists" data-list-values='["id", "provider", "date", "type", "transaction"]'>
					<table class="table align-items-center table-flush">
						<thead class="thead-light">
							<tr>
								<th scope="col" class="sort" data-sort="id">ID</th>
								<th scope="col" class="sort" data-sort="provider">Proveedor</th>
								<th scope="col" class="sort" data-sort="date">Fecha</th>
								<th scope="col" class="sort" data-sort="type">Tipo</th>
								<th scope="col" class="sort" data-sort="transaction">Transacción</th>
								<th scope="col"></th>
							</tr>
						</thead>
						<tbody class="list">
							<?php if(!empty($logs)): ?>
								<?php foreach($logs as $log): ?>
									<tr>
										<th class="id">
											<?php echo Html::anchor('admin/logs/info/'.$log['id'], $log['id']); ?>
										</th>
										<td class="provider">
											<?php echo $log['provider']; ?>
										</td>
										<td class="date">
											<?php echo $log['date']; ?>
										</td>
										<td class="type">
											<?php echo $log['type']; ?>
										</td>
										<td class="transaction">
											<?php echo $log['transaction']; ?>
										</td>
										<td class="text-right">
											<div class="dropdown">
												<a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													<i class="fas fa-ellipsis-v"></i>
												</a>
												<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
													<?php echo Html::anchor('admin/logs/info/'.$log['id'], 'Ver', array('class' => 'dropdown-item')); ?>
												</div>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<th scope="row">
										No existen registros
									</th>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
				<?php if($pagination != ''): ?>
					<!-- CARD FOOTER -->
					<div class="card-footer py-4">
						<?php echo $pagination; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
