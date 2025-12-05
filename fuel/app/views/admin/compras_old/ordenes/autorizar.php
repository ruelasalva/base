<script>
window.order_id           = <?= (int) $order_id; ?>;
window.prefillProviderId  = <?= (int)($prefill_provider_id ?? 0); ?>;
window.prefillProviderName = <?= json_encode($prefill_provider_name ?? ''); ?>;
</script>

<!-- HEADER -->
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
                            <li class="breadcrumb-item active" aria-current="page">Autorizar</li>
                        </ol>
                    </nav>
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
                        <h3 class="mb-0">Autorizar Orden de Compra</h3>
                        <p class="mb-0 text-muted small">
                            Revisa, ajusta y autoriza la orden antes de notificar al proveedor.
                        </p>
                    </div>
                    <div class="card-body">

                        <div id="orden-compra-autorizar-app">
                            <div v-if="loading" class="text-center p-5">
                                <i class="fas fa-spinner fa-spin fa-2x"></i><br>Cargando orden y catálogos...
                            </div>

                            <div v-else-if="load_error" class="alert alert-danger">
                                <strong>Error:</strong> No se pudieron cargar los datos de la orden.<br>
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

                                        <!-- PROVEEDOR (buscador) -->
                                        <div class="col-md-6 mb-3 position-relative">
                                            <label>Proveedor</label>
                                            <input type="text"
                                                   v-model="proveedor_nombre"
                                                   class="form-control"
                                                   placeholder="Buscar proveedor por nombre o código"
                                                   @input="filtrarProveedores">
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
                                            <small class="text-muted" v-if="proveedor_id">
                                                ID proveedor: {{ proveedor_id }}
                                            </small>
                                        </div>

                                        <!-- TIPO DE DOCUMENTO -->
                                        <div class="col-md-6 mb-3">
                                            <label>Tipo de Documento</label>
                                            <select v-model="document_type_id" class="form-control">
                                                <option value="">Selecciona</option>
                                                <option v-for="doc in document_type_opts" :value="doc.id">
                                                    {{ doc.name }}
                                                </option>
                                            </select>
                                        </div>

                                        <!-- CÓDIGO DOCUMENTO BASE -->
                                        <div class="col-md-6 mb-3">
                                            <label>Código Documento Base</label>
                                            <div class="input-group">
                                                <input type="text"
                                                       v-model="codigo_oc"
                                                       class="form-control"
                                                       placeholder="Código / referencia física">
                                            </div>
                                        </div>

                                        <!-- FECHA CREACIÓN -->
                                        <div class="col-md-3 mb-3">
                                            <label>Fecha de creación</label>
                                            <input type="date" v-model="fecha" class="form-control">
                                        </div>

                                        <!-- FECHA DE PAGO (VISIBLE EN AUTORIZAR) -->
                                        <div class="col-md-3 mb-3">
                                            <label>Fecha de pago estimada</label>
                                            <input type="date" v-model="fecha_pago" class="form-control">
                                        </div>

                                        <!-- NOTAS -->
                                        <div class="col-md-12 mb-3">
                                            <label>Notas</label>
                                            <textarea v-model="notas"
                                                      class="form-control"
                                                      rows="2"
                                                      placeholder="Notas adicionales para el proveedor o control interno..."></textarea>
                                        </div>
                                    </div>
                                </fieldset>

                                <hr>

                                <!-- =============================== -->
                                <!-- TIPO DE OPERACIÓN + CATÁLOGO PRODUCTO -->
                                <!-- =============================== -->
                                <div class="form-row">
                                    <div class="col-md-3 mb-3">
                                        <label>Tipo de operación</label>
                                        <select v-model="tipo_general" class="form-control" @change="aplicarGeneral('type')">
                                            <option v-for="type in type_opts" :value="type.id">{{ type.label }}</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label>Catálogo de producto</label>
                                        <select v-model="codigo_producto_tipo" class="form-control">
                                            <option value="interno">Código interno</option>
                                            <option value="proveedor">Código proveedor / orden</option>
                                        </select>
                                    </div>
                                </div>

                                <hr>

                                <!-- =============================== -->
                                <!-- DATOS PREDETERMINADOS (COLAPSADOS) -->
                                <!-- =============================== -->
                                <fieldset class="mb-3">
                                    <legend class="mb-0 heading">
                                        <a class="d-block text-decoration-none collapsed"
                                           data-toggle="collapse"
                                           href="#collapseValoresGeneralesAut"
                                           role="button"
                                           aria-expanded="false"
                                           aria-controls="collapseValoresGeneralesAut">
                                            <i class="fas fa-sliders-h mr-1"></i>Datos Predeterminados (Desplegar/ocultar)
                                            <i class="fas fa-chevron-down float-right"></i>
                                        </a>
                                    </legend>

                                    <div class="collapse mt-3" id="collapseValoresGeneralesAut">
                                        <div class="card card-body border-light bg-light">
                                            <div class="form-row">
                                                <!-- Impuesto general -->
                                                <div class="col-md-3 mb-3">
                                                    <label>Impuesto general</label>
                                                    <select v-model="tax_general"
                                                            class="form-control"
                                                            @change="aplicarGeneral('tax')">
                                                        <option v-for="tax in tax_opts" :value="tax.id">{{ tax.label }}</option>
                                                    </select>
                                                </div>

                                                <!-- Retención general -->
                                                <div class="col-md-3 mb-3">
                                                    <label>Retención general</label>
                                                    <select v-model="retention_general"
                                                            class="form-control"
                                                            @change="aplicarGeneral('ret')">
                                                        <option value="">Sin retención</option>
                                                        <option v-for="ret in retention_opts" :value="ret.id">{{ ret.label }}</option>
                                                    </select>
                                                </div>

                                                <!-- Moneda -->
                                                <div class="col-md-3 mb-3">
                                                    <label>Moneda</label>
                                                    <select v-model="moneda" class="form-control">
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

                                    <!-- Configuración de columnas -->
                                    <div class="text-right mb-2">
                                        <button class="btn btn-light btn-sm" type="button" data-toggle="collapse" data-target="#configColumnasAut">
                                            <i class="fas fa-cog"></i> Columnas
                                        </button>
                                    </div>

                                    <div class="collapse" id="configColumnasAut">
                                        <div class="card card-body border-light bg-light">
                                            <div class="form-check form-check-inline" v-for="(col, key) in columnasVisibles" :key="key">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       v-model="col.visible"
                                                       :id="'col-aut-' + key">
                                                <label class="form-check-label" :for="'col-aut-' + key">{{ col.label }}</label>
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-sm">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#</th>
                                                <th v-if="columnasVisibles.tipo.visible">Tipo</th>
                                                <th v-if="columnasVisibles.producto.visible">Producto / Código</th>
                                                <th v-if="columnasVisibles.descripcion.visible">Descripción</th>
                                                <th v-if="columnasVisibles.cantidad.visible">Cantidad</th>
                                                <th v-if="columnasVisibles.precio.visible">Precio</th>
                                                <th v-if="columnasVisibles.cuenta.visible">Cuenta Contable</th>
                                                <th v-if="columnasVisibles.impuesto.visible">Impuesto</th>
                                                <th v-if="columnasVisibles.retencion.visible">Retención</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, idx) in partidas" :key="idx">
                                                <td>{{ idx + 1 }}</td>

                                                <!-- Tipo -->
                                                <td v-if="columnasVisibles.tipo.visible">
                                                    <select v-model="item.tipo" class="form-control">
                                                        <option v-for="type in type_opts" :value="type.id">{{ type.label }}</option>
                                                    </select>
                                                </td>

                                                <!-- Producto / Código -->
                                                <td v-if="columnasVisibles.producto.visible">
                                                    <div v-if="item.tipo === 'articulo'">
                                                        <input list="productos-lista-aut"
                                                               v-model="item.code_product"
                                                               @change="autocompletarDescripcion(idx)"
                                                               class="form-control"
                                                               :placeholder="codigo_producto_tipo === 'interno'
                                                                    ? 'Buscar por código/nombre interno'
                                                                    : 'Buscar por código/nombre proveedor'">
                                                    </div>
                                                    <div v-else>
                                                        <input type="text"
                                                               v-model="item.code_product"
                                                               class="form-control"
                                                               placeholder="Código o clave de servicio">
                                                    </div>
                                                </td>

                                                <!-- Descripción -->
                                                <td v-if="columnasVisibles.descripcion.visible">
                                                    <input type="text"
                                                           v-model="item.description"
                                                           class="form-control"
                                                           :placeholder="item.tipo === 'servicio'
                                                                ? 'Descripción del servicio'
                                                                : 'Descripción del producto'">
                                                </td>

                                                <!-- Cantidad -->
                                                <td v-if="columnasVisibles.cantidad.visible">
                                                    <input type="number"
                                                           v-model.number="item.quantity"
                                                           class="form-control"
                                                           min="0.01"
                                                           placeholder="Cantidad">
                                                </td>

                                                <!-- Precio -->
                                                <td v-if="columnasVisibles.precio.visible">
                                                    <input type="number"
                                                           v-model.number="item.unit_price"
                                                           class="form-control"
                                                           min="0.01"
                                                           placeholder="Precio">
                                                </td>

                                                <!-- Cuenta contable -->
                                                <td v-if="columnasVisibles.cuenta.visible" class="position-relative">
                                                    <input type="text"
                                                           v-model="item.cuenta_busqueda"
                                                           @input="buscarCuentas(idx, item.cuenta_busqueda)"
                                                           class="form-control"
                                                           placeholder="Buscar cuenta (código o nombre)"
                                                           autocomplete="off">
                                                    <div v-if="focusedIndex === idx && sugerencias.length"
                                                         class="list-group position-absolute w-100 zindex-dropdown">
                                                        <a v-for="acc in sugerencias"
                                                           :key="acc.id"
                                                           href="#"
                                                           class="list-group-item list-group-item-action py-1"
                                                           @click.prevent="seleccionarCuentaDirecta(acc, idx)">
                                                            {{ acc.code }} - {{ acc.name }}
                                                        </a>
                                                    </div>
                                                </td>

                                                <!-- Impuesto -->
                                                <td v-if="columnasVisibles.impuesto.visible">
                                                    <select v-model="item.tax_id" class="form-control">
                                                        <option value="">Sin impuesto</option>
                                                        <option v-for="tax in tax_opts" :value="tax.id">{{ tax.label }}</option>
                                                    </select>
                                                </td>

                                                <!-- Retención -->
                                                <td v-if="columnasVisibles.retencion.visible">
                                                    <select v-model="item.retention_id" class="form-control">
                                                        <option value="">Sin retención</option>
                                                        <option v-for="ret in retention_opts" :value="ret.id">{{ ret.label }}</option>
                                                    </select>
                                                </td>

                                                <!-- Eliminar -->
                                                <td>
                                                    <button type="button"
                                                            class="btn btn-danger btn-sm"
                                                            @click="eliminarPartida(idx)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <datalist id="productos-lista-aut">
                                        <option v-for="prod in productos_filtrados"
                                                :value="prod.code + ' - ' + prod.name"></option>
                                    </datalist>

                                    <button type="button"
                                            class="btn btn-outline-secondary btn-sm"
                                            @click="agregarPartida">
                                        Agregar partida
                                    </button>
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
                                <div class="mt-4 d-flex flex-wrap align-items-center">
                                    <!-- Guardar sin autorizar (por si solo corrige) -->
                                    <button type="button"
                                            class="btn btn-secondary mr-2 mb-2"
                                            @click="guardarCambios"
                                            :disabled="loading">
                                        Guardar cambios
                                    </button>

                                    <!-- Guardar y autorizar -->
                                    <button type="button"
                                            class="btn btn-success mr-2 mb-2"
                                            @click="guardarYAutorizar"
                                            :disabled="loading">
                                        Autorizar orden
                                    </button>

                                    <button type="button"
                                            class="btn btn-outline-secondary mb-2"
                                            @click="cancelar"
                                            :disabled="loading">
                                        Cancelar
                                    </button>
                                </div>

                            </div> <!-- /v-else -->
                        </div> <!-- /orden-compra-autorizar-app -->

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= Asset::js('admin/compras/orden-compra-vue.js'); ?>
