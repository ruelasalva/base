<!-- ENCABEZADO -->
<div class="header bg-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Previsualización Footer</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
              <li class="breadcrumb-item">
                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
              </li>
              <li class="breadcrumb-item">
                <?php echo Html::anchor('admin/apariencia/footer', 'Footer'); ?>
              </li>
              <li class="breadcrumb-item active" aria-current="page">Info</li>
            </ol>
          </nav>
        </div>
        <div class="col-lg-6 col-5 text-right">
          <?php echo Html::anchor('admin/apariencia/footer/editar/'.$footer->id, '<i class="fas fa-edit"></i> Editar', ['class'=>'btn btn-sm btn-warning']); ?>
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
      <div class="card">
        <div class="card-header border-0">
          <h3 class="mb-0">Vista previa</h3>
        </div>
        <div class="card-body">

          <!-- FOOTER DINÁMICO -->
          <footer class="bg-white pt-5 pb-0 border">
            <div class="container-fluid px-5">
              <div class="row">

                <!-- Columna 1 -->
                <div class="col-lg-4 col-md-6 mb-4 border-orange text-left">
                  <div class="text-center mb-3">
                    <?php if ($footer->logo_main): ?>
                      <?php echo Html::anchor(
                        '/',
                        Html::img(Uri::base(false).'assets/'.$footer->logo_main, [
                          'alt'   => 'Logo principal',
                          'class' => 'img-fluid mx-2',
                          'style' => 'max-height:60px;'
                        ]),
                        ['title'=>'Inicio']
                      ); ?>
                    <?php endif; ?>

                    <?php if ($footer->logo_secondary): ?>
                      <?php echo Html::img(Uri::base(false).'assets/'.$footer->logo_secondary, [
                        'alt'   => 'Logo secundario',
                        'class' => 'img-fluid mx-2',
                        'style' => 'max-height:60px;'
                      ]); ?>
                    <?php endif; ?>
                  </div>


                  <h5 class="footer-title-left">Dirección</h5>
                  <p class="small mb-2"><?php echo nl2br(e($footer->address)); ?></p>

                  <h5 class="footer-title-left mt-3"></h5>
                  <p class="small text-justify"><?php echo nl2br(e($footer->customer_service)); ?></p>
                </div>

                <!-- Columna 2 -->
                <div class="col-lg-2 col-md-6 mb-4 border-orange text-left">
                  <h5 class="footer-title-left">Mapa de sitio</h5>
                  <ul class="list-unstyled small mb-3">
                    <?php if (!empty($links)): ?>
                      <?php foreach ($links as $link): ?>
                        <?php if ($link->type == 'sitemap' && $link->status): ?>
                          <li><?php echo Html::anchor($link->url, $link->title); ?></li>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </ul>

                  <h5 class="footer-title-left">Documentos legales</h5>
                  <ul class="list-unstyled small">
                    <?php if (!empty($links)): ?>
                      <?php foreach ($links as $link): ?>
                        <?php if ($link->type == 'legal' && $link->status): ?>
                          <li><?php echo Html::anchor($link->slug, $link->title); ?></li>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </ul>
                </div>

                <!-- Columna 3 -->
                <div class="col-lg-3 col-md-6 mb-4 border-orange text-center">
                  <h5 class="footer-title">Contáctanos</h5>
                  <?php if ($footer->phone): ?>
                    <p class="mb-1"><i class="fas fa-phone text-danger mr-2"></i><?php echo e($footer->phone); ?></p>
                  <?php endif; ?>
                  <?php if ($footer->email): ?>
                    <p class="mb-1"><i class="fas fa-envelope text-danger mr-2"></i><?php echo e($footer->email); ?></p>
                  <?php endif; ?>

                  <h5 class="footer-title mt-3">Horarios de atención</h5>
                  <?php if ($footer->office_hours_week): ?>
                    <p class="small mb-1"><?php echo nl2br(e($footer->office_hours_week)); ?></p>
                  <?php endif; ?>
                  <?php if ($footer->office_hours_weekend): ?>
                    <p class="small mb-0"><?php echo nl2br(e($footer->office_hours_weekend)); ?></p>
                  <?php endif; ?>
                </div>

                <!-- Columna 4 -->
                <div class="col-lg-3 col-md-6 mb-4 text-left">
                  <h5 class="footer-title">Nuestros distintivos</h5>
                  <div class="text-center">
                    <?php if (!empty($badges)): ?>
                      <?php foreach ($badges as $badge): ?>
                        <?php if ($badge->status): ?>
                          <?php echo Html::img(Uri::base(false).'assets/'.$badge->image, [ 'alt'   => $badge->title,'class' => 'img-fluid mb-2', 'style' => 'max-height:70px']); ?>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </div>

                  <h5 class="footer-title">Síguenos en nuestras redes</h5>
                  <div class="text-center">
                    <?php if ($footer->facebook): ?><a href="<?php echo $footer->facebook; ?>" target="_blank" class="text-dark mr-2"><i class="fab fa-facebook fa-2x"></i></a><?php endif; ?>
                    <?php if ($footer->instagram): ?><a href="<?php echo $footer->instagram; ?>" target="_blank" class="text-dark mr-2"><i class="fab fa-instagram fa-2x"></i></a><?php endif; ?>
                    <?php if ($footer->linkedin): ?><a href="<?php echo $footer->linkedin; ?>" target="_blank" class="text-dark mr-2"><i class="fab fa-linkedin fa-2x"></i></a><?php endif; ?>
                    <?php if ($footer->youtube): ?><a href="<?php echo $footer->youtube; ?>" target="_blank" class="text-dark"><i class="fab fa-youtube fa-2x"></i></a><?php endif; ?>
                    <?php if ($footer->twitter): ?><a href="<?php echo $footer->twitter; ?>" target="_blank" class="text-dark"><i class="fab fa-twitter fa-2x"></i></a><?php endif; ?>
                    <?php if ($footer->tiktok): ?><a href="<?php echo $footer->tiktok; ?>" target="_blank" class="text-dark"><i class="fab fa-tiktok fa-2x"></i></a><?php endif; ?>
                    <?php if ($footer->whatsapp): ?><a href="https://wa.me/<?php echo $footer->whatsapp; ?>" target="_blank" class="text-dark"><i class="fab fa-whatsapp fa-2x"></i></a><?php endif; ?>
                    <?php if ($footer->telegram): ?><a href="<?php echo $footer->telegram; ?>" target="_blank" class="text-dark"><i class="fab fa-telegram fa-2x"></i></a><?php endif; ?>
                    <?php if ($footer->pinterest): ?><a href="<?php echo $footer->pinterest; ?>" target="_blank" class="text-dark"><i class="fab fa-pinterest fa-2x"></i></a><?php endif; ?>
                    <?php if ($footer->snapchat): ?><a href="<?php echo $footer->snapchat; ?>" target="_blank" class="text-dark"><i class="fab fa-snapchat fa-2x"></i></a><?php endif; ?>
                  </div>
                </div>

              </div>

              <!-- COPYRIGHT -->
              <div class="row border-top pt-3 mt-3 text-center">
                <div class="col-md text-md-left mb-2 mb-md-0">
                  <small>Copyright <?php echo date('Y'); ?> © <strong>Distribuidora Sajor S.A. de C.V.</strong></small>
                </div>
                <div class="col-md text-md-right">
                  <small>Desarrollado por <?php echo Html::anchor('http://sectorweb.mx', '<strong>Sector Web</strong>', ['target' => '_blank']); ?></small>
                </div>
              </div>
            </div>
          </footer>
          <!-- FIN FOOTER -->

        </div>
      </div>
    </div>
  </div>
</div>
