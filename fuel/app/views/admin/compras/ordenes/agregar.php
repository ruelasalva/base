<!-- ========================================= -->
<!-- HEADER -->
<!-- ========================================= -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Compras</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/compras/ordenes', 'Órdenes de Compra'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Agregar</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================= -->
<!-- PAGE CONTENT -->
<!-- ========================================= -->
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col">
            <div class="card-wrapper">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Agregar Orden de Compra</h3>
                    </div>
                    <div class="card-body">

                        <div id="oc-defaults" data-next-code="<?= $next_code; ?>"></div>
                        <div id="orden-compra-app">
                            <div v-if="loading" class="text-center p-5">
                                <i class="fas fa-spinner fa-spin fa-2x"></i><br>Cargando catálogos...
                            </div>

                            <div v-else-if="load_error" class="alert alert-danger">
                                <strong>Error:</strong> No se pudieron cargar los catálogos.<br>
                                Intenta recargar la página o contacta al administrador.
                            </div>

                            <div v-else>

                                <!-- =============================== -->
                                <!-- INFORMACIÓN GENERAL -->
                                <!-- =============================== -->
                                <fieldset>
                                  <div class="form-row">
                                    <div class="col-md-12 mt-0 mb-3">
                                      <legend class="mb-0 heading">Información general</legend>
                                    </div>

                                    <!-- PROVEEDOR -->
                                    <div class="col-md-6 mb-3 position-relative">
                                        <label>Proveedor</label>
                                        <input type="text"
                                              v-model="proveedor_nombre"
                                              class="form-control"
                                              placeholder="Buscar proveedor por nombre o código"
                                              @input="filtrarProveedores"
                                              :readonly="proveedorBloqueado">
                                        <div v-if="filteredProviders.length"
                                            class="list-group position-absolute w-100 mt-1 zindex-dropdown">
                                            <a v-for="prov in filteredProviders"
                                              :key="prov.id"
                                              href="#"
                                              class="list-group-item list-group-item-action py-1"
                                              @click.prevent="seleccionarProveedor(prov)">
                                              {{ prov.code }} - {{ prov.name }}
                                            </a>
                                        </div>
                                        <div v-if="proveedorBloqueado" class="mt-2 text-muted small">
                                            Orden vinculada a: <strong>{{ proveedor_nombre }}</strong>
                                        </div>
                                    </div>


                                    <!-- TIPO DE DOCUMENTO -->
                                    <div class="col-md-6 mb-3">
                                      <label>Tipo de Documento</label>
                                      <select v-model="document_type_id" class="form-control" required>
                                        <option value="">Selecciona</option>
                                        <option v-for="doc in document_type_opts" :value="doc.id">{{ doc.name }}</option>
                                      </select>
                                    </div>

                                    <!-- CÓDIGO DOCUMENTO BASE -->
                                    <div class="col-md-6 mb-3">
                                      <label>Código Documento Base</label>
                                      <div class="input-group">
                                        <input type="text" v-model="codigo_oc" class="form-control bg-light"
                                              placeholder="Se genera automáticamente" :readonly="!editarCodigoOc">
                                        <div class="input-group-append">
                                          <button class="btn btn-outline-secondary" type="button" @click="toggleCodigoOc"
                                                  :title="editarCodigoOc ? 'Bloquear campo' : 'Usar remisión física'">
                                            <i class="fas" :class="editarCodigoOc ? 'fa-lock' : 'fa-unlock'"></i>
                                          </button>
                                        </div>
                                      </div>
                                      <small class="text-muted">
                                        Sugerido: <?= Helper_OC::next_code(); ?> (automático si se deja bloqueado)
                                      </small>
                                    </div>

                                    <!-- FECHA CREACIÓN -->
                                    <div class="col-md-6 mb-3">
                                      <label>Fecha de creación</label>
                                      <input type="date" v-model="fecha" class="form-control bg-light" readonly>
                                    </div>

                                    <!-- FECHA DE PAGO (OCULTA) -->
                                    <input type="hidden" v-model="fecha_pago">

                                    <!-- NOTAS -->
                                    <div class="col-md-12 mb-3">
                                      <label>Notas</label>
                                      <textarea v-model="notas" class="form-control" rows="2" placeholder="Notas adicionales..."></textarea>
                                    </div>
                                  </div>
                                </fieldset>

                                <hr>

                                <div class="form-row">

                                <!-- =============================== -->
                                  <!-- TIPO GENERAL -->
                                  <!-- =============================== -->
                                  <div class="col-md-3 mb-3">
                                    <label>Tipo de operación</label>
                                    <select v-model="tipo_general" class="form-control">
                                      <option v-for="type in type_opts" :value="type.id">{{ type.label }}</option>
                                    </select>
                                  </div>

                                  <!-- CÓDIGO DE PRODUCTO -->
                                  <div class="col-md-3 mb-3">
                                      <label>Catálogo de producto</label>
                                      <select v-model="codigo_producto_tipo"
                                              class="form-control"
                                              :disabled="tipo_general === 'servicio'">
                                        <option value="interno">Código interno</option>
                                        <option value="proveedor">Código proveedor / orden</option>
                                      </select>

                                  </div>

                                </div>

                                <hr>
                                <!-- =============================== -->
                                <!-- DATOS PREDETERMINADOS (COLAPSADOS POR DEFAULT) -->
                                <!-- =============================== -->
                                <fieldset class="mb-3">
                                  <legend class="mb-0 heading">
                                    <a class="d-block text-decoration-none collapsed"
                                      data-toggle="collapse"
                                      href="#collapseValoresGenerales"
                                      role="button"
                                      aria-expanded="false"
                                      aria-controls="collapseValoresGenerales">
                                      <i class="fas fa-sliders-h mr-1"></i>Datos Predeterminados (Desplegar/ocultar)
                                      <i class="fas fa-chevron-down float-right"></i>
                                    </a>
                                  </legend>

                                  <div class="collapse mt-3" id="collapseValoresGenerales">
                                    <div class="card card-body border-light bg-light">
                                      <div class="form-row">
                                        <!-- IMPUESTO GENERAL -->
                                        <div class="col-md-3 mb-3">
                                          <label>Impuesto general</label>
                                          <select v-model="tax_general" class="form-control">
                                            <option v-for="tax in tax_opts" :value="tax.id">{{ tax.label }}</option>
                                          </select>
                                        </div>

                                        <!-- RETENCIÓN GENERAL -->
                                        <div class="col-md-3 mb-3">
                                          <label>Retención general</label>
                                          <select v-model="retention_general" class="form-control">
                                            <option value="">Sin retención</option>
                                            <option v-for="ret in retention_opts" :value="ret.id">{{ ret.label }}</option>
                                          </select>
                                        </div>

                                        <!-- MONEDA -->
                                        <div class="col-md-3 mb-3">
                                          <label>Moneda</label>
                                          <select v-model="moneda" class="form-control" required>
                                            <option v-for="mon in currency_opts" :value="mon.id">{{ mon.label }}</option>
                                          </select>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </fieldset>

                                <hr>

                                <!-- =============================== -->
                                <!-- PARTIDAS -->
                                <!-- =============================== -->
                                <fieldset>
                                  <legend class="mb-0 heading">Artículos / Servicios</legend>

                                  <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                      <thead class="thead-light">
                                        <tr>
                                          <th class="text-center">#</th>
                                          <th class="text-center">Tipo</th>
                                          <th class="text-center">Código / Producto</th>
                                          <th class="text-center">Descripción</th>
                                          <th class="text-center">Cantidad</th>
                                          <th class="text-center">Precio</th>
                                          <th class="text-center">Impuesto</th>
                                          <th class="text-center">Retención</th>
                                          <th class="text-center"></th>
                                        </tr>
                                      </thead>

                                      <tbody>
                                        <tr v-for="(item, idx) in partidas" :key="idx">
                                          <!-- # -->
                                          <td class="text-center align-middle">{{ idx + 1 }}</td>

                                          <!-- TIPO -->
                                          <td class="align-middle">
                                            <input type="text"
                                                  class="form-control bg-light text-center"
                                                  :value="type_opts.find(t => t.id === item.tipo)?.label || ''"
                                                  readonly>
                                          </td>

                                          <!-- CÓDIGO / PRODUCTO -->
                                          <td class="align-middle">
                                            <template v-if="item.tipo === 'articulo'">
                                              <input list="productos-lista"
                                                    v-model="item.code_product"
                                                    @change="autocompletarDescripcion(idx)"
                                                    class="form-control"
                                                    placeholder="Buscar código o producto">
                                              <datalist id="productos-lista">
                                                <option v-for="prod in productos_filtrados"
                                                        :value="prod.code + ' - ' + prod.name"></option>
                                              </datalist>
                                            </template>

                                            <template v-else>
                                              <input type="text"
                                                    v-model="item.code_product"
                                                    class="form-control"
                                                    placeholder="Código del servicio">
                                            </template>
                                          </td>

                                          <!-- DESCRIPCIÓN -->
                                          <td class="align-middle">
                                            <template v-if="item.tipo === 'servicio'">
                                              <input type="text"
                                                    v-model="item.description"
                                                    class="form-control"
                                                    placeholder="Descripción del servicio">
                                            </template>

                                            <template v-else>
                                              <input type="text"
                                                    v-model="item.description"
                                                    class="form-control bg-light"
                                                    placeholder="Descripción del producto"
                                                    readonly>
                                            </template>
                                          </td>

                                          <!-- CANTIDAD -->
                                          <td class="align-middle">
                                            <input type="number"
                                                  v-model.number="item.quantity"
                                                  class="form-control text-center"
                                                  min="0.01"
                                                  placeholder="0.00">
                                          </td>

                                          <!-- PRECIO -->
                                          <td class="align-middle">
                                            <input type="number"
                                                  v-model.number="item.unit_price"
                                                  class="form-control text-center"
                                                  min="0.01"
                                                  placeholder="$0.00">
                                          </td>

                                          <!-- IMPUESTO -->
                                          <td class="align-middle">
                                            <select v-model="item.tax_id" class="form-control text-center">
                                              <option value="">Sin impuesto</option>
                                              <option v-for="tax in tax_opts" :value="tax.id">{{ tax.label }}</option>
                                            </select>
                                          </td>

                                          <!-- RETENCIÓN -->
                                          <td class="align-middle">
                                            <select v-model="item.retention_id" class="form-control text-center">
                                              <option value="">Sin retención</option>
                                              <option v-for="ret in retention_opts" :value="ret.id">{{ ret.label }}</option>
                                            </select>
                                          </td>

                                          <!-- ACCIÓN -->
                                          <td class="text-center align-middle">
                                            <button type="button"
                                                    class="btn btn-danger btn-sm"
                                                    @click="eliminarPartida(idx)">
                                              <i class="fas fa-trash"></i>
                                            </button>
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>
                                      <div class="text-left mt-2">
                                        <button type="button"
                                                class="btn btn-outline-secondary btn-sm"
                                                @click="agregarPartida">
                                          <i class="fas fa-plus"></i> Agregar partida
                                        </button>
                                    </div>
                                  </div>

                                </fieldset>



                                <hr>

                                <!-- =============================== -->
                                <!-- TOTALES -->
                                <!-- =============================== -->
                                <div class="row mb-3">
                                    <div class="col-md-3 offset-md-5 text-right">
                                        <strong>Subtotal:</strong>
                                        <span class="badge badge-secondary">{{ subtotal | currency }}</span>
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <strong>Impuesto:</strong>
                                        <span class="badge badge-warning">{{ impuestoTotal | currency }}</span>
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <strong>Retención:</strong>
                                        <span class="badge badge-info">{{ retencionTotal | currency }}</span>
                                    </div>
                                    <div class="col-md-12 text-right mt-2">
                                        <strong>Total:</strong>
                                        <span class="badge badge-success">{{ total | currency }}</span>
                                    </div>
                                </div>

                                <hr>

                                <!-- =============================== -->
                                <!-- BOTONES -->
                                <!-- =============================== -->
                                <div class="btn-group mt-4">
                                    <button type="button" class="btn btn-primary"
                                            @click="guardarYVer" :disabled="loading" v-if="!modoEdicion">
                                        Agregar y ver
                                    </button>
                                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"
                                            data-toggle="dropdown" :disabled="loading" v-if="!modoEdicion">
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu" v-if="!modoEdicion">
                                        <a class="dropdown-item" href="#" @click.prevent="guardarYNuevo">Agregar y nuevo</a>
                                        <a class="dropdown-item" href="#" @click.prevent="guardarYVer">Agregar y ver</a>
                                        <a class="dropdown-item" href="#" @click.prevent="guardarYCerrar">Agregar y cerrar</a>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-secondary ml-2" @click="cancelar" :disabled="loading">Cancelar</button>

                            </div> <!-- /v-else -->
                        </div> <!-- /orden-compra-app -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
window.prefillProviderId = <?= (int)($prefill_provider_id ?? 0); ?>;
window.prefillProviderName = <?= json_encode($prefill_provider_name ?? ''); ?>;
</script>

<?= Asset::js('admin/compras/orden-compra-vue.js'); ?>
