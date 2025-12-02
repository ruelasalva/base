<!-- CONTENIDO -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Precotización</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item"><?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?></li>
							<li class="breadcrumb-item active" aria-current="page">Precotización</li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="container-fluid mt--6">
    <div class="row">
        <div class="col-lg-8 checkout-products">
            <?php foreach($products as $product): ?>
                <div class="p-3 border bg-white rounded mb-3 product-<?php echo $product['id']; ?>">
                    <div class="row mx-n3">
                        <div class="col-3 px-1 px-sm-3">
                            <?php
                               if (file_exists(DOCROOT.'assets/uploads/thumb_'.$product['image']))
                               {
                                   echo Html::anchor('admin/precotizacion/producto/'.$product['slug'], Asset::img('thumb_'.$product['image'], array('alt' => $product['name'], 'class' => 'img-fluid d-block mx-auto')), array('title' => $product['name'], 'class' => ''));
                                }
								else
								{
                                	echo Html::anchor('admin/precotizacion/producto/'.$product['slug'], Asset::img('thumb_no_image.png', array('alt' => $product['name'], 'class' => 'img-fluid d-block mx-auto')), array('title' => $product['name'], 'class' => ''));
                                }
                            ?>
                        </div>
                        <div class="col-9">
                            <div class="row">
                                <div class="col-md-8">
                                    <h2 class="title-product text-left text-uppercase mb-3"><?php echo $product['name']; ?></h2>
                                </div>
                            </div>
                            <p class="mb-4">
                                <?php echo Str::truncate($product['description'], 250); ?>
                            </p>
                            <div class="form-row align-items-center">
                                <div class="col-md">
                                    <div class="d-flex align-items-center">
                                        <label class="mb-0 mr-3 font-weight-bold text-uppercase" for="form_quote-qty-<?php echo $product['id']; ?>">Cantidad:</label>
                                        <div class="flex-fill">
                                            <?php echo Form::input('quote-qty-'.$product['id'], $product['quantity'], array('class' => 'touchspin touchspin-edit edit-product-quote text-center form-control', 'data-product' => $product['id'])); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md text-right text-md-left mt-3 mt-md-0">
                                    <button type="submit" class="bg-transparent border-0 text-primary font-weight-bold text-uppercase delete-product-quote" data-product="<?php echo $product['id']; ?>">Eliminar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <aside class="col-lg-4">
            <div class="rounded border bg-white p-3 mt-3 mt-lg-0" id="checkout_sidebar">
				<?php echo Form::open(array('method' => 'post')); ?>
                	<p class="text-justify">Selecciona un socio para enviar la precotización:</p>
					<fieldset>
						<div class="form-row">
							<div class="col-md-12 mb-3">
								<div class="form-group">
									<?php echo Form::select('partner', (isset($partner) ? $partner : 'none'), $partner_opts, array('id' => 'partner', 'class' => 'form-control', 'data-toggle' => 'select')); ?>
								</div>
							</div>
						</div>
					</fieldset>
					<?php echo Form::submit(array('value'=> 'Enviar', 'name' => 'submit', 'class' => 'btn btn-primary btn-block text-uppercase')); ?>
				<?php echo Form::close(); ?>
            </div>
        </aside>
    </div>
</div> <!-- /.container-fluid -->
