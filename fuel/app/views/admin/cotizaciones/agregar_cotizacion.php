
<input type="hidden" id="cotizacion-pending-id" value="<?php echo Input::get('pending_id', ''); ?>">

<script>
window.access_id = <?= (int)Auth::get('id'); ?>;
window.access_token = '<?= md5(Auth::get('login_hash')); ?>';
window.pending_id = '<?= Input::get('pending_id', ''); ?>';

</script>

<div id="cotizacion-app" class="container-fluid px-4 py-3">

<!-- ALERTA DE RECUPERACIÓN DE COTIZACIÓN EN CURSO -->
<div v-if="cotizacionRecuperable" class="alert alert-danger d-flex justify-content-between align-items-center">
    <span>
        <strong>¡Tienes una cotización sin finalizar!</strong>
        Puedes continuar trabajando en ella o descartar los datos.
    </span>
    <div>
        <button class="btn btn-primary btn-sm mr-2" @click="aplicarCotizacionRecuperada">Recuperar</button>
        <button class="btn btn-danger btn-sm" @click="borrarCotizacionAutoguardada">Descartar</button>
    </div>
</div>

    <!-- HEADER Y BREADCRUMB -->
    <div class="row mb-3">
        <div class="col-8">
            <h4>
                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?> <?php echo Html::anchor('admin/cotizaciones', 'Cotización'); ?>
                <small class="text-muted">/ Agregar cotización</small>
            </h4>
        </div>
        <div class="col-12 col-md-4 text-md-right mt-3 mt-md-0 d-flex flex-column flex-md-row justify-content-md-end align-items-stretch gap-2">
  <button @click="descargarCatalogos" class="btn btn-success btn-sm mb-2 mb-md-0 mr-md-2 w-100 w-md-auto btn-block">
    Descargar Catálogos Offline <br><span v-if="fechaCatalogos">({{ fechaCatalogos }})</span>
  </button>
  <button id="btn-sincronizar-cotizaciones" style="display:none" class="btn btn-warning btn-sm mb-2 mb-md-0 mr-md-2 w-100 w-md-auto btn-block">
    <i class="fa fa-sync"></i> Sincronizar cotizaciones offline
  </button>
  <button class="btn btn-danger btn-sm w-100 w-md-auto btn-block" @click="reiniciarFormulario">
    <i class="ni ni-fat-remove"></i> Reiniciar Formulario
  </button>
