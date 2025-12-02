<!-- CONTENT -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Detalles de la Lista de Deseados</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <?php echo Html::anchor('admin/deseados', 'Listas de Deseados'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Detalles</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <!-- Aquí puedes agregar un botón de volver o alguna otra acción -->
                    <?php echo Html::anchor('admin/deseados', 'Volver', array('class' => 'btn btn-sm btn-neutral')); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
    <!-- CARD -->
    <div class="row">
        <div class="col">
            <div class="card">
                <!-- CARD HEADER -->
                <div class="card-header border-0">
                    <h3 class="mb-0">Detalles de la Lista de Deseados</h3>
                </div>
                <!-- CARD BODY -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Cliente</h4>
                            <p><?php echo $wishlist_item['customer_id']; ?></p>
                        </div>
                        <div class="col-md-6">
                            <h4>Fecha de Creación</h4>
                            <p><?php echo $wishlist_item['created_at']; ?></p>
                        </div>
                        <div class="col-md-6">
                            <h4>Última Actualización</h4>
                            <p><?php echo $wishlist_item['updated_at']; ?></p>
                        </div>
                        <div class="col-md-6">
                            <h4>Total de la Lista</h4>
                            <p>$<?php echo number_format($wishlist_item['total_price'], 2); ?></p>
                        </div>
                    </div>

                    <hr>

                    <h4>Productos en la Lista de Deseados</h4>
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Producto</th>
                                    <th scope="col">Precio</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                <?php if(!empty($products)): ?>
                                    <?php foreach($products as $product): ?>
                                        <tr>
                                            <th><?php echo $product->id; ?></th>
                                            <td><?php echo $product->name; ?></td>
                                            <td>$<?php echo number_format($product->price->price, 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <th colspan="3">No hay productos en esta lista de deseados</th>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
