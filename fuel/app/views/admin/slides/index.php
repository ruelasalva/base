<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Slides</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/slides', 'Slides'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/slides/agregar', 'Agregar', array('class' => 'btn btn-sm btn-neutral')); ?>
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
					<div class="form-row">
						<div class="col-md-9">
							<h3 class="mb-0">Lista de slides</h3>
						</div>
					</div>
				</div>
				<!-- LIGHT TABLE -->
				<div class="table-responsive" data-toggle="lists" data-list-values='["url"]'>
					<table class="table align-items-center table-flush sorted-table">
						<thead class="thead-light">
							<tr>
								<th scope="col">Imagen</th>
								<th scope="col" class="sort" data-sort="url">URL</th>
								<th scope="col">Mostrar</th>
								<th scope="col">Orden</th>
								<th scope="col"></th>
							</tr>
						</thead>
						<tbody class="list">
							<?php if(!empty($slides)): ?>
								<?php foreach($slides as $slide): ?>
									<tr data-item-id="<?php echo $slide['id']; ?>">
										<th>
											<?php 
												if (file_exists(DOCROOT.'assets/uploads/'.$slide['image'])){
													echo Asset::img($slide['image'], array('class' => 'avatar')); 
														}else{
												 	echo Asset::img('sw_no_slider.jpg', array('class' => 'avatar')); 
												}
											?>
										</th>
										<td class="url">
											<?php echo $slide['url']; ?>
										</td>
										<td>
											<label class="custom-toggle">
                                                <input type="checkbox" class="toggle-sl" data-slide="<?php echo $slide['id']; ?>" <?php echo ($slide['status']) ? 'checked' : ''; ?>>
                                                <span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Sí"></span>
                                            </label>
										</td>
										<td>
											<i class="fas fa-arrows-alt-v" title="Arrastra la fila para modificar el orden"></i> <span class="order-num"><?php echo $slide['order']; ?></span>
										</td>
										<td class="text-right">
											<div class="dropdown">
												<a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													<i class="fas fa-ellipsis-v"></i>
												</a>
												<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
													<?php echo Html::anchor('admin/slides/info/'.$slide['id'], 'Ver', array('class' => 'dropdown-item')); ?>
													<?php echo Html::anchor('admin/slides/editar/'.$slide['id'], 'Editar', array('class' => 'dropdown-item')); ?>
													<div class="dropdown-divider"></div>
													<?php echo Html::anchor('admin/slides/eliminar/'.$slide['id'], 'Eliminar', array('class' => 'dropdown-item delete-item')); ?>
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