</div>

    </div>

    <!-- 1. DATOS DEL SOCIO -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <b>Datos del Socio</b>
        </div>
        <div class="card-body">
          <div class="form-group">
            <label for="partner_id"><b>Socio de Negocio</b></label>
            <div class="input-group">
              <div class="form-control p-0" style="border:none;">
                <v-select
                  v-model="socioSeleccionado"
                  :options="partners_opts"
                  :get-option-label="option => `${option.code_sap} - ${option.name}`"
                  @search="buscarSocios"
                  :loading="loadingPartners"
                  @input="onSeleccionarSocio"
                  :disabled="bloqueadoSocio"
                  placeholder="Escribe para buscar socio..."
                  ref="socioSelect"
                  :clearable="false"
                  :class="{ 'is-invalid': errores.partner_id }"
                />
              </div>
              <div class="input-group-append">
                <button
                  class="btn btn-warning btn-block"
                  type="button"
                  @click="reiniciarSocio"
                  :disabled="!socioSeleccionado"
                  style="min-width:110px; font-size:0.92rem; white-space:nowrap;"
                >
                  <i class="ni ni-refresh-02"></i> Cambiar socio
                </button>
              </div>
            </div>
          </div>

          <div class="form-row">
              <div class="form-group col-12 col-md-4">
                  <div class="form-group">
                    <label for="partner_contact_id">Contacto</label>
                    <div class="input-group">
                        <select v-model="partner_contact_id" class="form-control" :disabled="!socioSeleccionado" :class="{ 'is-invalid': errores.partner_contact_id }">
                          <option value="">Selecciona contacto</option>
                            <option v-for="c in contacts_opts" :value="c.id">
                              {{ c.name }}{{ c.last_name ? ' ' + c.last_name : '' }}{{ c.email ? ' (' + c.email + ')' : '' }}
                            </option>
                        </select>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-primary" @click="abrirModalContacto">
                                <i class="ni ni-fat-add"></i> Agregar
                            </button>
                        </div>
                    </div>
                </div>
              </div>
              <div class="form-group col-12 col-md-4">
                  <label>Referencia</label>
                  <input type="text" class="form-control" v-model="reference" maxlength="30" :disabled="!socioSeleccionado" :class="{ 'is-invalid': errores.reference }">
              </div>
              <div class="form-group col-12 col-md-4">
                  <label>Válido Hasta</label>
                  <input type="date" class="form-control" v-model="valid_date" :disabled="!socioSeleccionado" :class="{ 'is-invalid': errores.valid_date }">
              </div>
          </div>
          <div v-if="vendedorAsignado" class="alert alert-info py-2 mt-2">
              <i class="ni ni-single-02"></i>
              Vendedor asignado: <b>{{ vendedorAsignado }}</b>
          </div>
      </div>


    </div>

    <!-- 2. VALORES GENERALES -->
    <div class="card shadow mb-4">
      <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
          <b>Valores Generales / Impuestos, Retenciones, Monedas, Descuentos</b>
          <button class="btn btn-sm btn-light text-dark" type="button" data-toggle="collapse" data-target="#collapseValoresGenerales" aria-expanded="false">
              Mostrar / Ocultar
          </button>
      </div>
      <div class="collapse" id="collapseValoresGenerales">
          <div class="card-body">
              <div class="form-row">
                  <!-- IMPUESTO GENERAL -->
                  <div class="form-group col-md-3">
                      <label>Impuesto General</label>
                      <select class="form-control" v-model="tax_general">
                          <option value="">Selecciona impuesto</option>
                          <option v-for="tax in tax_opts" :value="tax.id">{{ tax.name }}</option>
                      </select>
                  </div>

                  <!-- RETENCIÓN GENERAL -->
                  <div class="form-group col-md-3">
                      <label>Retención General</label>
                      <select class="form-control" v-model="retention_general">
                          <option value="">Sin retención</option>
                          <option v-for="r in retention_opts" :value="r.id">{{ r.name }}</option>
                      </select>
                  </div>

                  <!-- MONEDA -->
                  <div class="form-group col-md-3">
                      <label>Moneda</label>
                      <select class="form-control" v-model="moneda_id">
                          <option value="">Selecciona moneda</option>
                          <option v-for="m in moneda_opts" :value="m.id">{{ m.name }}</option>
                      </select>
                  </div>

                  <!-- DESCUENTO GENERAL -->
                  <div class="form-group col-md-3">
                      <label>Descuento General</label>
                      <select class="form-control" v-model="discount_general_id">
                          <option value="">Selecciona descuento</option>
                          <option v-for="d in discounts_opts" :value="d.id">
                              {{ d.name }}<span v-if="d.final_effective && d.final_effective > 0"> </span>
                          </option>
                      </select>
                  </div>
              </div>
          </div>
      </div>
  </div>

    <!-- 3. CAPTURA DE PRODUCTOS / SERVICIOS -->
