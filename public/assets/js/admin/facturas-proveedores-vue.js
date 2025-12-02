window.FacturasProveedoresImport = Vue.extend({
    props: ['resumen'],
    data() {
        return {
            urlBase: document.getElementById('url-location').dataset.url,
            facturas: [],
            proveedoresNuevos: [],
            filtroStatus: 'todos',
            showModalProveedor: false,
            proveedorEdit: null,
            showModalFactura: false,
            facturaDetalle: null,
            selectAll: true,
            ocManualMap: {},
            ocNumeroMap: {},
            scrollPos: 0,
            loadingGuardar: false,
            resultadoGuardado: null,
            // urlGuardar: 'assests/proveedores/facturas', // Ya no es necesario, se usa urlBase + 'guardar_facturas_masivo'
        }
    },
    watch: {
        resumen: {
            handler(val) {
                if (val) {
                    this.facturas = (val.facturas || []).map(f => ({
                        ...f,
                        seleccionado: true,
                        generar_orden: false,
                        // Asegúrate de que estas propiedades existan o se inicialicen
                        traslados_globales: f.traslados_globales || [],
                        retenciones_globales: f.retenciones_globales || [],
                        productos: (f.productos || []).map(p => ({
                            ...p,
                            traslados: p.traslados || [],
                            retenciones: p.retenciones || []
                        }))
                    }));
                    this.proveedoresNuevos = val.proveedores_nuevos || [];
                    this.selectAll = true;
                    this.ocManualMap = {};
                    this.ocNumeroMap = {};
                }
            },
            immediate: true,
            deep: true
        }
    },
    computed: {
        facturasFiltradas() {
            if (this.filtroStatus === 'todos') return this.facturas;
            if (this.filtroStatus === 'nuevos') return this.facturas.filter(f => !f.proveedor_existe);
            if (this.filtroStatus === 'duplicados') return this.facturas.filter(f => f.factura_duplicada);
            if (this.filtroStatus === 'error') return this.facturas.filter(f => f.errores && f.errores.length > 0);
            if (this.filtroStatus === 'sat_rechazado') return this.facturas.filter(f => f.valida_sat === 'NO_VIGENTE');
            return this.facturas;
        },
        haySeleccionadas() {
            return this.facturas.some(f => f.seleccionado);
        },
        proveedoresNuevosVisibles() {
            const rfcsSeleccionados = this.facturas
                .filter(f => f.seleccionado && !f.proveedor_existe)
                .map(f => f.emisor_rfc);
            return this.proveedoresNuevos.filter(p => rfcsSeleccionados.includes(p.rfc));
        }
    },
    methods: {
        verDetalleFactura(factura) {
            this.scrollPos = window.scrollY || window.pageYOffset;
            this.facturaDetalle = factura;
            this.showModalFactura = true;
        },
        cerrarModalFactura() {
            this.showModalFactura = false;
            this.facturaDetalle = null;
            setTimeout(() => window.scrollTo(0, this.scrollPos), 50);
        },
        editarProveedor(proveedor) {
            this.proveedorEdit = Object.assign({}, proveedor);
            this.showModalProveedor = true;
        },
        cerrarModalProveedor() {
            this.showModalProveedor = false;
            this.proveedorEdit = null;
        },
        toggleSelectAll() {
            let nuevo = this.selectAll;
            this.facturas.forEach(f => { f.seleccionado = nuevo; });
        },
        actualizarSelectAll() {
            this.selectAll = this.facturas.every(f => f.seleccionado);
        },
        eliminarFactura(idx) {
            this.facturas.splice(idx, 1);
            this.actualizarSelectAll();
        },
        facturasSeleccionadas() {
            return this.facturas.filter(f => f.seleccionado);
        },
        toggleOCManual(factura) {
            factura.generar_orden = !factura.generar_orden;
            if (!factura.generar_orden) {
                this.ocNumeroMap[factura.uuid] = '';
            }
        },
        setOCManualNumero(uuid, valor) {
            this.ocNumeroMap = { ...this.ocNumeroMap, [uuid]: valor };
        },
        reiniciarCarga() {
            location.reload();
        },
        // Este es el método para guardar un proveedor individual desde el modal
        guardarProveedor() {
            if (!this.proveedorEdit.faltantes.email) {
                // CAMBIO: swal antiguo a Swal.fire moderno
                Swal.fire({
                    title: 'Error',
                    text: 'Debes capturar el email.',
                    icon: 'error'
                });
                return;
            }
            let self = this;
            let params = new URLSearchParams();
            params.append('rfc', this.proveedorEdit.rfc);
            params.append('nombre', this.proveedorEdit.nombre);
            params.append('email', this.proveedorEdit.faltantes.email);

            axios.post(this.urlBase + 'ajax_crear_proveedor', params)
                .then(function (response) {
                    if (response.data.success) {
                        // CAMBIO: swal antiguo a Swal.fire moderno
                        Swal.fire({
                            title: 'Proveedor creado',
                            text: 'El proveedor fue creado correctamente.',
                            icon: 'success'
                        });
                        self.facturas.forEach(f => {
                            if (f.emisor_rfc === self.proveedorEdit.rfc) f.proveedor_existe = true;
                        });
                        self.showModalProveedor = false;
                    } else {
                        // CAMBIO: swal antiguo a Swal.fire moderno
                        Swal.fire({
                            title: 'Error',
                            text: response.data.mensaje || 'No se pudo crear el proveedor.',
                            icon: 'error'
                        });
                    }
                })
                .catch(function () {
                    // CAMBIO: swal antiguo a Swal.fire moderno
                    Swal.fire({
                        title: 'Error',
                        text: 'Error en el servidor al crear proveedor.',
                        icon: 'error'
                    });
                });
        },
        // Este es el método principal para guardar las facturas seleccionadas
        guardarFacturas() {
            const facturasAguardar = this.facturasSeleccionadas().map(f => ({
                ...f, // Esto copia TODAS las propiedades de 'f', incluyendo 'ruta_archivo_temp'
                oc_manual: f.generar_orden,
                order_id: f.generar_orden ? (this.ocNumeroMap[f.uuid] || '') : '', // Usar 'order_id' para que el backend lo reconozca
                // No es necesario 'oc_numero' si 'order_id' es lo que el backend usa
            }));

            this.loadingGuardar = true;
            this.resultadoGuardado = null;

            fetch(this.urlBase + 'guardar_facturas_masivo', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ facturas: facturasAguardar, proveedores: this.proveedoresNuevosVisibles })
            })
                .then(resp => {
                    if (!resp.ok) {
                        return resp.text().then(text => {
                            if (text.trim().startsWith('<')) {
                                throw new Error("El servidor respondió con un error inesperado (posiblemente un error de PHP). Revisa los logs del servidor.");
                            } else {
                                throw new Error(`HTTP Error: ${resp.status} - ${text || 'Mensaje de error no disponible.'}`);
                            }
                        });
                    }
                    return resp.json().catch(e => {
                        if (e instanceof SyntaxError) {
                            throw new Error("Error al procesar la respuesta del servidor. El formato de datos es incorrecto. Contacte a soporte técnico.");
                        }
                        throw e;
                    });
                })
                .then(data => {
                    let msgHTML = data.mensaje || '';
                    const errores = data.detalles || [];

                    if (errores.length > 0) {
                        const erroresPorTipo = errores.reduce((acc, error) => {
                            const tipo = error.tipo_error || 'desconocido';
                            if (!acc[tipo]) {
                                acc[tipo] = [];
                            }
                            acc[tipo].push(error);
                            return acc;
                        }, {});

                        msgHTML += '<br><hr><b>Detalles del guardado:</b><br>';

                        // Errores de duplicidad
                        if (erroresPorTipo.duplicada) {
                            msgHTML += '<b><span style="color: #F8BB86;">Duplicadas:</span></b><br>';
                            erroresPorTipo.duplicada.forEach(err => {
                                msgHTML += `- ${err.uuid}: ${err.mensaje}<br>`;
                            });
                        }

                        // Errores por falta de proveedor
                        if (erroresPorTipo.sin_proveedor) {
                            msgHTML += '<b><span style="color: #F8BB86;">Sin proveedor:</span></b><br>';
                            erroresPorTipo.sin_proveedor.forEach(err => {
                                msgHTML += `- ${err.uuid}: ${err.mensaje}<br>`;
                            });
                        }

                        // Otros errores
                        const otrosErrores = Object.keys(erroresPorTipo).filter(t => !['duplicada', 'sin_proveedor'].includes(t));
                        if (otrosErrores.length > 0) {
                            msgHTML += '<b><span style="color: #F8BB86;">Otros errores:</span></b><br>';
                            otrosErrores.forEach(tipo => {
                                erroresPorTipo[tipo].forEach(err => {
                                    msgHTML += `- ${err.uuid}: ${err.mensaje}<br>`;
                                });
                            });
                        }
                    }

                    const titulo = (errores.length > 0) ? 'Algunas facturas no se guardaron' : 'Guardado exitoso';
                    const tipoAlerta = (errores.length > 0) ? 'warning' : 'success';

                    // Ya estaba actualizado, pero se mantiene para la completitud
                    Swal.fire({
                        title: titulo,
                        html: msgHTML || 'Facturas guardadas correctamente',
                        icon: tipoAlerta
                    });

                    // Opcional: Actualizar la lista en el frontend
                    if (data.success) {
                        // Filtrar las que se guardaron y no mostrar las que tuvieron errores
                        // Si data.detalles incluye las facturas exitosas, esto funcionará para remover las exitosas
                        // Si solo incluye errores, entonces se mantendrán las que no están en 'detalles' (las exitosas)
                        const uuidsConError = new Set(errores.map(err => err.uuid));
                        this.facturas = this.facturas.filter(f => uuidsConError.has(f.uuid));
                        this.actualizarSelectAll();
                    }
                })
                .catch(err => {
                    // Ya estaba actualizado, pero se mantiene para la completitud
                    Swal.fire({
                        title: 'Error',
                        text: 'Error al guardar: ' + err.message,
                        icon: 'error'
                    });
                })
                .finally(() => {
                    this.loadingGuardar = false;
                });
        },
    },
    filters: {
        currency(value) {
            if (!value && value !== 0) return '$0.00';
            return '$' + parseFloat(value).toLocaleString('es-MX', { minimumFractionDigits: 2 });
        }
    },
    template: `
    <div>
        <h4 class="mb-3">Resumen de archivos procesados</h4>
        <!-- FILTROS -->
        <div class="mb-2">
            <button class="btn btn-sm btn-outline-primary mr-1" @click="filtroStatus='todos'">Todos</button>
            <button class="btn btn-sm btn-outline-info mr-1" @click="filtroStatus='nuevos'">Nuevos proveedores</button>
            <button class="btn btn-sm btn-outline-danger mr-1" @click="filtroStatus='duplicados'">Duplicadas</button>
            <button class="btn btn-sm btn-outline-warning mr-1" @click="filtroStatus='error'">Con errores</button>
            <button class="btn btn-sm btn-outline-dark" @click="filtroStatus='sat_rechazado'">SAT no vigente</button>
        </div>
        <!-- SELECT ALL Y TABLA DE FACTURAS -->
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th><input type="checkbox" v-model="selectAll" @change="toggleSelectAll"></th>
                        <th>Archivo</th>
                        <th>UUID</th>
                        <th>RFC Emisor</th>
                        <th>Proveedor</th>
                        <th>Duplicada</th>
                        <th>Validez SAT</th>
                        <th>
                            <input type="checkbox"
                                         :checked="facturas.every(f=>f.generar_orden)"
                                         @change="facturas.forEach(f=>f.generar_orden=$event.target.checked)">
                            OC manual
                        </th>
                        <th>Errores</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(factura, idx) in facturasFiltradas" :key="factura.uuid"> <!-- Cambiado a key="factura.uuid" para unicidad -->
                        <td>
                            <input type="checkbox" v-model="factura.seleccionado" @change="actualizarSelectAll">
                        </td>
                        <td>{{ factura.archivo }}</td>
                        <td>{{ factura.uuid }}</td>
                        <td>{{ factura.emisor_rfc }}</td>
                        <td>
                            <span v-if="factura.proveedor_existe" class="badge badge-success">Ya existe</span>
                            <span v-else class="badge badge-warning">
                                {{ (proveedoresNuevos.find(p=>p.rfc===factura.emisor_rfc) || {}).nombre || 'Nuevo' }}
                            </span>
                        </td>
                        <td>
                            <span v-if="factura.factura_duplicada" class="badge badge-danger">Sí</span>
                            <span v-else class="badge badge-success">No</span>
                        </td>
                        <td>
                            <span :class="{
                                'badge badge-success': factura.valida_sat === 'VIGENTE',
                                'badge badge-warning': factura.valida_sat === 'NO_DETERMINADO' || factura.valida_sat === 'CAPTCHA',
                                'badge badge-danger': factura.valida_sat === 'NO_VIGENTE'
                            }">
                                {{ factura.valida_sat || 'Sin validar' }}
                            </span>
                        </td>
                        <td>
                            <input type="checkbox" v-model="factura.generar_orden" @change="toggleOCManual(factura)">
                            <span v-if="factura.generar_orden" class="ml-1 text-primary">Manual</span>
                            <span v-else class="ml-1 text-muted small">Automática</span>
                        </td>
                        <td>
                            <ul class="mb-0" v-if="factura.errores && factura.errores.length">
                                <li v-for="err in factura.errores">{{ err }}</li>
                            </ul>
                            <span v-else class="badge badge-success">OK</span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info" @click="verDetalleFactura(factura)">Detalle</button>
                            <button class="btn btn-sm btn-danger" @click="eliminarFactura(idx)" title="Eliminar factura"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-if="!haySeleccionadas" class="alert alert-warning mt-2">
            Debes seleccionar al menos una factura para guardar.
        </div>
        <!-- NUEVOS PROVEEDORES SÓLO SI HAY FACTURAS SELECCIONADAS -->
        <div v-if="proveedoresNuevosVisibles.length">
            <h5 class="mt-4">Nuevos proveedores detectados</h5>
            <ul class="list-unstyled">
                <li v-for="prov in proveedoresNuevosVisibles" :key="prov.rfc" class="mb-2 d-flex align-items-center">
                    <span class="font-weight-bold mr-2">{{ prov.rfc }}</span> 
                    <span class="mr-2">- {{ prov.nombre }}</span>
                    <button class="btn btn-sm btn-warning ml-auto" @click="editarProveedor(prov)">Capturar datos</button>
                </li>
            </ul>
        </div>
        <!-- BOTONES -->
        <div class="mt-4">
            <button class="btn btn-primary" :disabled="!haySeleccionadas || loadingGuardar" @click="guardarFacturas">
                <span v-if="loadingGuardar"><i class="fa fa-spinner fa-spin"></i> Guardando...</span>
                <span v-else><i class="fas fa-save"></i> Guardar seleccionadas</span>
            </button>
            <button class="btn btn-secondary ml-2" @click="reiniciarCarga">
                <i class="fas fa-sync"></i> Reiniciar / Subir nuevos XMLs
            </button>
        </div>
        <div v-if="resultadoGuardado" class="mt-3">
            <pre class="p-2 bg-light">{{ resultadoGuardado }}</pre>
        </div>
        <!-- MODAL: DETALLE DE FACTURA -->
        <div class="modal fade show d-block" v-if="showModalFactura" tabindex="-1" role="dialog" style="background:rgba(0,0,0,0.25);overflow:auto;">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detalle de factura {{ facturaDetalle.uuid }}</h5>
                        <button type="button" class="close" @click="cerrarModalFactura"><span>&times;</span></button>
                    </div>
                    <div class="modal-body" style="max-height:70vh;overflow-y:auto;">
    <!-- ENCABEZADO Y DATOS GENERALES -->
<div class="row mb-2">
    <div class="col-md-6">
        <strong>Serie:</strong> {{ facturaDetalle.serie || '-' }}<br>
        <strong>Folio:</strong> {{ facturaDetalle.folio || '-' }}<br>
        <strong>Fecha:</strong> {{ facturaDetalle.fecha || '-' }}<br>
        <strong>Moneda:</strong> {{ facturaDetalle.moneda || '-' }}<br>
        <strong>Total:</strong> {{ facturaDetalle.total | currency }}<br>
        <strong>Subtotal:</strong> {{ facturaDetalle.subtotal | currency }}<br>
        <strong>Total impuestos:</strong> {{ facturaDetalle.total_impuestos_trasladados | currency }}<br>
        <strong>Total retenciones:</strong> {{ facturaDetalle.total_impuestos_retenidos | currency }}<br>
        <strong>Condiciones de pago:</strong> {{ facturaDetalle.condiciones_pago || '-' }}<br>
        <strong>Tipo de comprobante:</strong> {{ facturaDetalle.tipo_comprobante || '-' }}<br>
    </div>
    <div class="col-md-6">
        <strong>Emisor:</strong> {{ facturaDetalle.emisor_nombre }} ({{ facturaDetalle.emisor_rfc }})<br>
        <strong>Régimen Fiscal Emisor:</strong> {{ facturaDetalle.emisor_regimen_fiscal || '-' }}<br>
        <strong>Receptor:</strong> {{ facturaDetalle.receptor_nombre }} ({{ facturaDetalle.receptor_rfc }})<br>
        <strong>Régimen Fiscal Receptor:</strong> {{ facturaDetalle.receptor_regimen_fiscal || '-' }}<br>
        <strong>Uso CFDI Receptor:</strong> {{ facturaDetalle.receptor_uso_cfdi || '-' }}<br>
        <strong>Método de pago:</strong> {{ facturaDetalle.metodo_pago || '-' }}<br>
        <strong>Forma de pago:</strong> {{ facturaDetalle.forma_pago || '-' }}<br>
        <strong>Lugar de expedición:</strong> {{ facturaDetalle.lugar_expedicion || '-' }}<br>
        <strong>No. certificado:</strong> {{ facturaDetalle.no_certificado || '-' }}<br>
    </div>
</div>

    <!-- TABLA DE PRODUCTOS/CONCEPTOS -->
    <div v-if="facturaDetalle.productos && facturaDetalle.productos.length">
        <h6 class="mt-3">Productos / Conceptos</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Clave</th>
                        <th>No. Identificación</th>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Unidad</th>
                        <th>Precio unitario</th>
                        <th>Importe</th>
                        <th>Impuestos</th>
                        <th>Retenciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="prod in facturaDetalle.productos">
                        <td>{{ prod.clave_prod_serv || '-' }}</td>
                        <td>{{ prod.noidentificacion || '-' }}</td>
                        <td>{{ prod.descripcion || '-' }}</td>
                        <td>{{ prod.cantidad || '-' }}</td>
                        <td>{{ prod.clave_unidad || '-' }}</td>
                        <td>{{ prod.valor_unitario | currency }}</td>
                        <td>{{ prod.importe | currency }}</td>
                        <td>
                            <ul v-if="prod.traslados && prod.traslados.length" class="mb-0 pl-3">
                                <li v-for="t in prod.traslados">
                                    {{ t.impuesto }} {{ t.tasaocuota }}: {{ t.importe | currency }}
                                </li>
                            </ul>
                            <span v-else>-</span>
                        </td>
                        <td>
                            <ul v-if="prod.retenciones && prod.retenciones.length" class="mb-0 pl-3">
                                <li v-for="r in prod.retenciones">
                                    {{ r.impuesto }} {{ r.tasaocuota }}: {{ r.importe | currency }}
                                </li>
                            </ul>
                            <span v-else>-</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- IMPUESTOS GLOBALES -->
    <div v-if="(facturaDetalle.traslados_globales && facturaDetalle.traslados_globales.length) ||
                (facturaDetalle.retenciones_globales && facturaDetalle.retenciones_globales.length)">
        <div v-if="facturaDetalle.traslados_globales && facturaDetalle.traslados_globales.length">
            <h6 class="mt-3">Impuestos trasladados (globales)</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Impuesto</th>
                            <th>Tasa</th>
                            <th>Importe</th>
                            <th>Base</th>
                            <th>Tipo factor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="imp in facturaDetalle.traslados_globales">
                            <td>{{ imp.impuesto }}</td>
                            <td>{{ imp.tasaocuota }}</td>
                            <td>{{ imp.importe | currency }}</td>
                            <td>{{ imp.base | currency }}</td>
                            <td>{{ imp.tipo_factor }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div v-if="facturaDetalle.retenciones_globales && facturaDetalle.retenciones_globales.length">
            <h6 class="mt-3">Retenciones (globales)</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Impuesto</th>
                            <th>Tasa</th>
                            <th>Importe</th>
                            <th>Base</th>
                            <th>Tipo factor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="ret in facturaDetalle.retenciones_globales">
                            <td>{{ ret.impuesto }}</td>
                            <td>{{ ret.tasaocuota }}</td>
                            <td>{{ ret.importe | currency }}</td>
                            <td>{{ ret.base | currency }}</td>
                            <td>{{ ret.tipo_factor }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- JSON CRUDO (debug) -->
    <pre class="mt-3 p-2 bg-light" style="max-height: 200px; overflow: auto;">{{ facturaDetalle }}</pre>
</div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="cerrarModalFactura">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL: CAPTURA DE NUEVO PROVEEDOR -->
        <div v-if="showModalProveedor">
            <div class="modal-backdrop fade show"></div>
            <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background:rgba(0,0,0,0.25);">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Completar datos de proveedor</h5>
                            <button type="button" class="close" @click="cerrarModalProveedor"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>RFC (usuario)</label>
                                <input class="form-control" v-model="proveedorEdit.rfc" readonly>
                            </div>
                            <div class="form-group">
                                <label>Razón Social</label>
                                <input class="form-control" v-model="proveedorEdit.nombre" readonly>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input class="form-control" v-model="proveedorEdit.faltantes.email" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" @click="guardarProveedor">Guardar</button>
                            <button class="btn btn-secondary" @click="cerrarModalProveedor">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `
});
