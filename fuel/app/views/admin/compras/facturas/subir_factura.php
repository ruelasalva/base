<!-- HEADER -->
<div class="header bg-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Facturas</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
              <li class="breadcrumb-item">
                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
              </li>
              <li class="breadcrumb-item">
                <?php echo Html::anchor('admin/compras/facturas', 'Facturas'); ?>
              </li>
              <li class="breadcrumb-item active" aria-current="page">Subir factura</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- PAGE CONTENT -->
<div class="container-fluid mt--6" id="subir-factura-app">
  <div class="row">
    <div class="col">
      <div class="card-wrapper">
        <div class="card">
          <div class="card-header">
            <h3 class="mb-0">Subir factura</h3>
          </div>
          <div class="card-body">
            <?php echo Form::open(['method' => 'post', 'enctype' => 'multipart/form-data']); ?>
            <fieldset>
              <div class="form-row">
                <div class="col-md-12 mt-0 mb-3">
                  <legend class="mb-0 heading">Información general</legend>
                </div>

                <?php if (isset($order)): ?>
                  <!-- Caso: factura desde una orden de compra -->
                  <div class="col-md-4 mb-3">
                    <?php echo Form::label('Orden de Compra', 'purchase', ['class' => 'form-control-label']); ?>
                    <input type="text" class="form-control" value="<?php echo $order->code_order; ?>" readonly>
                    <?php echo Form::hidden('purchase', $order->id); ?>
                  </div>
                  <div class="col-md-4 mb-3">
                    <?php echo Form::label('Proveedor', 'provider_id', ['class' => 'form-control-label']); ?>
                    <input type="text" class="form-control" value="<?php echo $order->provider->name; ?>" readonly>
                    <?php echo Form::hidden('provider_id', $order->provider->id); ?>
                  </div>
                  <div class="col-md-4 mb-3">
                    <?php echo Form::label('Total OC', 'order_total', ['class' => 'form-control-label']); ?>
                    <input type="text" class="form-control" value="$<?php echo number_format($order->total, 2); ?>" readonly>
                  </div>
                <?php else: ?>
                  <!-- Caso: admin sube factura libre -->
                  <div class="col-md-12 mb-3">
                    <?php echo Form::label('Proveedor', 'provider_id', ['class' => 'form-control-label']); ?>
                    <select v-model="provider_id" @change="cargarOrdenes" name="provider_id" class="form-control" required>
                      <option value="">Selecciona un proveedor</option>
                      <?php foreach ($providers as $prov): ?>
                        <option value="<?php echo $prov->id; ?>"><?php echo $prov->name; ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-6 mb-3">
                    <?php echo Form::label('Orden de Compra', 'purchase', ['class' => 'form-control-label']); ?>
                    <select v-model="purchase_id" name="purchase" class="form-control">
                      <option value="">Selecciona una orden (si existe)</option>
                      <option v-if="loadingOrders" disabled>Cargando órdenes...</option>
                      <option v-if="errorOrders" disabled>Error al cargar órdenes</option>
                      <option v-for="oc in orders" :value="oc.id">
                        {{ oc.code_order }} - ${{ oc.total }} – {{ oc.date }}
                      </option>
                      <option v-if="!loadingOrders && !errorOrders && orders.length === 0" disabled>
                        (Sin órdenes disponibles)
                      </option>
                    </select>
                    <small class="form-text text-muted">Si no aparece, puedes capturarla abajo.</small>
                  </div>
                  <div class="col-md-6 mb-3">
                    <?php echo Form::label('Orden de Compra manual', 'purchase_manual', ['class' => 'form-control-label']); ?>
                    <?php echo Form::input('purchase_manual', '', ['id' => 'purchase_manual', 'class' => 'form-control', 'placeholder' => 'Escribe la orden de compra']); ?>
                  </div>
                <?php endif; ?>

                <!-- Archivos -->
                <div class="col-md-6 mb-3">
                  <?php echo Form::label('Archivo PDF', 'pdf', ['class' => 'form-control-label']); ?>
                  <div class="custom-file">
                    <?php echo Form::file('pdf', ['id' => 'pdf', 'class' => 'custom-file-input', 'lang' => 'es']); ?>
                    <label class="custom-file-label" for="pdf">Seleccionar archivo PDF (máx. 20MB)</label>
                  </div>
                </div>
                <div class="col-md-6 mb-3">
                  <?php echo Form::label('Archivo XML', 'xml', ['class' => 'form-control-label']); ?>
                  <div class="custom-file">
                    <?php echo Form::file('xml', ['id' => 'xml', 'class' => 'custom-file-input', 'lang' => 'es']); ?>
                    <label class="custom-file-label" for="xml">Seleccionar archivo XML (máx. 20MB)</label>
                  </div>
                </div>
              </div>
            </fieldset>

            <div class="mt-3">
              <?php echo Form::submit(['value' => 'Subir Factura', 'name' => 'submit', 'class' => 'btn btn-primary']); ?>
              <?php echo Html::anchor('admin/compras/facturas', 'Cancelar', ['class' => 'btn btn-secondary']); ?>
            </div>
            <?php echo Form::close(); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Script Vue -->
<?= Asset::js('admin/compras/subir-factura-vue.js'); ?>
