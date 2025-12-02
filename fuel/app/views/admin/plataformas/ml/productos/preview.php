<div class="header bg-info pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <h6 class="h2 text-white py-3">
                <i class="fa-solid fa-code"></i> Preview JSON Mercado Libre
            </h6>
        </div>
    </div>
</div>

<div class="container-fluid mt--6">
    <div class="card">
        <div class="card-body">

            <h3 class="mb-3">
                Producto: <?php echo e($product->name); ?>
            </h3>

            <pre style="background:#1e1e1e;color:#00ff9d;padding:20px;border-radius:8px;">
<?php echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?>
            </pre>

            <div class="text-right mt-3">
                <?php echo Html::anchor(
                    'admin/plataforma/ml/productos?config_id='.$config->id,
                    'Volver',
                    ['class'=>'btn btn-secondary']
                ); ?>
            </div>
        </div>
    </div>
</div>
