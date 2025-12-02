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
                            <li class="breadcrumb-item active" aria-current="page">Producto</li>
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
                        <div class="col-lg-6">
                            <ul>
                                <li>
                                    <?php
                                    if (file_exists(DOCROOT.'assets/uploads/'.$image))
                                    {
                                        echo Asset::img($image, array('alt' => $name, 'class' => 'img-fluid d-block', 'data-gc-caption','none' => $name));
                                    } else{
                                        echo Asset::img('sw_no_image.png', array('alt' => $name, 'class' => 'img-fluid d-block', 'data-gc-caption','none' => $name));
                                    }
                                    ?>
                                </li>
                            </ul>
                        </div>
                        <div class="col-lg-6">
                            <div class="product-info px-3 py-5">
                                <?php if (!empty($badge)) echo $badge; ?>
                                <h3 class="mb-4"><?php echo $name; ?></h3>
                                <div class="d-block mt-4 mb-4">
                                    <span>Categoría: <strong>
                                        <?php echo Html::anchor('admin/precotizacion/categoria/'.$category['slug'], $category['name'], array('title' => $category['name'])); ?></strong>
                                    </span>
                                    <span class="px-1">|</span>
                                    <span>Grupo: <strong>
                                        <?php echo Html::anchor('admin/precotizacion/subcategoria/'.$subcategory['slug'], $subcategory['name'], array('title' => $subcategory['name'])); ?></strong>
                                    </span>
                                    <span class="px-1">|</span>
                                    <span>Marca: <strong>
                                        <?php echo Html::anchor('admin/precotizacion/marca/'.$brand['slug'], $brand['name'], array('title' => $brand['name'])); ?></strong>
                                    </span>
                                    <span class="px-1">|</span>
                                    <span>Código : <strong><?php echo $code; ?></strong></span>
                                    <span class="px-1">|</span>
                                    <?php if (!empty($sku)): ?>
                                        <span>SKU: <strong><?php echo $sku; ?></strong></span>
                                    <?php endif; ?>
                                    <?php if(Auth::member(100)): ?>


                                        <div class="form-row align-items-center pt-5 pb-4">
                                            <div class="col-xl">
                                                <div class="d-flex align-items-center">
                                                    <label class="mb-0 mr-1 font-weight-bold text-primary" for="form_cart-qty">Cantidad:</label>
                                                    <div class="flex-fill">
                                                        <?php echo Form::input('quote-qty', '1', array(
                                                            'class' => 'touchspin touchspin-add text-center form-control',
                                                            'id'    => 'quote-qty-'.$product_id,
                                                            'min'   => '1'
                                                        )); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl">
                                                <button type="submit"
                                                    class="btn btn-secondary btn-block my-1 text-uppercase add-product-quote"
                                                    data-type="multiple"
                                                    data-product="<?php echo $product_id; ?>">
                                                    <i class="fas fa-file-invoice mr-1"></i>Agregar a la cotización
                                                </button>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <p class="border-top pt-4">
                                        <?php echo $description; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- /.card-body -->
            </div> <!-- /.card -->
        </div> <!-- /.col-12 -->
    </div> <!-- /.row -->
</div> <!-- /.container-fluid -->