<div class="card shadow mb-4">
    <!-- ENCABEZADO DE LA TARJETA -->
    <div class="card-header bg-warning text-black">
        <b>Productos y Servicios</b>
    </div>
    <div class="card-body">
        <!-- FORMULARIO DE CAPTURA DE PARTIDAS -->
        <div class="form-row align-items-end">
            <!-- CAMPO: TIPO DE PARTIDA -->
            <div class="form-group col-md-2 col-6">
                <label>Tipo</label>
                <select class="form-control" v-model="tipo_partida">
                    <option value="articulo">Artículo</option>
                    <option value="servicio">Servicio</option>
                </select>
            </div>
            <!-- CAMPO: PRODUCTO (vue-select) -->
            <div class="form-group col-md-4 col-12">
                <label>Buscar producto/servicio</label>
                <v-select
                    :options="products_opts"
                    :get-option-label="option => `${option.code} - ${option.name}`"
                    @search="buscarProductos"
                    @input="onSeleccionarProducto"
                    placeholder="Buscar producto..."
                    ref="productoSelect"
                />
            </div>
            <!-- CAMPO: Existencias -->
            <div class="form-group col-md-1 col-2">
                <label>Existencias</label>
                <input type="number" class="form-control"
                    :value="nuevo_available"
                    disabled
                    tabindex="-1"
                    style="background:#eee; pointer-events:none;">  
            </div> 
            <!-- CAMPO: PRECIO UNITARIO -->
            <div class="form-group col-md-2 col-6">
                <label>Precio Unitario</label>
                <input type="number" class="form-control"
                    v-model="nuevo_unit_price"
                    :disabled="!productoSeleccionado"
                    ref="inputPrecio"
                    @keydown.enter.prevent="focusCantidad">
            </div>
            <!-- CAMPO: CANTIDAD -->
            <div class="form-group col-md-2 col-6">
                <label>Cantidad</label>
                <input type="number" class="form-control"
                    v-model="nuevo_quantity"
                    min="1"
                    :disabled="!productoSeleccionado"
                    ref="inputCantidad"
                    @keyup.enter="agregarProducto">
            </div>
            <!-- IMAGEN DEL PRODUCTO -->
            <div class="form-group col-md-2 col-6 text-center">
                <img :src="nuevo_product_image" alt="Imagen producto"
                    class="img-thumbnail mx-auto d-block"
                    style="max-width:150px; max-height:150px;">
            </div>
        </div>
        <!-- BOTONES PARA AGREGAR MASIVAMENTE -->
        <div class="form-row mt-2">
            <div class="col-12 col-md-4 mb-2 mb-md-0 d-flex flex-wrap">
                <button type="button" class="btn btn-info btn-sm mr-2 mb-2" data-toggle="modal" data-target="#modal-agregar-marca">
                    <i class="ni ni-collection"></i> Agregar por Marca
                </button>
                <button type="button" class="btn btn-info btn-sm mb-2" data-toggle="modal" data-target="#modal-agregar-rango">
                    <i class="ni ni-bullet-list-67"></i> Agregar por Rango
                </button>
            </div>
            <div class="col-12 col-md-4 mb-2 mb-md-0 d-flex align-items-center justify-content-center">
                <button type="button" class="btn btn-primary"
                    @click="agregarProducto"
                    :disabled="!productoSeleccionado || !nuevo_quantity">
                    <i class="ni ni-fat-add"></i> Agregar
                </button>
            </div>
            <div class="col-12 col-md-4 text-md-right text-center d-flex justify-content-end align-items-center">
                <!-- Espacio reservado para imagen de producto si quieres mostrarla en la botonera -->
            </div>
        </div>
        <!-- TABLA DE PARTIDAS AGREGADAS -->
        <div class="table-responsive mt-4">
            <table class="table table-bordered table-striped table-sm mb-0" id="productlist">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th v-if="showDescuento">Descuento</th>
                        <th>Imagen</th>
                        <th v-if="showImpuesto">Impuesto</th>
                        <th v-if="showRetencion">Retención</th>
                        <th>Total</th>
                        <th class="text-center">
                            <!-- Botón Editar columnas dentro del encabezado -->
                            <button type="button"
                                class="btn btn-link btn-sm p-0 ml-2"
                                data-toggle="modal"
                                data-target="#modal-editar-columnas"
                                style="vertical-align: middle;">
                                <i class="ni ni-settings"></i> <span class="d-none d-md-inline">Editar columnas</span>
                            </button><br>
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, idx) in partidas" :key="item.id || idx">
                        <td>{{ item.tipo || tipo_partida }}</td>
                        <td>{{ item.code }}</td>
                        <td>{{ item.name }}</td>
                        <td>
                            <input type="number"
                                v-model.number="item.quantity"
                                min="1"
                                class="form-control form-control-sm"
                                @change="recalculaTotales()">
                        </td>
                        <td>
                            <input type="number"
                                v-model.number="item.unit_price"
                                min="0"
                                step="0.01"
                                class="form-control form-control-sm"
                                @change="recalculaTotales()">
                        </td>
                        <!-- DESCUENTO POR PARTIDA -->
                        <td v-if="showDescuento">
                            <input type="number"
                                v-model.number="item.discount"
                                min="0"
                                max="100"
                                step="0.01"
                                class="form-control form-control-sm"
                                placeholder="%"
                                @change="recalculaTotales()">
                        </td>
                        <!-- IMAGEN DEL PRODUCTO -->
                        <td class="text-center align-middle">
                          <img
                            :src="getProductThumb(item.image)"
                            alt="Imagen producto"
                            class="img-thumbnail mx-auto d-block"
                            style="max-width:70px; max-height:70px;"
                            @error="setDefaultImagePartida($event)"
                          >
                        </td>
                        <!-- CAMPO IMPUESTO -->
                        <td v-if="showImpuesto">
                            <select class="form-control form-control-sm" v-model="item.tax_id">
                                <option value="">--</option>
                                <option v-for="tax in tax_opts" :value="tax.id">{{ tax.name }}</option>
                            </select>
                        </td>
                        <!-- CAMPO RETENCIÓN -->
                        <td v-if="showRetencion">
                            <select class="form-control form-control-sm" v-model="item.retention_id">
                                <option value="">--</option>
                                <option v-for="r in retention_opts" :value="r.id">{{ r.name }}</option>
                            </select>
                        </td>
                        <!-- TOTAL -->
                        <td>
                            {{ calcularTotalPartida(item) | currency }}
                        </td>
                        <!-- ACCIONES -->
                        <td class="text-center">
                            <button class="btn btn-danger btn-sm" @click="eliminarProducto(idx)">
                                <i class="ni ni-fat-remove"></i>
                            </button>
                        </td>
                    </tr>
                    <tr v-if="partidas.length === 0">
                        <td :colspan="8 + (showImpuesto ? 1 : 0) + (showRetencion ? 1 : 0) + (showDescuento ? 1 : 0)" class="text-center text-muted">
                            Sin productos agregados
                        </td>
                    </tr>
                </tbody>
                <tfoot v-if="partidas.length">
                  <tr>
                    <td :colspan="9 + (showImpuesto ? 1 : 0) + (showRetencion ? 1 : 0) + (showDescuento ? 1 : 0)" class="text-right font-weight-bold">Subtotal:</td>
                    <td class="font-weight-bold">{{ subtotal | currency }}</td>
                  </tr>
                  <tr>
                    <td :colspan="9" class="text-right font-weight-bold">Descuento:</td>
                    <td class="font-weight-bold">{{ descuento| currency }}</td>
                  </tr>
                  <tr>
                    <td :colspan="9 + (showImpuesto ? 1 : 0) + (showRetencion ? 1 : 0) + (showDescuento ? 1 : 0)" class="text-right font-weight-bold">Retención:</td>
                    <td class="font-weight-bold">{{ retencion | currency }}</td>
                  </tr>
                  <tr>
                    <td :colspan="9 + (showImpuesto ? 1 : 0) + (showRetencion ? 1 : 0) + (showDescuento ? 1 : 0)" class="text-right font-weight-bold">IVA:</td>
                    <td class="font-weight-bold">{{ iva | currency }}</td>
                  </tr>
                  <tr class="table-info">
                    <td :colspan="9 + (showImpuesto ? 1 : 0) + (showRetencion ? 1 : 0) + (showDescuento ? 1 : 0)" class="text-right font-weight-bold">Total:</td>
                    <td class="font-weight-bold">{{ total | currency }}</td>
                  </tr>
                </tfoot>

            </table>
        </div>
    </div>
