<!-- HEADER -->
<div class="header bg-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <!-- IZQUIERDA -->
        <div class="col-lg-6 col-7">
          <h2 class="text-white mb-0">Plan de Cuentas</h2>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark mb-0">
              <li class="breadcrumb-item"><?= Html::anchor('admin', '<i class="fas fa-home"></i>'); ?></li>
              <li class="breadcrumb-item active" aria-current="page">Finanzas / Plan de Cuentas</li>
            </ol>
          </nav>
        </div>

        <!-- DERECHA -->
        <div class="col-lg-6 col-5">
          <div class="d-flex justify-content-end align-items-center header-actions flex-wrap">
            <?= Html::anchor(
              'admin/finanzas/plancuentas/arbol',
              '<i class="fas fa-sitemap mr-1"></i> Vista árbol',
              ['class' => 'btn btn-neutral btn-md mr-3 mb-2']
            ); ?>

            <?= Html::anchor(
              'admin/finanzas/plancuentas/agregar',
              '<i class="fas fa-plus-circle mr-1"></i> Agregar cuenta',
              ['class' => 'btn btn-neutral btn-md mr-3 mb-2']
            ); ?>

            <form action="<?= Uri::create('admin/finanzas/plancuentas/importar_csv'); ?>"
                  method="post" enctype="multipart/form-data"
                  class="mb-2">
              <div class="input-group input-group-md" style="min-width:420px;">
                <div class="custom-file">
                  <input type="file" name="archivo" accept=".csv" class="custom-file-input" id="importCSV" required>
                  <label class="custom-file-label text-truncate" for="importCSV">Seleccionar CSV...</label>
                </div>
                <div class="input-group-append">
                  <button type="submit" class="btn btn-success">
                    <i class="fas fa-file-import mr-1"></i> Importar CSV
                  </button>
                </div>
              </div>
            </form>
          </div>
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

        <!-- CARD HEADER -->
        <div class="card-header border-0">
          <div class="form-row align-items-center">
            <div class="col-md-4">
              <h3 class="mb-0">Lista del Plan de Cuentas</h3>
            </div>

            <!-- FORMULARIO DE BÚSQUEDA -->
            <div class="col-md-4 mb-0">
              <?php echo Form::open(array('action' => 'admin/finanzas/plancuentas/buscar', 'method' => 'post', 'class' => 'mb-0')); ?>
              <div class="input-group input-group-sm mt-3 mt-md-0">
                <?php echo Form::input('search', (isset($search) ? $search : ''), array(
                  'id' => 'search',
                  'class' => 'form-control',
                  'placeholder' => 'Código o Nombre',
                  'aria-describedby' => 'button-addon'
                )); ?>
                <div class="input-group-append">
                  <?php echo Form::submit(array(
                    'value'=> 'Buscar',
                    'name'=>'submit',
                    'id' => 'button-addon',
                    'class' => 'btn btn-outline-primary'
                  )); ?>
                </div>
              </div>
              <?php echo Form::close(); ?>
            </div>

            <!-- SELECT DE MONEDA -->
            <div class="col-md-4 mb-0">
              <?php echo Form::open(array('method' => 'get', 'class' => 'mb-0')); ?>
              <div class="input-group input-group-sm mt-3 mt-md-0">
                <?php echo Form::select('currency_id', (isset($currency_id) ? $currency_id : ''), $currency_opts, array(
                  'class' => 'form-control',
                  'onchange' => 'this.form.submit()'
                )); ?>
              </div>
              <?php echo Form::close(); ?>
            </div>
          </div>
        </div>


        <!-- LIGHT TABLE -->
        <div class="table-responsive" 
             data-toggle="lists" 
             data-list-values='["code", "name", "type", "level", "currency", "is_active", "parent_id", "annex24_code", "account_class"]'>
          <table class="table align-items-center table-flush">
            <thead class="thead-light">
              <tr>
                <th scope="col">Código</th>
                <th scope="col">Nombre</th>
                <th scope="col">Tipo</th>
                <th scope="col">Nivel</th>
                <th scope="col">Moneda</th>
                <th scope="col">Clase</th>
                <th scope="col">Padre</th>
                <th scope="col">Anexo 24</th>
                <th scope="col">Activa</th>
                <th scope="col" class="text-right">Acciones</th>
              </tr>
            </thead>
            <tbody class="list">
              <?php if (!empty($accounts)): ?>
                <?php foreach ($accounts as $account): ?>
                  <tr>
                    <td><strong><?php echo $account['code']; ?></strong></td>
                    <td>
                      <?php echo Html::anchor('admin/finanzas/plancuentas/info/'.$account['id'], $account['name']); ?>
                    </td>
                    <td><?php echo $account['type']; ?></td>
                    <td><?php echo $account['level']; ?></td>
                    <td><?php echo $account['currency']; ?></td>
                    <td><?php echo $account['account_class']; ?></td>
                    <td>
                      <?php 
                        echo ($account['parent_id'])
                          ? Html::anchor('admin/finanzas/plancuentas/info/'.$account['parent_id'], $account['parent_id'])
                          : '-'; 
                      ?>
                    </td>
                    <td><?php echo $account['annex24_code']; ?></td>
                    <td>
                      <span class="badge badge-<?php echo ($account['is_active'] ? 'success' : 'secondary'); ?>">
                        <?php echo ($account['is_active'] ? 'Sí' : 'No'); ?>
                      </span>
                    </td>
                    <td class="text-right">
                      <div class="dropdown">
                        <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="fas fa-ellipsis-v"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                          <?php echo Html::anchor('admin/finanzas/plancuentas/info/'.$account['id'], 'Ver', array('class' => 'dropdown-item')); ?>
                          <?php echo Html::anchor('admin/finanzas/plancuentas/editar/'.$account['id'], 'Editar', array('class' => 'dropdown-item')); ?>
                          <div class="dropdown-divider"></div>
                          <?php echo Html::anchor(
                            'admin/finanzas/plancuentas/eliminar/'.$account['id'],
                            'Eliminar',
                            array(
                              'class' => 'dropdown-item delete-item',
                              'onclick' => "return confirm('¿Seguro que deseas eliminar la cuenta {$account['code']} - {$account['name']}?');"
                            )
                          ); ?>
                        </div>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="10" class="text-center text-muted">No existen registros.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <?php if ($pagination != ''): ?>
          <!-- CARD FOOTER -->
          <div class="card-footer py-4">
            <nav aria-label="...">
              <?php echo $pagination; ?>
            </nav>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>


<!-- JS: mostrar nombre del archivo -->
<script>
document.addEventListener('change', function(e){
  if (e.target && e.target.id === 'importCSV') {
    const label = e.target.nextElementSibling;
    label.textContent = e.target.files.length ? e.target.files[0].name : 'Seleccionar CSV...';
  }
});
</script>

<!-- Ajustes visuales -->
<style>
  .header-actions .btn { min-width: 160px; }
  .header-actions .btn i { font-size: 0.95rem; }
  .custom-file-label { background: #fff; }
  @media (max-width: 991.98px){
    .header-actions { gap: .5rem; }
  }
</style>
