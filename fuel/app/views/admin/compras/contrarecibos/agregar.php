<!-- HEADER CON ESTILO UNIFORME -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Contrarecibos</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/compras', 'Compras'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/compras/contrarecibos', 'Contrarecibos'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Agregar
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <!-- Puedes añadir botones adicionales aquí si son relevantes para esta vista -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CONTENIDO DE LA PÁGINA -->
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col">
            <!-- APP VUE -->
            <div id="contrarecibos-app">
                <!-- CARD: SELECCIÓN DE PROVEEDOR Y DÍAS DE CRÉDITO -->
                <div class="card mb-4">
                    <div class="card-header border-0">
                        <h3 class="mb-0">Crear Nuevo Contrarecibo</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-row align-items-end">
                            <div class="col-md-6 mb-3">
                                <label for="proveedor"><strong>Proveedor</strong></label>
                                <select class="form-control form-control-sm" v-model="proveedor_id" @change="onProveedorChange" :disabled="loading">
                                    <option value="">Selecciona proveedor...</option>
                                    <option v-for="prov in proveedores" :value="prov.id" :key="prov.id">
                                        {{ prov.name }} ({{ prov.rfc }})
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="dias_credito"><strong>Días de crédito</strong>
                                    <span class="badge badge-info" v-if="origenCredito=='proveedor'">Proveedor</span>
                                    <span class="badge badge-warning" v-if="origenCredito=='config'">Config global</span>
                                </label>
                                <div class="input-group input-group-sm">
                                    <input class="form-control" type="number" min="0" v-model="dias_credito" :readonly="origenCredito=='proveedor' || !proveedor_id">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" v-if="origenCredito=='config' && proveedor_id" @click="editarDiasCredito">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted" v-if="!dias_credito && proveedor_id">* Configura los días de crédito antes de continuar.</small>
                            </div>
                            <div class="col-md-3 mb-3 text-right">
                                <button class="btn btn-sm btn-outline-info" @click="recargarFacturas" v-if="proveedor_id" :disabled="loading">
                                    <i class="fas fa-sync"></i> Actualizar facturas
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- TABLA: FACTURAS Y OC -->
                <div class="card" v-if="proveedor_id && (facturas.length > 0 || loading)">
                    <div class="card-header border-0">
                        <h3 class="mb-0">Facturas pendientes de pago</h3>
                    </div>
                    <div class="card-body p-0">
                        <div v-if="loading" class="text-center py-5">
                            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                            <p class="mt-2">Cargando facturas...</p>
                        </div>
                        <div v-else-if="facturas.length > 0" class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col"><input type="checkbox" v-model="allSelected"></th>
                                        <th scope="col">UUID</th>
                                        <th scope="col">Monto</th>
                                        <th scope="col">Fecha OC</th>
                                        <th scope="col">Fecha factura</th>
                                        <th scope="col">OC</th>
                                        <th scope="col">
                                            <span data-toggle="tooltip" title="Días de crédito según proveedor o configuración">Días crédito</span>
                                        </th>
                                        <th scope="col">Fecha pago estimada</th>
                                        <th scope="col">Estatus</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                               </thead>
                                <tbody>
                                    <tr v-for="(fac, idx) in facturas" :key="fac.id">
                                        <td>
                                            <input type="checkbox" v-model="fac.seleccionada" :disabled="!fac.puede_pagar">
                                        </td>
                                        <td><span class="badge badge-light">{{ fac.uuid }}</span></td>
                                        <td>{{ fac.total | currency }}</td>
                                        <td>{{ fac.fecha_oc | formatDate }}</td>
                                        <td>{{ fac.fecha_factura | formatDate }}</td>
                                        <td>{{ fac.code_order }}</td>
                                        <td>
                                            <span v-if="fac.dias_credito">{{ fac.dias_credito }}</span>
                                            <span v-else class="text-danger"><i class="fas fa-exclamation-circle"></i> Falta</span>
                                        </td>
                                        <td>
                                            <span v-if="fac.fecha_pago">{{ fac.fecha_pago | formatDate }}</span>
                                            <span v-else class="text-warning"><i class="fas fa-clock"></i> Sin calcular</span>
                                        </td>
                                        <td>
                                            <span :class="{'badge badge-success': fac.puede_pagar, 'badge badge-secondary': !fac.puede_pagar}">
                                                {{ fac.puede_pagar ? 'Completa' : 'Incompleta' }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <button class="btn btn-sm btn-icon-only text-light" @click="verDetalle(fac)" title="Ver detalle">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">Total seleccionado</th>
                                        <th>{{ totalSeleccionado | currency }}</th>
                                        <th colspan="7"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div v-else class="text-center py-4">
                            <p class="text-muted">No hay facturas pendientes de pago para este proveedor.</p>
                        </div>
                    </div>
                    <div class="card-footer py-4" v-if="facturas.length > 0">
                        <button class="btn btn-primary" :disabled="seleccionadas.length == 0 || loading" @click="crearContrarecibo">
                            <span v-if="loading"><i class="fas fa-spinner fa-spin"></i> Procesando...</span>
                            <span v-else><i class="fas fa-check"></i> Generar Contrarecibo</span>
                        </button>
                    </div>
                </div>

                <div v-if="errorMsg" class="alert alert-danger mt-2">
                    <i class="fas fa-exclamation-triangle"></i> {{ errorMsg }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DETALLE FACTURA -->
<!-- ELIMINADO: 'd-block' y 'show' para que Bootstrap los maneje. -->
<div class="modal fade" id="modal-detalle-factura" v-if="modalDetalleVisible" tabindex="-1" role="dialog" aria-labelledby="modal-detalle-factura-label" aria-hidden="true" style="background:rgba(0,0,0,0.25);">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-detalle-factura-label">Detalle de factura {{ facturaDetalle ? facturaDetalle.uuid : '' }}</h5>
                <button type="button" class="close" @click="cerrarModalDetalle" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body" style="max-height:70vh;overflow-y:auto;">
                <!-- Aquí muestra los campos que necesites de facturaDetalle.invoice_data_completa -->
                <div v-if="facturaDetalle && facturaDetalle.invoice_data_completa">
                    <h6 class="mb-3">Datos Generales de la Factura</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Serie:</strong> {{ facturaDetalle.invoice_data_completa.serie || '-' }}</p>
                            <p><strong>Folio:</strong> {{ facturaDetalle.invoice_data_completa.folio || '-' }}</p>
                            <p><strong>Fecha Emisión:</strong> {{ facturaDetalle.invoice_data_completa.fecha || '-' }}</p>
                            <p><strong>Moneda:</strong> {{ facturaDetalle.invoice_data_completa.moneda || '-' }}</p>
                            <p><strong>Total:</strong> {{ facturaDetalle.invoice_data_completa.total | currency }}</p>
                            <p><strong>Subtotal:</strong> {{ facturaDetalle.invoice_data_completa.subtotal | currency }}</p>
                            <p><strong>Tipo Comprobante:</strong> {{ facturaDetalle.invoice_data_completa.tipo_comprobante || '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>RFC Emisor:</strong> {{ facturaDetalle.invoice_data_completa.emisor_rfc || '-' }}</p>
                            <p><strong>Nombre Emisor:</strong> {{ facturaDetalle.invoice_data_completa.emisor_nombre || '-' }}</p>
                            <p><strong>RFC Receptor:</strong> {{ facturaDetalle.invoice_data_completa.receptor_rfc || '-' }}</p>
                            <p><strong>Nombre Receptor:</strong> {{ facturaDetalle.invoice_data_completa.receptor_nombre || '-' }}</p>
                            <p><strong>Uso CFDI:</strong> {{ facturaDetalle.invoice_data_completa.receptor_uso_cfdi || '-' }}</p>
                            <p><strong>Método Pago:</strong> {{ facturaDetalle.invoice_data_completa.metodo_pago || '-' }}</p>
                            <p><strong>Forma Pago:</strong> {{ facturaDetalle.invoice_data_completa.forma_pago || '-' }}</p>
                        </div>
                    </div>

                    <h6 class="mt-4 mb-3">Conceptos</h6>
                    <div class="table-responsive">
                        <!-- Añadido v-if para conceptos -->
                        <table class="table table-sm table-bordered" v-if="facturaDetalle.invoice_data_completa.conceptos && facturaDetalle.invoice_data_completa.conceptos.length > 0">
                            <thead>
                                <tr>
                                    <th>Clave ProdServ</th>
                                    <th>Descripción</th>
                                    <th>Cantidad</th>
                                    <th>Unidad</th>
                                    <th>Valor Unitario</th>
                                    <th>Importe</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(concepto, cIdx) in facturaDetalle.invoice_data_completa.conceptos" :key="cIdx">
                                    <td>{{ concepto.clave_prod_serv || '-' }}</td>
                                    <td>{{ concepto.descripcion || '-' }}</td>
                                    <td>{{ concepto.cantidad || '-' }}</td>
                                    <td>{{ concepto.clave_unidad || '-' }}</td>
                                    <td>{{ concepto.valor_unitario | currency }}</td>
                                    <td>{{ concepto.importe | currency }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <p v-else class="text-muted">No hay conceptos disponibles para esta factura.</p>
                    </div>

                    <h6 class="mt-4 mb-3">Impuestos</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Traslados Globales:</strong>
                            <!-- Añadido v-if para traslados_globales -->
                            <ul class="list-unstyled" v-if="facturaDetalle.invoice_data_completa.traslados_globales && facturaDetalle.invoice_data_completa.traslados_globales.length > 0">
                                <li v-for="(imp, iIdx) in facturaDetalle.invoice_data_completa.traslados_globales" :key="'tg-'+iIdx">
                                    {{ imp.impuesto }} ({{ imp.tasaocuota }}): {{ imp.importe | currency }}
                                </li>
                            </ul>
                            <p v-else>No aplica</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Retenciones Globales:</strong>
                            <!-- Añadido v-if para retenciones_globales -->
                            <ul class="list-unstyled" v-if="facturaDetalle.invoice_data_completa.retenciones_globales && facturaDetalle.invoice_data_completa.retenciones_globales.length > 0">
                                <li v-for="(ret, rIdx) in facturaDetalle.invoice_data_completa.retenciones_globales" :key="'rg-'+rIdx">
                                    {{ ret.impuesto }} ({{ ret.tasaocuota }}): {{ ret.importe | currency }}
                                </li>
                            </ul>
                            <p v-else>No aplica</p>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <p class="text-muted">No hay datos detallados de la factura disponibles.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" @click="cerrarModalDetalle">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPTS VUE Y DEPENDENCIAS -->
<!-- Asegúrate de que SweetAlert2, Axios y jQuery/Bootstrap JS estén cargados globalmente antes de este script -->
<script src="<?= Asset::get_file('admin/contrarecibos-vue.js', 'js'); ?>"></script>
