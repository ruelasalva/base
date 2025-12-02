<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Diseño de plantillas</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								Plantillas visuales
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/editordiseno/agregar', 'Agregar plantilla', ['class'=>'btn btn-sm btn-neutral']); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
	<div class="row">
		<div class="col">
			<div class="card">
				<div class="card-header border-0">
					<h3 class="mb-0">Lista de plantillas visuales</h3>
				</div>

				<div class="card-body">
					<?php if (empty($plantillas)): ?>
						<div class="alert alert-info mb-0">
							<i class="fas fa-info-circle mr-2"></i>
							No hay plantillas guardadas.
						</div>
					<?php else: ?>
						<div class="row">
							<?php foreach ($plantillas as $plantilla): ?>
								<div class="col-md-3 col-12 mb-4">
									<div class="card h-100 shadow-sm border">
										<div class="card-body text-center p-3">
											<div class="img-preview">
                                                <?php
                                                // SI EXISTE Y NO ESTÁ VACÍO EL PREVIEW
                                                if (isset($plantilla['preview']) && !empty($plantilla['preview'])) {
                                                    // SI ES BASE64
                                                    if (str_starts_with($plantilla['preview'], 'data:image/')) {
                                                        echo '<img src="' . $plantilla['preview'] . '" class="mb-2 rounded bg-light border" style="width:100%;max-width:130px;max-height:110px;object-fit:contain;" alt="Preview de '.$plantilla['name'].'">';
                                                    }
                                                    // SI ES PATH FÍSICO (y existe en uploads)
                                                    elseif (file_exists(DOCROOT . 'assets/uploads/' . $plantilla['preview'])) {
                                                        echo '<img src="/assets/uploads/' . $plantilla['preview'] . '" class="mb-2 rounded bg-light border" style="width:100%;max-width:130px;max-height:110px;object-fit:contain;" alt="Preview de '.$plantilla['name'].'">';
                                                    }
                                                    // SI NO EXISTE FÍSICO, USA DUMMY
                                                    else {
                                                        echo Asset::img('thumb_no_image.png', [
                                                            'class' => 'mb-2 rounded bg-light border',
                                                            'style' => 'width:100%;max-width:130px;max-height:110px;object-fit:contain;',
                                                            'alt' => 'Sin preview de '.$plantilla['name']
                                                        ]);
                                                    }
                                                } else {
                                                    // NO HAY PREVIEW, USA DUMMY
                                                    echo Asset::img('thumb_no_image.png', [
                                                        'class' => 'mb-2 rounded bg-light border',
                                                        'style' => 'width:100%;max-width:130px;max-height:110px;object-fit:contain;',
                                                        'alt' => 'Sin preview de '.$plantilla['name']
                                                    ]);
                                                }
                                                ?>
                                            </div>

											<div class="font-weight-bold mb-1" style="font-size:1.08rem;"><?php echo $plantilla['name']; ?></div>
											<div class="text-muted mb-2" style="font-size:.92rem;">
												Actualizado: <?php echo date('d/m/Y H:i', strtotime($plantilla['updated_at'])); ?>
											</div>
											<div class="btn-group btn-group-sm" role="group">
												<?php echo Html::anchor('admin/editordiseno/info/'.$plantilla['id'], '<i class="fas fa-eye"></i> Ver', ['class'=>'btn btn-outline-info']); ?>
												<?php echo Html::anchor('admin/editordiseno/editar/'.$plantilla['id'], '<i class="fas fa-edit"></i> Editar', ['class'=>'btn btn-outline-primary']); ?>
												<?php echo Html::anchor('admin/editordiseno/eliminar/'.$plantilla['id'], '<i class="fas fa-trash"></i> Eliminar', [
													'class'=>'btn btn-outline-danger delete-item',
													'onclick' => "return confirm('¿Seguro que deseas eliminar esta plantilla?');"
												]); ?>
											</div>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