</div>



    <!-- 4. OBSERVACIONES -->
    <div class="form-row mb-4">
        <div class="form-group col-12">
            <label>Observaciones</label>
            <textarea class="form-control" v-model="comments" rows="2"></textarea>
        </div>
    </div>

    <!-- 5. ENTREGA, PAGO Y VENDEDOR -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <b>Entrega, Pago y Vendedor</b>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label for="address_id">Domicilio de entrega</label>
                    <div class="input-group">
                        <select v-model="address_id" class="form-control" :disabled="!socioSeleccionado" :class="{ 'is-invalid': errores.address_id }">
                            <option value="">Selecciona domicilio</option>
                            <option v-for="a in addresses_opts" :value="a.id">
                                {{ a.text }}
                            </option>
                        </select>
                        <div class="input-group-append">
                            <button class="btn btn-warning btn-sm"
                                    type="button"
                                    @click="abrirModalDomicilio"
                                    :disabled="!partner_id">
                                <i class="fa fa-plus"></i> Agregar domicilio de entrega
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-3">
                    <label>Método de Pago</label>
                    <select class="form-control" v-model="payment_id" :disabled="!camposEntregaActivos">
                        <option value="">Selecciona método</option>
                        <option v-for="p in payments_opts" :value="p.id">{{ p.name }}</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label>Vendedor</label>
                    <select class="form-control" v-model="employee_id" :disabled="!camposEntregaActivos">
                        <option value="">Selecciona vendedor</option>
                        <option v-for="e in employees_opts" :value="e.id">{{ e.name }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- TOTALES -->
    <div class="row mb-4">
        <div class="col-md-2"><b>Descuento aplicado:</b> <span>{{ descuento | currency }}</span> <br><b>Subtotal sin descuento: <span>{{ totalsindescuento | currency }}</span></b></div>
        <div class="col-md-2"><b>Subtotal:</b> <span>{{ subtotal | currency }}</span></div>
        <div class="col-md-2"><b>Impuesto:</b> <span>{{ iva | currency }}</span></div>
        <div class="col-md-2"><b>Retención:</b> <span>{{ retencion | currency }}</span></div>
        <div class="col-md-2"><b>Total:</b> <span>{{ total | currency }}</span></div>
    </div>

    <!-- BOTONES DE ACCIÓN -->
    <div class="row mb-5">
    <div class="col text-right d-flex flex-wrap justify-content-end align-items-center">
        
        <!-- Botón Guardar cotización -->
          <button
              class="btn btn-primary mr-2 mb-2"
              @click="descargarPdfCotizacion"
              v-if="cotizacionGuardada">
              <i class="ni ni-cloud-download-95"></i> Descargar PDF
          </button>
        <!-- Botón Imprimir cotización -->
        <button 
            class="btn btn-success mr-2 mb-2"
            @click="imprimirCotizacion"
            v-if="cotizacionGuardada">
            <i class="ni ni-single-copy-04"></i> Imprimir cotización
        </button>

        <!-- Botón Enviar por correo (fuera del btn-group y con margen a la derecha) -->
        <button 
            class="btn btn-info mr-2 mb-2"
            @click="abrirModalCorreo"
            v-if="cotizacionGuardada"   
            :disabled="!cotizacionGuardada">
            <i class="ni ni-email-83"></i> Enviar cotización por correo
        </button>

        <!-- Botones de finalizar cotización -->
        <div class="btn-group mb-2 mr-2">
            <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                Agregar y ver
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                <button class="dropdown-item" @click="finalizarCotizacion('nuevo')">Agregar y Nuevo</button>
                <button class="dropdown-item" @click="finalizarCotizacion('ver')">Agregar y ver</button>
                <button class="dropdown-item" @click="finalizarCotizacion('cerrar')">Agregar y cerrar</button>
            </div>
        </div>
        <button class="btn btn-secondary mb-2" @click="reiniciarFormulario">
            Cancelar
        </button>
    </div>
</div>




<!-- MODAL AGREGAR POR MARCA -->
<div class="modal fade" id="modal-agregar-marca" tabindex="-1" role="dialog" aria-labelledby="titulo-modal-marca" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content shadow-lg">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="titulo-modal-marca">
          <i class="ni ni-collection"></i> Agregar productos por <b>Marca</b>
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar" @click="limpiarModalMarca">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body px-4 pb-2">
        <!-- SELECTOR AUTOCOMPLETE vue-select -->
        <div class="form-row mb-3 align-items-end">
          <div class="form-group col-12 col-md-6 mb-0">
            <label class="font-weight-bold mb-1">Selecciona marca</label>
            <v-select
              :options="marcas_opts"
              label="name"
              v-model="marcaSeleccionada"
              @input="onSeleccionarMarca"
              :filterable="true"
              placeholder="Buscar o seleccionar marca..."
            />
          </div>
          <div class="form-group col-12 col-md-6 mb-0 d-flex align-items-end justify-content-end">
            <label class="mb-0 mr-3">
              <input type="checkbox" v-model="todosSeleccionados" @change="toggleSeleccionarTodos"> Seleccionar todos
            </label>
          </div>
        </div>
        <!-- TABLA DE PRODUCTOS POR MARCA -->
        <div class="table-responsive" style="max-height: 48vh;">
          <table class="table table-sm table-hover table-bordered mb-0">
            <thead class="thead-light">
              <tr>
                <th style="width:32px;"></th>
                <th style="width:60px;">Imagen</th>
                <th>Código</th>
                <th>Descripción</th>
                <th>Existencia</th>
                <th>Precio</th>
                <th style="width:90px;">Cantidad</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(prod, idx) in productos_marca" :key="prod.id">
                <td class="align-middle text-center">
                  <input type="checkbox" v-model="prod.selected">
                </td>
                <td class="align-middle text-center">
                  <img
                    :src="getProductThumb(prod.image_url)"
                    alt="Imagen"
                    class="rounded shadow-sm"
                    style="width:45px;height:45px;object-fit:cover;"
                    @error="setDefaultImagePartida($event)"
                  >
                </td>
                <td class="align-middle"><span class="badge badge-info">{{ prod.code }}</span></td>
                <td class="align-middle">{{ prod.name }}</td>
                <td class="align-middle text-center">
                  <span v-if="parseFloat(prod.available) > 0" class="badge badge-success">{{ prod.available }}</span>
                  <span v-else class="badge badge-danger">{{ prod.available || 0 }}</span>
                </td>
                <td class="align-middle" :class="{ 'bg-danger text-white': prod.price == 0 }">
                  <input
                    type="number"
                    class="form-control form-control-sm"
                    v-model.number="prod.price"
                    min="0"
                    step="0.01"
                    :disabled="!prod.selected"
                  >
                </td>
                <td class="align-middle">
                  <input type="number" class="form-control form-control-sm" v-model.number="prod.cantidad" min="1" :disabled="!prod.selected">
                </td>
              </tr>
              <tr v-if="productos_marca.length === 0">
                <td colspan="7" class="text-center text-muted">Sin productos para esta marca</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer d-flex justify-content-between">
        <button class="btn btn-primary px-4" @click="agregarSeleccionadosMarca" data-dismiss="modal">
          <i class="ni ni-fat-add"></i> Agregar seleccionados
        </button>
        <button type="button" class="btn btn-secondary px-4" data-dismiss="modal" @click="limpiarModalMarca">
          Cancelar
        </button>
      </div>
    </div>
  </div>
</div>



<!-- MODAL AGREGAR POR RANGO -->
<div class="modal fade" id="modal-agregar-rango" tabindex="-1" role="dialog" aria-labelledby="titulo-modal-rango" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content shadow-lg">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="titulo-modal-rango">
          <i class="ni ni-bullet-list-67"></i> Agregar productos por <b>Rango de Códigos</b>
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body px-4 pb-2">
        <div class="form-row align-items-end mb-3">
          <div class="form-group col-md-5">
            <label class="font-weight-bold mb-1">Código inicio</label>
            <input type="text" class="form-control" v-model="rango_inicio">
          </div>
          <div class="form-group col-md-5">
            <label class="font-weight-bold mb-1">Código fin</label>
            <input type="text" class="form-control" v-model="rango_fin">
          </div>
          <div class="form-group col-md-2">
            <button class="btn btn-info btn-block mt-3" @click="buscarProductosRango">
              <i class="ni ni-zoom-split-in"></i> Buscar<br> Productos
            </button>
          </div>
        </div>
        <div class="table-responsive" style="max-height: 45vh;">
          <table class="table table-sm table-hover table-bordered mb-0">
            <thead class="thead-light">
              <tr>
                <th style="width:32px;"></th>
                <input type="checkbox" v-model="todosSeleccionadosRango" @change="seleccionarTodosRango"> Seleccionar Todos
                <th style="width:60px;">Imagen</th>
                <th>Código</th>
                <th>Descripción</th>
                <th>Existencia</th>
                <th>Precio</th>
                <th style="width:90px;">Cantidad</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(prod, idx) in productos_rango" :key="prod.id">
                <td class="align-middle text-center">
                  <input type="checkbox" v-model="prod.selected">
                </td>
                <td class="align-middle text-center">
                  <img
                    :src="getProductThumb(prod.image_url)"
                    alt="Imagen"
                    class="rounded shadow-sm"
                    style="width:45px;height:45px;object-fit:cover;"
                    @error="setDefaultImagePartida($event)"
                  >
                </td>
                <td class="align-middle"><span class="badge badge-info">{{ prod.code }}</span></td>
                <td class="align-middle">{{ prod.name }}</td>
                <td class="align-middle text-center"><b>{{ prod.stock || 0 }}</b></td>
                <td class="align-middle" :class="{'bg-warning text-danger font-weight-bold': !prod.price || prod.price == 0}">
                  <input
                    type="number"
                    class="form-control form-control-sm"
                    v-model.number="prod.price"
                    min="0"
                    step="0.01"
                    :disabled="!prod.selected"
                  >
                </td>
                <td class="align-middle">
                  <input type="number" class="form-control form-control-sm" v-model.number="prod.cantidad" min="1" :disabled="!prod.selected">
                </td>
              </tr>
              <tr v-if="productos_rango.length === 0">
                <td colspan="7" class="text-center text-muted">Sin productos en este rango</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer d-flex justify-content-between">
        <button class="btn btn-primary px-4" @click="agregarSeleccionadosRango" data-dismiss="modal">
          <i class="ni ni-fat-add"></i> Agregar seleccionados
        </button>
        <button type="button" class="btn btn-secondary px-4" data-dismiss="modal" @click="cerrarModalRango">
          Cancelar
        </button>
      </div>
    </div>
  </div>
</div>



<!-- MODAL PARA EDITAR COLUMNAS DE LA TABLA DE PARTIDAS -->
<div class="modal fade" id="modal-editar-columnas" tabindex="-1" role="dialog" aria-labelledby="modalColumnasLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content shadow">
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold" id="modalColumnasLabel">
          <i class="ni ni-settings-gear-65"></i> Editar columnas
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" id="colImpuesto" v-model="showImpuesto">
          <label class="form-check-label" for="colImpuesto">
            Mostrar columna <b>Impuesto</b>
          </label>
        </div>
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" id="colRetencion" v-model="showRetencion">
          <label class="form-check-label" for="colRetencion">
            Mostrar columna <b>Retención</b>
          </label>
        </div>
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" id="colDescuento" v-model="showDescuento">
          <label class="form-check-label" for="colDescuento">
            Mostrar columna <b>Descuento</b>
          </label>
        </div>
      </div>
      <div class="modal-footer p-2">
        <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


<!-- Modal Enviar por Correo -->
<div class="modal fade" id="modal-correo-cotizacion" tabindex="-1" role="dialog" aria-labelledby="modalCorreoLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="modalCorreoLabel">
          <i class="ni ni-email-83"></i> Enviar cotización por correo
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Correo principal del socio -->
        <div class="form-group">
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="checkCorreoPrincipal"
              v-model="correoPrincipalSeleccionado">
            <label class="custom-control-label" for="checkCorreoPrincipal">
              Enviar a correo principal del socio:
              <b>{{ correoSocioPrincipal || 'No disponible' }}</b>
            </label>
          </div>
        </div>
        <!-- Correos adicionales -->
        <div class="form-group">
          <label>Correos adicionales (separa por coma):</label>
          <input type="text" class="form-control" v-model="correosAdicionales"
            placeholder="ejemplo@correo.com, otro@dominio.com">
        </div>
        <!-- Respuesta o alerta -->
        <div v-if="alertaCorreo" class="alert alert-danger py-2">
          {{ alertaCorreo }}
        </div>
        <div v-if="exitoCorreo" class="alert alert-success py-2">
          {{ exitoCorreo }}
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          Cancelar
        </button>
        <button type="button" class="btn btn-info" @click="enviarCotizacionCorreo">
          <i class="ni ni-send"></i> Enviar ahora
        </button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalContacto" tabindex="-1" role="dialog" aria-labelledby="modalContactoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form @submit.prevent="guardarNuevoContacto">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalContactoLabel">Agregar nuevo contacto</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Nombre(s)</label>
            <input v-model="nuevoContacto.name" type="text" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Apellido(s)</label>
            <input v-model="nuevoContacto.last_name" type="text" class="form-control">
          </div>
          <div class="form-group">
            <label>Email</label>
            <input v-model="nuevoContacto.email" type="email" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Teléfono</label>
            <input v-model="nuevoContacto.phone" type="text" class="form-control">
          </div>
          <!-- Más campos si quieres -->
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>


<div v-if="mostrarModalDomicilio" class="modal-backdrop fade show"></div>
<div 
    v-if="mostrarModalDomicilio" 
    class="modal fade show"
    style="display:block;" 
    tabindex="-1" 
    role="dialog"
    aria-labelledby="modalDomicilioTest"
>
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="modalDomicilioTest">Agregar Domicilio de Entrega</h5>
        <button type="button" class="close text-white" @click="cerrarModalDomicilio">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form @submit.prevent="guardarNuevoDomicilio">
        <div class="modal-body">
          <!-- Mensaje de error general -->
          <div v-if="erroresDomicilio.length" class="alert alert-danger py-2">
            <ul>
              <li v-for="e in erroresDomicilio" :key="e">{{ e }}</li>
            </ul>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Identificador *</label>
              <input v-model="nuevoDomicilio.iddelivery" :class="{'is-invalid': errores.iddelivery}" class="form-control" maxlength="50">
            </div>
            <div class="form-group col-md-6">
              <label>Calle *</label>
              <input v-model="nuevoDomicilio.street" :class="{'is-invalid': errores.street}" class="form-control">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-3">
              <label>Número *</label>
              <input v-model="nuevoDomicilio.number" :class="{'is-invalid': errores.number}" class="form-control">
            </div>
            <div class="form-group col-md-3">
              <label>Interior</label>
              <input v-model="nuevoDomicilio.internal_number" class="form-control">
            </div>
            <div class="form-group col-md-3">
              <label>Colonia *</label>
              <input v-model="nuevoDomicilio.colony" :class="{'is-invalid': errores.colony}" class="form-control">
            </div>
            <div class="form-group col-md-3">
              <label>Código Postal *</label>
              <input v-model="nuevoDomicilio.zipcode" :class="{'is-invalid': errores.zipcode}" class="form-control">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-4">
              <label>Ciudad *</label>
              <input v-model="nuevoDomicilio.city" :class="{'is-invalid': errores.city}" class="form-control">
            </div>
            <div class="form-group col-md-4">
              <label>Municipio</label>
              <input v-model="nuevoDomicilio.municipality" class="form-control">
            </div>
            <div class="form-group col-md-4">
              <label>Estado *</label>
              <select v-model="nuevoDomicilio.state" :class="{'is-invalid': errores.state}" class="form-control">
                <option value="">Selecciona</option>
                <option v-for="estado in estados_opts" :key="estado.id" :value="estado.id">
                  {{ estado.name }}
                </option>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Horario de recepción *</label>
              <input v-model="nuevoDomicilio.reception_hours" :class="{'is-invalid': errores.reception_hours}" class="form-control">
            </div>
            <div class="form-group col-md-6">
              <label>Notas</label>
              <input v-model="nuevoDomicilio.delivery_notes" class="form-control">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Guardar domicilio</button>
          <button type="button" class="btn btn-secondary" @click="cerrarModalDomicilio">Cancelar</button>
        </div>


      </form>
    </div>
  </div>
</div>





</div>

<script>
    window.modoEdicion = <?= isset($modoEdicion) && $modoEdicion ? 'true' : 'false' ?>;
    window.cotizacionEdicion = <?= isset($cotizacionEdicion) ? $cotizacionEdicion : 'null' ?>;
</script>
<?= Asset::js('admin/cotizaciones-vue.js'); ?>

<script>
// Mostrar el botón solo si hay cotizaciones pendientes
localforage.getItem('cotizaciones_offline').then(cotis => {
    if (cotis && cotis.length) {
        document.getElementById('btn-sincronizar-cotizaciones').style.display = '';
    }
});
document.getElementById('btn-sincronizar-cotizaciones').onclick = function() {
    window.sincronizarCotizacionesOffline(function(status, enviadas, errores) {
        let msg = '';
        if (status === 'completado') {
            msg = (enviadas ? enviadas + ' cotizaciones enviadas. ' : '') +
                  (errores ? errores + ' errores.' : '');
            if (window.Swal) Swal.fire('Resultado', msg, 'info');
        }
        document.getElementById('btn-sincronizar-cotizaciones').style.display = 'none';
    });
};
</script>