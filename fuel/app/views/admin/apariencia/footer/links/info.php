<div class="container-fluid mt-4">

  <div class="row mb-4">
    <div class="col"><h4><i class="fa fa-eye text-info"></i> Detalle del enlace</h4></div>
    <div class="col text-right">
      <?php echo Html::anchor('admin/apariencia/footer/links/index/'.$link->footer_id, '<i class="fa fa-arrow-left"></i> Volver', ['class'=>'btn btn-secondary btn-sm']); ?>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <p><strong>Título:</strong> <?php echo e($link->title); ?></p>
      <p><strong>Tipo:</strong> <?php echo $link->type == 'sitemap' ? 'Mapa de sitio' : 'Legal'; ?></p>
      <p><strong>URL:</strong> <?php echo e($link->url); ?></p>
      <p><strong>Slug:</strong> <?php echo e($link->slug); ?></p>
      <p><strong>Orden:</strong> <?php echo e($link->sort_order); ?></p>
      <p><strong>Estado:</strong> <?php echo $link->status ? 'Activo' : 'Inactivo'; ?></p>
    </div>
  </div>

</div>
