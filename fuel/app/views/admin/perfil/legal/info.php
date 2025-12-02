<!-- ENCABEZADO -->
<div class="header bg-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Documento Legal</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
              <li class="breadcrumb-item">
                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
              </li>
              <li class="breadcrumb-item">
                <?php echo Html::anchor('admin/perfil/legal', 'Documentos Legales'); ?>
              </li>
              <li class="breadcrumb-item active" aria-current="page"><?php echo $doc->title; ?></li>
            </ol>
          </nav>
        </div>
        <div class="col-lg-6 col-5 text-right">
            <?php echo Html::anchor('admin/perfil/legal/ver_pdf/'.$doc->id,'Ver en PDF', ['class' => 'btn btn-sm btn-info','target'=>'_blank']); ?>
            <?php echo Html::anchor('admin/perfil/legal/descargar/'.$doc->id,'Descargar en PDF', ['class' => 'btn btn-sm btn-neutral']); ?>  
            <?php echo Html::anchor('admin/perfil/legal/imprimir/'.$doc->id, 'Imprimir', ['class' => 'btn btn-sm btn-neutral','target'=>'_blank']); ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- CONTENIDO -->
<div class="container-fluid mt--6">
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-body">
          <h3><?php echo $doc->title; ?></h3>
          <p><b>Versión:</b> <?php echo $doc->version; ?></p>
          <p><b>Fecha:</b> <?php echo $doc->created_at ? date('d/m/Y', $doc->created_at) : '-'; ?></p>
          <hr>
          <div class="legal-content">
            <?php echo html_entity_decode($doc->content); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
