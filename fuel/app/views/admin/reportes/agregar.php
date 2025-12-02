<!-- ENCABEZADO -->
<div class="header bg-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Agregar Nuevo Reporte</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
              <li class="breadcrumb-item">
                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
              </li>
              <li class="breadcrumb-item active">
                <?php echo Html::anchor('admin/reportes', 'Reportes'); ?>
              </li>
              <li class="breadcrumb-item active" aria-current="page">Nuevo</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- CONTENEDOR PRINCIPAL -->
<div id="reportes-app" class="container-fluid mt--6">
  <div class="row">
    <div class="col">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">Generador de Consultas</h5>
        </div>
        <div class="card-body">

          <!-- DATOS GENERALES -->
          <div class="row mb-3">
            <div class="col-md-4">
              <label>Nombre del reporte</label>
              <input v-model="query_name" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label>Departamento</label>
              <select v-model="departamento_id" class="form-control" required>
                <option value="">Seleccione...</option>
                <option v-for="d in departamentos" :key="d.id" :value="d.id">{{ d.name }}</option>
              </select>
            </div>
            <div class="col-md-4">
              <label>Descripción</label>
              <input v-model="description" class="form-control">
            </div>
          </div>

          <hr>

          <!-- PANEL PRINCIPAL DE CONSTRUCCIÓN -->
          <div class="row">
            <!-- PANEL IZQUIERDO -->
            <div class="col-md-3 border-right">
              <h6><i class="fas fa-database text-primary"></i> Tablas</h6>
              <select v-model="tablaSeleccionada" @change="seleccionarTabla" class="form-control mb-2">
                <option value="">Seleccione tabla...</option>
                <option v-for="t in tablas" :key="t.TABLE_NAME" :value="t.TABLE_NAME">{{ t.TABLE_NAME }}</option>
              </select>

              <!-- Campos de la tabla base -->
              <div class="mt-3 border rounded p-2" style="max-height:60vh; overflow-y:auto;">
                <p v-if="campos.length===0" class="text-muted">Seleccione una tabla para ver sus campos</p>
                <div v-for="c in campos" :key="c.COLUMN_NAME" class="form-check py-1">
                  <input type="checkbox" class="form-check-input" :id="c.COLUMN_NAME" @change="toggleCampo(c)">
                  <label class="form-check-label" :for="c.COLUMN_NAME">
                    {{ c.COLUMN_NAME }}
                    <small class="text-muted">({{ c.DATA_TYPE }})</small>
                  </label>
                </div>
              </div>
            </div>

            <!-- PANEL DERECHO -->
            <div class="col-md-9">

              <!-- CAMPOS DISPONIBLES -->
              <h6><i class="fas fa-columns text-primary"></i> Campos disponibles</h6>
              <div class="border rounded p-2 mb-3" style="max-height:220px; overflow-y:auto;">
                <div v-if="!tablaSeleccionada" class="text-muted">Seleccione una tabla base.</div>

                <!-- Tabla base -->
                <div v-if="tablaSeleccionada">
                  <strong class="text-success">{{ tablaSeleccionada }}</strong>
                  <ul class="list-group mb-2">
                    <li v-for="c in (tablaCampos[tablaSeleccionada] || [])"
                        :key="tablaSeleccionada + '.' + c.COLUMN_NAME"
                        class="list-group-item py-1 d-flex justify-content-between align-items-center">
                      <span>{{ c.COLUMN_NAME }}</span>
                      <button class="btn btn-sm btn-outline-primary"
                              @click="agregarCampo(tablaSeleccionada, c.COLUMN_NAME)">
                        <i class="fas fa-plus"></i>
                      </button>
                    </li>
                  </ul>
                </div>

                <!-- Tablas relacionadas (JOINs) -->
                <div v-for="j in joins" v-if="j.tabla" :key="'join-'+j.tabla">
                  <strong class="text-info">{{ j.tabla }}</strong>
                  <ul class="list-group mb-2">
                    <li v-for="c in (tablaCampos[j.tabla] || [])"
                        :key="j.tabla + '.' + c.COLUMN_NAME"
                        class="list-group-item py-1 d-flex justify-content-between align-items-center">
                      <span>{{ c.COLUMN_NAME }}</span>
                      <button class="btn btn-sm btn-outline-primary"
                              @click="agregarCampo(j.tabla, c.COLUMN_NAME)">
                        <i class="fas fa-plus"></i>
                      </button>
                    </li>
                  </ul>
                </div>
              </div>

              <!-- CAMPOS SELECCIONADOS -->
              <h6><i class="fas fa-table text-success"></i> Campos Seleccionados</h6>
              <table class="table table-sm table-bordered">
                <thead class="thead-light">
                  <tr>
                    <th>Campo</th>
                    <th>Alias</th>
                    <th>Acción</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(c,idx) in camposSeleccionados" :key="c.name">
                    <td>{{ c.name }}</td>
                    <td><input v-model="c.alias" class="form-control form-control-sm" placeholder="Alias opcional" @change="generarSQL"></td>
                    <td><button class="btn btn-danger btn-sm" @click="eliminarCampo(idx)"><i class="fas fa-times"></i></button></td>
                  </tr>
                </tbody>
              </table>

              <hr>

              <!-- CONDICIONES -->
              <h6><i class="fas fa-filter text-warning"></i> Condiciones (WHERE)</h6>
              <table class="table table-sm table-bordered">
                <thead class="thead-light">
                  <tr><th>Campo</th><th>Operador</th><th>Valor</th><th></th></tr>
                </thead>
                <tbody>
                  <tr v-for="(cond,i) in condiciones" :key="i">
                    <td>
                      <select v-model="cond.campo" class="form-control form-control-sm">
                        <option v-for="c in camposSeleccionados" :value="c.name">{{ c.name }}</option>
                      </select>
                    </td>
                    <td>
                      <select v-model="cond.operador" class="form-control form-control-sm">
                        <option>=</option><option>!=</option><option>&gt;</option><option>&lt;</option>
                        <option>LIKE</option><option>IN</option><option>BETWEEN</option>
                      </select>
                    </td>
                    <td><input v-model="cond.valor" class="form-control form-control-sm" placeholder="Valor o @param" @change="generarSQL"></td>
                    <td><button class="btn btn-danger btn-sm" @click="eliminarCondicion(i)"><i class="fas fa-times"></i></button></td>
                  </tr>
                </tbody>
              </table>
              <button class="btn btn-sm btn-outline-primary" @click="agregarCondicion">
                <i class="fas fa-plus"></i> Agregar condición
              </button>

              <hr>

              <!-- RELACIONES (JOINs) -->
              <h6><i class="fas fa-link text-info"></i> Relaciones (JOINs)</h6>
              <table class="table table-sm table-bordered">
                <thead class="thead-light">
                  <tr><th>Tipo</th><th>Tabla</th><th>Campo 1</th><th>Operador</th><th>Campo 2</th><th></th></tr>
                </thead>
                <tbody>
                  <tr v-for="(j,idx) in joins" :key="idx">
                    <td>
                      <select v-model="j.tipo" class="form-control form-control-sm">
                        <option>INNER</option><option>LEFT</option><option>RIGHT</option>
                      </select>
                    </td>
                    <td>
                      <select v-model="j.tabla" class="form-control form-control-sm" @change="obtenerCamposDeTabla(j.tabla)">
                        <option value="">Seleccione...</option>
                        <option v-for="t in tablas" :value="t.TABLE_NAME">{{ t.TABLE_NAME }}</option>
                      </select>
                    </td>
                    <td>
                      <select v-model="j.campo1" class="form-control form-control-sm">
                        <option value="">Seleccione...</option>
                        <option v-for="c in camposTablaBase" :value="tablaSeleccionada + '.' + c.COLUMN_NAME">
                          {{ tablaSeleccionada + '.' + c.COLUMN_NAME }}
                        </option>
                      </select>
                    </td>
                    <td>
                      <select v-model="j.operador" class="form-control form-control-sm">
                        <option>=</option><option>!=</option><option>&gt;</option><option>&lt;</option>
                      </select>
                    </td>
                    <td>
                      <select v-model="j.campo2" class="form-control form-control-sm">
                        <option value="">Seleccione...</option>
                        <option v-for="c in (tablaCampos[j.tabla] || [])" :value="j.tabla + '.' + c.COLUMN_NAME">
                          {{ j.tabla + '.' + c.COLUMN_NAME }}
                        </option>
                      </select>
                    </td>
                    <td>
                      <button class="btn btn-danger btn-sm" @click="eliminarJoin(idx)">
                        <i class="fas fa-times"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
              <button class="btn btn-sm btn-outline-primary" @click="agregarJoin">
                <i class="fas fa-plus"></i> Agregar JOIN
              </button>

              <hr>

              <!-- ORDEN -->
              <h6><i class="fas fa-sort-amount-down text-info"></i> Orden (ORDER BY)</h6>
              <select v-model="orderBy" class="form-control form-control-sm mb-3" style="width:50%;" @change="generarSQL">
                <option value="">Sin orden</option>
                <option v-for="c in camposSeleccionados" :value="c.name">{{ c.name }}</option>
              </select>

              <!-- SQL -->
              <div class="form-group">
                <label>Consulta SQL generada</label>
                <textarea v-model="querySQL" class="form-control" rows="8" required></textarea>
                <small class="text-muted">⚠️ Solo se permiten consultas SELECT. Puede editar manualmente si lo desea.</small>
              </div>

              <!-- BOTONES -->
              <div class="text-right">
                <button class="btn btn-info btn-sm" @click="probarConsulta"><i class="fas fa-play"></i> Probar Consulta</button>
                <button class="btn btn-success btn-sm" @click="guardarReporte"><i class="fas fa-save"></i> Guardar Reporte</button>
                <?php echo Html::anchor('admin/reportes', 'Cancelar', array('class'=>'btn btn-secondary btn-sm')); ?>
              </div>

              <!-- RESULTADO SCROLABLE -->
              <div v-if="resultado.length > 0" class="mt-4 border rounded p-2" style="max-height:400px; overflow:auto;">
                <table class="table table-bordered table-sm table-hover">
                  <thead class="thead-dark sticky-top">
                    <tr><th v-for="(v,k) in resultado[0]" :key="k">{{ k }}</th></tr>
                  </thead>
                  <tbody>
                    <tr v-for="(r,idx) in resultado" :key="idx">
                      <td v-for="(v,k) in r">{{ v }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>

            </div> <!-- /.col-md-9 -->
          </div> <!-- /.row -->
        </div> <!-- /.card-body -->
      </div> <!-- /.card -->
    </div>
  </div>
</div>

<?= Asset::js('admin/reportes-vue.js'); ?>
