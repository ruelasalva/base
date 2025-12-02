<!-- HEADER -->
<div class="header bg-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Finanzas</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
              <li class="breadcrumb-item">
                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
              </li>
              <li class="breadcrumb-item">
                <?php echo Html::anchor('admin/finanzas/plancuentas', 'Plan de Cuentas'); ?>
              </li>
              <li class="breadcrumb-item active" aria-current="page">
                <?php echo Html::anchor('admin/finanzas/plancuentas/info/'.$id, $code); ?>
              </li>
            </ol>
          </nav>
        </div>
        <div class="col-lg-6 col-5 text-right">
          <?php echo Html::anchor('admin/finanzas/plancuentas/editar/'.$id, 'Editar', array('class' => 'btn btn-sm btn-neutral')); ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
  <div class="row">
    <div class="col">
      <div class="card-wrapper">
        <div class="card">
          <div class="card-header">
            <h3 class="mb-0">Información de la cuenta</h3>
          </div>

          <div class="card-body">
            <fieldset>
              <div class="form-row">
                <div class="col-md-12 mt-0 mb-3">
                  <legend class="mb-0 heading">Detalles Generales</legend>
                </div>

                <!-- Código -->
                <div class="col-md-4 mb-3">
                  <div class="form-group">
                    <?php echo Form::label('Código', 'code', array('class' => 'form-control-label')); ?>
                    <span class="form-control"><?php echo $code; ?></span>
                  </div>
                </div>

                <!-- Nombre -->
                <div class="col-md-8 mb-3">
                  <div class="form-group">
                    <?php echo Form::label('Nombre', 'name', array('class' => 'form-control-label')); ?>
                    <span class="form-control"><?php echo $name; ?></span>
                  </div>
                </div>

                <!-- Tipo -->
                <div class="col-md-4 mb-3">
                  <div class="form-group">
                    <?php echo Form::label('Tipo', 'type', array('class' => 'form-control-label')); ?>
                    <span class="form-control"><?php echo $type; ?></span>
                  </div>
                </div>

                <!-- Nivel -->
                <div class="col-md-2 mb-3">
                  <div class="form-group">
                    <?php echo Form::label('Nivel', 'level', array('class' => 'form-control-label')); ?>
                    <span class="form-control"><?php echo $level; ?></span>
                  </div>
                </div>

                <!-- Moneda -->
                <div class="col-md-4 mb-3">
                  <div class="form-group">
                    <?php echo Form::label('Moneda', 'currency', array('class' => 'form-control-label')); ?>
                    <span class="form-control"><?php echo $currency; ?></span>
                  </div>
                </div>

                <!-- Cuenta Padre -->
                <div class="col-md-6 mb-3">
                  <div class="form-group">
                    <?php echo Form::label('Cuenta Padre', 'parent', array('class' => 'form-control-label')); ?>
                    <span class="form-control">
                      <?php if (!empty($parent)): ?>
                        <?php echo Html::anchor('admin/finanzas/plancuentas/info/'.$parent['id'], $parent['name'], array('target' => '_blank')); ?>
                      <?php else: ?>
                        -
                      <?php endif; ?>
                    </span>
                  </div>
                </div>

                <!-- Booleans -->
                <div class="col-md-2 mb-3">
                  <div class="form-group">
                    <?php echo Form::label('Confidencial', 'is_confidential', array('class' => 'form-control-label')); ?>
                    <span class="form-control"><?php echo ($is_confidential ? 'Sí' : 'No'); ?></span>
                  </div>
                </div>
                <div class="col-md-2 mb-3">
                  <div class="form-group">
                    <?php echo Form::label('Efectivo', 'is_cash_account', array('class' => 'form-control-label')); ?>
                    <span class="form-control"><?php echo ($is_cash_account ? 'Sí' : 'No'); ?></span>
                  </div>
                </div>
                <div class="col-md-2 mb-3">
                  <div class="form-group">
                    <?php echo Form::label('Activa', 'is_active', array('class' => 'form-control-label')); ?>
                    <span class="form-control"><?php echo ($is_active ? 'Sí' : 'No'); ?></span>
                  </div>
                </div>

                <!-- Anexo 24 -->
                <div class="col-md-4 mb-3">
                  <div class="form-group">
                    <?php echo Form::label('Código Anexo 24', 'annex24_code', array('class' => 'form-control-label')); ?>
                    <span class="form-control"><?php echo $annex24_code ?: '-'; ?></span>
                  </div>
                </div>

                <!-- Clase -->
                <div class="col-md-4 mb-3">
                  <div class="form-group">
                    <?php echo Form::label('Clase de Cuenta', 'account_class', array('class' => 'form-control-label')); ?>
                    <span class="form-control"><?php echo $account_class ?: '-'; ?></span>
                  </div>
                </div>

                <!-- Fechas -->
                <div class="col-md-4 mb-3">
                  <div class="form-group">
                    <?php echo Form::label('Creado en', 'created_at', array('class' => 'form-control-label')); ?>
                    <span class="form-control"><?php echo (!empty($created_at)) ? date('d/m/Y H:i', $created_at) : '-'; ?></span>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <div class="form-group">
                    <?php echo Form::label('Actualizado en', 'updated_at', array('class' => 'form-control-label')); ?>
                    <span class="form-control"><?php echo (!empty($updated_at)) ? date('d/m/Y H:i', $updated_at) : '-'; ?></span>
                  </div>
                </div>

              </div>
            </fieldset>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
