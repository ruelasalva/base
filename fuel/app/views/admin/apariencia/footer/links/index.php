<!-- ENCABEZADO -->
<div class="header bg-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Links del Footer</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
              <li class="breadcrumb-item"><?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?></li>
              <li class="breadcrumb-item"><?php echo Html::anchor('admin/apariencia/footer', 'Footer'); ?></li>
              <li class="breadcrumb-item active" aria-current="page">Links</li>
            </ol>
          </nav>
        </div>
        <div class="col-lg-6 col-5 text-right">
          <?php echo Html::anchor('admin/apariencia/footer/links/agregar/'.$footer->id, '<i class="fas fa-plus"></i> Agregar', ['class'=>'btn btn-sm btn-warning']); ?>
          <?php echo Html::anchor('admin/apariencia/footer/info/'.$footer->id, '<i class="fas fa-eye"></i> Previsualizar', ['class'=>'btn btn-sm btn-info']); ?>
          <?php echo Html::anchor('admin/apariencia/footer', '<i class="fas fa-arrow-left"></i> Volver', ['class'=>'btn btn-sm btn-neutral']); ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- CONTENIDO -->
<div class="container-fluid mt--6">
  <div class="row">
    <div class="col">
      <div class="card shadow">
        <div class="card-header border-0">
          <h3 class="mb-0"><i class="fas fa-link text-primary"></i> Listado de Links</h3>
        </div>
        <div class="card-body">

          <!-- SECCION: MAPA DE SITIO -->
          <h5 class="text-muted mb-3"><i class="fas fa-sitemap text-info"></i> Mapa de sitio</h5>
          <div class="table-responsive">
            <table class="table table-hover align-items-center table-sm">
              <thead class="thead-light">
                <tr>
                  <th>Título</th>
                  <th>URL</th>
                  <th>Slug</th>
                  <th>Orden</th>
                  <th>Estado</th>
                  <th class="text-right">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($sitemaps): ?>
                  <?php foreach ($sitemaps as $link): ?>
                  <tr>
                    <td><?php echo e($link->title); ?></td>
                    <td><small class="text-truncate d-inline-block" style="max-width:200px;"><?php echo e($link->url); ?></small></td>
                    <td><?php echo e($link->slug); ?></td>
                    <td><?php echo e($link->sort_order); ?></td>
                    <td><?php echo $link->status ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>'; ?></td>
                    <td class="text-right">
                      <?php echo Html::anchor('admin/apariencia/footer/links/editar/'.$link->id, '<i class="fas fa-edit"></i>', ['class'=>'btn btn-sm btn-warning', 'title'=>'Editar']); ?>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="6" class="text-center text-muted">No hay links en Mapa de sitio.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <!-- SECCION: DOCUMENTOS LEGALES -->
          <h5 class="text-muted mt-5 mb-3"><i class="fas fa-balance-scale text-warning"></i> Documentos legales</h5>
          <div class="table-responsive">
            <table class="table table-hover align-items-center table-sm">
              <thead class="thead-light">
                <tr>
                  <th>Título</th>
                  <th>Slug</th>
                  <th>Orden</th>
                  <th>Estado</th>
                  <th class="text-right">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($legals): ?>
                  <?php foreach ($legals as $link): ?>
                  <tr>
                    <td><?php echo e($link->title); ?></td>
                    <td><?php echo e($link->slug); ?></td>
                    <td><?php echo e($link->sort_order); ?></td>
                    <td><?php echo $link->status ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>'; ?></td>
                    <td class="text-right">
                      <?php echo Html::anchor('admin/apariencia/footer/links/editar/'.$link->id, '<i class="fas fa-edit"></i>', ['class'=>'btn btn-sm btn-warning', 'title'=>'Editar']); ?>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="5" class="text-center text-muted">No hay links de Documentos legales.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
