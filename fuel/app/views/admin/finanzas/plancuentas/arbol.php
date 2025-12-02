<!-- CONTENT -->
<div class="header bg-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Plan de Cuentas</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark mb-0">
              <li class="breadcrumb-item">
                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
              </li>
              <li class="breadcrumb-item">
                <?php echo Html::anchor('admin/finanzas/plancuentas', 'Finanzas'); ?>
              </li>
              <li class="breadcrumb-item active" aria-current="page">
                Plan de Cuentas
              </li>
            </ol>
          </nav>
        </div>

        <!-- BOTONES DE ACCIÓN -->
        <div class="col-lg-6 col-5 text-right">
          <div class="btn-group" role="group">
            <?php echo Html::anchor('admin/finanzas/plancuentas/agregar', '<i class="fas fa-plus"></i> Agregar cuenta', ['class' => 'btn btn-sm btn-neutral']); ?>
          </div>
          <form action="<?= Uri::create('admin/finanzas/plancuentas/importar_csv'); ?>" method="post" enctype="multipart/form-data" class="d-inline-block align-middle ml-2">
            <div class="input-group input-group-sm" style="max-width:400px;">
              <input type="file" name="archivo" accept=".csv" class="form-control">
              <div class="input-group-append">
                <button type="submit" class="btn btn-success btn-sm">
                  <i class="fas fa-file-import"></i> Importar CSV
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
  <div id="plan-cuentas-app" v-cloak>
    <div class="row">

      <!-- FILTRO DE TIPOS -->
      <div class="col-lg-2 col-md-3 mb-4">
        <div class="card shadow-sm h-100">
          <div class="card-header text-white" style="background-color:#1e4194;">
            <strong>Tipos de cuenta</strong>
          </div>
          <div class="card-body p-2">
            <div class="list-group list-group-flush">
              <button class="list-group-item list-group-item-action py-2 text-center"
                      :class="{active: filtroTipo===''}"
                      @click="filtroTipo=''">Todas</button>
              <button v-for="t in tiposCuenta"
                      :key="t"
                      class="list-group-item list-group-item-action py-2 text-center"
                      :class="{active: filtroTipo===t}"
                      @click="filtroTipo=t">
                {{ t }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- ÁRBOL -->
      <div class="col-lg-5 col-md-5 mb-4">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <b>Estructura de Cuentas</b>
            <small class="text-white-50">{{ cuentasFiltradas.length }} cuentas</small>
          </div>
          <div class="card-body" style="max-height:65vh; overflow-y:auto;">
            <div v-if="loadingTree" class="alert alert-info mb-0">Cargando árbol...</div>
            <div v-else>
              <div v-if="!cuentasFiltradas.length" class="text-muted">Sin cuentas registradas.</div>
              <account-node
                v-for="n in cuentasFiltradas"
                :key="n.id"
                :node="n"
                :depth="0"
                @toggle="toggleNode"
                @select="selectNode"
                @add-child="addChild"
                @edit="editNode"
                @remove="removeNode"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- FORMULARIO DETALLE -->
      <div class="col-lg-5 col-md-4 mb-4">
        <div class="card shadow-sm">
          <div class="card-header bg-light d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Detalles de la cuenta</h5>
            <div>
              <button class="btn btn-success btn-sm" @click="nuevoRegistro">
                <i class="fa fa-plus"></i> Nuevo
              </button>
              <button class="btn btn-primary btn-sm" @click="formMode='edit'" v-if="form.id && formMode==='view'">
                <i class="fa fa-edit"></i> Editar
              </button>
            </div>
          </div>

          <div class="card-body">
            <form @submit.prevent="guardarCuenta">
              <div class="form-row">
                <div class="col-md-4 mb-3">
                  <label>Código</label>
                  <input type="text" v-model="form.code" class="form-control" :disabled="!isEditable">
                </div>

                <div class="col-md-8 mb-3">
                  <label>Nombre</label>
                  <input type="text" v-model="form.name" class="form-control" :disabled="!isEditable">
                </div>

                <div class="col-md-4 mb-3">
                <label>Tipo</label>
                <select v-model="form.type" class="form-control" :disabled="!isEditable">
                  <option>Ventas</option>
                  <option>Salidas</option>
                  <option>Otros</option>
                </select>
              </div>

                <div class="col-md-4 mb-3">
                  <label>Nivel</label>
                  <input type="number" v-model.number="form.level" class="form-control" :disabled="!isEditable">
                </div>

                <div class="col-md-4 mb-3">
                  <label>Moneda</label>
                  <select v-model="form.currency_id" class="form-control" :disabled="!isEditable">
                    <option value="">Sin moneda</option>
                    <option v-for="c in currencies" :key="c.id" :value="c.id">{{ c.code }} - {{ c.name }}</option>
                  </select>
                </div>

                <div class="col-md-6 mb-3">
                  <label>Cuenta Padre</label>
                  <select v-model="form.parent_id" class="form-control" :disabled="!isEditable">
                    <option value="">Sin cuenta padre</option>
                    <option v-for="c in accountsList" :key="c.id" :value="c.id">
                      {{ c.code }} - {{ c.name }}
                    </option>
                  </select>
                </div>

                <div class="col-md-6 mb-3">
                  <label>Clase de Cuenta</label>
                  <input type="text" v-model="form.account_class" class="form-control" :disabled="!isEditable">
                </div>

                <div class="col-md-4 mb-3">
                  <label>¿Confidencial?</label>
                  <select v-model.number="form.is_confidential" class="form-control" :disabled="!isEditable">
                    <option :value="0">No</option>
                    <option :value="1">Sí</option>
                  </select>
                </div>

                <div class="col-md-4 mb-3">
                  <label>¿Efectivo?</label>
                  <select v-model.number="form.is_cash_account" class="form-control" :disabled="!isEditable">
                    <option :value="0">No</option>
                    <option :value="1">Sí</option>
                  </select>
                </div>

                <div class="col-md-4 mb-3">
                  <label>¿Activa?</label>
                  <select v-model.number="form.is_active" class="form-control" :disabled="!isEditable">
                    <option :value="1">Sí</option>
                    <option :value="0">No</option>
                  </select>
                </div>

                <div class="col-md-6 mb-3">
                  <label>Código Anexo 24</label>
                  <input type="text" v-model="form.annex24_code" class="form-control" :disabled="!isEditable">
                </div>
              </div>

              <div class="text-right">
                <button type="submit" class="btn btn-primary" :disabled="!isEditable || saving">
                  <i class="fa fa-save"></i> {{ saving ? 'Guardando...' : 'Guardar' }}
                </button>
                <button type="button" class="btn btn-secondary" @click="cancelarEdicion">
                  Cancelar
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<?= Asset::js('admin/plan-cuentas-vue.js'); ?>

<style>
[v-cloak]{display:none}
.list-group-item.active {
  background-color:#1e4194;
  border-color:#1e4194;
  color:#fff;
  font-weight:bold;
}
.list-group-item:hover { background-color:#e8eefc; }
.card-header { font-weight:600; }
</style>
