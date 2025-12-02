<!-- CONTENT -->
<div class="header bg-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Subir Nota de Crédito (Admin)</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
              <li class="breadcrumb-item">
                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
              </li>
              <li class="breadcrumb-item">
                <?php echo Html::anchor('admin/compras/notasdecredito', 'Notas de Crédito'); ?>
              </li>
              <li class="breadcrumb-item active" aria-current="page">Subir</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- PAGE CONTENT -->
<div class="container-fluid mt--6" id="notascredito-app">
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-header border-0">
          <h3 class="mb-0">Formulario</h3>
        </div>
        <div class="card-body">

          <?php echo Form::open(['action'=>'admin/compras/notasdecredito/guardar','enctype'=>'multipart/form-data']); ?>

          <!-- PROVEEDOR -->
          <div class="form-group">
            <?php echo Form::label('Proveedor','provider_id'); ?>

            <v-select
              v-model="proveedorSeleccionado"
              :options="providers_opts"
              :get-option-label="p => `${p.rfc} - ${p.name}`"
              @search="buscarProveedores"
              @input="onSeleccionarProveedor"
              :loading="loadingProviders"
              :clearable="true"
              placeholder="Escribe para buscar proveedor..."
            />

            <!-- INPUT HIDDEN PARA EL FORM -->
            <input type="hidden" name="provider_id" :value="provider_id">
          </div>






          <!-- DESTINO -->
          <div class="form-group">
            <?php echo Form::label('Aplicar a','destino'); ?>
            <div>
              <label class="mr-3"><input type="radio" value="facturas" v-model="destino"> Factura(s)</label>
              <label class="mr-3"><input type="radio" value="oc" v-model="destino"> Orden de Compra</label>
              <label><input type="radio" value="saldo" v-model="destino"> Saldo a Favor</label>
            </div>
            <input type="hidden" name="destino" :value="destino">
          </div>

          <!-- FACTURAS -->
          <div v-if="destino === 'facturas'" class="mb-3">
            <h5>Facturas Relacionadas</h5>
            <div v-if="facturas_opts.length === 0" class="alert alert-info">
              Selecciona un proveedor para cargar sus facturas.
            </div>
            <table v-else class="table table-bordered">
              <thead class="thead-light">
                <tr>
                  <th>Sel</th>
                  <th>UUID</th>
                  <th>Total</th>
                  <th>Aplicar</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="f in facturas_opts" :key="f.id">
                  <td><input type="checkbox" v-model="seleccionFacturas[f.id].selected"></td>
                  <td>{{ f.uuid }}</td>
                  <td>${{ parseFloat(f.total).toFixed(2) }}</td>
                  <td>
                    <input type="number" step="0.01" class="form-control"
                           v-model="seleccionFacturas[f.id].amount">
                  </td>
                  <!-- hidden para enviar al backend -->
                  <input type="hidden" :name="'invoices['+f.id+'][selected]'" :value="seleccionFacturas[f.id]?.selected ? 1 : 0">
                  <input type="hidden" :name="'invoices['+f.id+'][amount]'" :value="seleccionFacturas[f.id]?.amount || 0">
                </tr>
              </tbody>
            </table>
          </div>

          <!-- ORDENES DE COMPRA -->
          <div v-if="destino === 'oc'" class="mb-3">
            <h5>Orden de Compra</h5>
            <div v-if="ocs_opts.length === 0" class="alert alert-info">
              Selecciona un proveedor para cargar sus órdenes de compra.
            </div>
            <select v-else v-model="ocSeleccionada" name="purchase_order_id" class="form-control">
              <option value="">Seleccione OC</option>
              <option v-for="oc in ocs_opts" :value="oc.id">
                OC #{{ oc.folio }} - ${{ parseFloat(oc.total).toFixed(2) }}
              </option>
            </select>
          </div>

          <!-- ARCHIVOS -->
          <div class="row">
            <div class="col-md-6 mb-3">
              <?php echo Form::label('XML','xml_file'); ?>
              <?php echo Form::file('xml_file',['class'=>'form-control']); ?>
            </div>
            <div class="col-md-6 mb-3">
              <?php echo Form::label('PDF (opcional)','pdf_file'); ?>
              <?php echo Form::file('pdf_file',['class'=>'form-control']); ?>
            </div>
          </div>

          <!-- OBSERVACIONES -->
          <div class="form-group">
            <?php echo Form::label('Observaciones','observations'); ?>
            <textarea name="observations" class="form-control" v-model="observations"></textarea>
          </div>

          <!-- TOTALES -->
          <div class="row">
            <div class="col-md-4 mb-3">
              <?php echo Form::label('Total Nota de Crédito','total'); ?>
              <input type="text" name="total" class="form-control" v-model="totalNota" readonly>
            </div>
            <div class="col-md-4 mb-3">
              <?php echo Form::label('Total Aplicado','total_aplicado'); ?>
              <input type="text" class="form-control" v-model="totalAplicado" readonly>
            </div>
            <div class="col-md-4 mb-3">
              <?php echo Form::label('Restante','restante'); ?>
              <input type="text" class="form-control" v-model="restante" readonly>
            </div>
          </div>

          <!-- BOTÓN -->
          <div class="form-group text-right">
            <?php echo Form::submit('submit','Guardar',['class'=>'btn btn-primary']); ?>
          </div>

          <?php echo Form::close(); ?>
        </div>
      </div>
    </div>
  </div>
</div>



<?= Asset::js('admin/compras/notasdecredito-vue.js'); ?>
