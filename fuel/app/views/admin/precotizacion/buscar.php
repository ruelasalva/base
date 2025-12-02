
<!-- CONTENIDO -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Resultados de búsqueda</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item"><?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?></li>
							<li class="breadcrumb-item active" aria-current="page">Búsqueda</li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="container-fluid mt--6">
	<div class="row">
		<div class="col-12">
			<div class="card shadow">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-2 mb-4">
							<div class="filter-select">
								<div class="form-group">
									<?php echo Form::select('order-by', (isset($order_by) ? $order_by : 'nuevos'), array(
										'menor-mayor' => 'Menor a Mayor Precio',
										'mayor-menor' => 'Mayor a Menor Precio',
										'a-z'         => 'Nombre: A - Z',
										'z-a'         => 'Nombre: Z - A',
										'nuevos'      => 'Lo más nuevo primero',
									), array('id' => 'order-by', 'class' => 'form-control')); ?>
								</div>
							</div>
							<ul class="accordion-menu">
								<li>
									<div class="dropdownlink"><i class="fa fa-angeles-right" aria-hidden="true"></i>
										Categoría
										<i class="fa fa-chevron-down" aria-hidden="true"></i>
									</div>
									<ul class="submenuItems">
										<?php if(!empty($categories)): ?>
											<?php foreach($categories as $category): ?>
												<li>
													<?php echo Html::anchor('admin/precotizacion/categoria/'.$category['slug'], $category['name'], array('title' => $category['name'])); ?>
												</li>
											<?php endforeach; ?>
										<?php else: ?>
											<li>
												Sin categorías
											</li>
										<?php endif; ?>
									</ul>
								</li>
								<li>
									<div class="dropdownlink"><i class="fa fa-angeles-right" aria-hidden="true"></i>
										Grupo
										<i class="fa fa-chevron-down" aria-hidden="true"></i>
									</div>
									<ul class="submenuItems">
										<?php if(!empty($subcategories)): ?>
											<?php foreach($subcategories as $subcategory): ?>
												<li>
													<?php echo Html::anchor('admin/precotizacion/subcategoria/'.$subcategory['slug'], $subcategory['name'], array('title' => $subcategory['name'])); ?>
												</li>
											<?php endforeach; ?>
										<?php else: ?>
											<li>
												Sin grupo
											</li>
										<?php endif; ?>
									</ul>
								</li>
								<li>
									<div class="dropdownlink"><i class="fa fa-angeles-right" aria-hidden="true"></i>
										Marca
										<i class="fa fa-chevron-down" aria-hidden="true"></i>
									</div>
									<ul class="submenuItems">
										<ul class="list-group list-group-flush">
											<?php if(!empty($brands)): ?>
												<?php foreach($brands as $brand): ?>
													<li>
														<?php echo Html::anchor('admin/precotizacion/marca/'.$brand['slug'], $brand['name'], array('title' => $brand['name'])); ?>
													</li>
												<?php endforeach; ?>
											<?php else: ?>
												<li>
													Sin marcas
												</li>
											<?php endif; ?>
										</ul>
									</ul>
								</li>
							</ul>
						</div>
						<div class="col-lg-10">
							<div class="row">
								<?php if(!empty($products)): ?>
								    <?php foreach($products as $product): ?>
								        <div class="col-6 col-md-4 col-lg-3 mb-4 d-flex">
								            <div class="card text-center border shadow-sm w-100 d-flex flex-column">
								                <?php if (!empty($product['badge'])) echo $product['badge']; ?>
								                <div class="pt-3">
								                    <?php
								                        if(file_exists(DOCROOT.'assets/uploads/thumb_'.$product['image']))
								                        {
								                            echo Html::anchor('admin/precotizacion/producto/'.$product['slug'], Asset::img('thumb_'.$product['image'], ['alt' => $product['name'], 'class' => 'img-fluid']), ['title' => $product['name']]);
								                        }
								                        else
								                        {
								                            echo Html::anchor('admin/precotizacion/producto/'.$product['slug'], Asset::img('thumb_no_image.png', ['alt' => $product['name'], 'class' => 'img-fluid']), ['title' => $product['name']]);
								                        }
								                    ?>
								                </div>
								                <div class="card-body p-2 d-flex flex-column justify-content-between">
								                    <h6 class="text-truncate mb-2"><?php echo Html::anchor('admin/precotizacion/producto/'.$product['slug'], $product['name']); ?></h6>
								                    <div class="small text-muted mb-2">SKU: <?php echo $product['code']; ?></div>
													<div class="pb-1">
														<div class="d-flex align-items-center">
															<label class="mb-0 mr-1 font-weight-bold text-primary" for="quote-qty-<?php echo $product['product_id']; ?>">Cantidad:</label>
															<div class="flex-fill">
																<?php echo Form::input('quote-qty-'.$product['product_id'], '1', array(
																	'class' => 'touchspin touchspin-add text-center form-control',
																	'id'    => 'quote-qty-'.$product['product_id'],
																	'min'   => '1'
																)); ?>
															</div>
														</div>
													</div>
													<div class="product-button">
														<button type="submit"
															class="btn btn-secondary btn-block text-uppercase add-product-quote"
															data-type="multiple"
															data-product="<?php echo $product['product_id']; ?>">
															<i class="fas fa-file-invoice mr-1"></i>
															<span class="d-none d-sm-inline">Agregar <br>a la <br>cotización</span>
															<span class="d-inline d-sm-none">Agregar</span>
														</button>
													</div>
								                </div>
								            </div>
								        </div>
								    <?php endforeach; ?>
								<?php else: ?>
								    <div class="col-12">
								        <div class="alert alert-warning text-center">No se encontraron productos.</div>
								    </div>
								<?php endif; ?>
							</div>
							<div class="row mt-4">
								<div class="col">
									<div class="d-flex justify-content-center">
										<?php echo $pagination; ?>
									</div>
								</div>
							</div>
						</div> <!-- /.col-lg-10 -->
					</div> <!-- /.row -->
				</div> <!-- /.card-body -->
			</div> <!-- /.card -->
		</div> <!-- /.col-12 -->
	</div> <!-- /.row -->
</div> <!-- /.container-fluid -->
