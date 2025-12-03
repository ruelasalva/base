<!-- HEADER -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Órdenes de Compra</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/compras/ordenes', 'Órdenes de Compra'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/compras/ordenes/info/'.$order->id, 'Detalle OC #'.$order->code_order); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Editar</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/compras/ordenes/info/'.$order->id, 'Ver', ['class' => 'btn btn-sm btn-neutral']); ?>
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
                        <h3 class="mb-0">
                            Editando Orden de Compra 
                            <span class="badge badge-primary">#<?= $order->code_order; ?></span>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="orden-compra-editar-app">
                            <div v-if="loading" class="text-center p-5">
                                <i class="fas fa-spinner fa-spin fa-2x"></i><br>Cargando datos...
                            </div>
                            <div v-else-if="load_error" class="alert alert-danger">
                                <strong>Error:</strong> No se pudieron cargar los datos.<br>
                                Intenta recargar la página o contacta al administrador.
                            </div>
                            <div v-else>
                                <!-- INFORMACIÓN GENERAL -->
                                <fieldset>
                                    <div class="form-row">
                                        <div class="col-md-12 mt-0 mb-3">
                                            <legend class="mb-0 heading">Información general</legend>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Proveedor</label>
                                            <!-- En edición, solo mostramos, no editable -->
                                            <div class="form-control bg-light">
                                                <strong>{{ proveedor_nombre }}</strong>
                                                <span class="d-block small text-muted" v-if="proveedor_rfc">{{ proveedor_rfc }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Código OC</label>
                                            <input type="text" v-model="codigo_oc" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Fecha de compra</label>
                                            <input type="date" v-model="fecha" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Moneda</label>
                                            <select v-model="moneda" class="form-control" required>
                                                <option v-for="mon in currency_opts" :value="mon.id">{{ mon.label }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label>Notas</label>
                                            <textarea v-model="notas" class="form-control" rows="2" placeholder="Notas adicionales..."></textarea>
                                        </div>
                                    </div>
                                </fieldset>
                                <hr>

                                <!-- IMPUESTOS Y RETENCIONES GENERALES -->
                                <fieldset>
                                    <legend class="mb-0 heading">Valores generales</legend>
                                    <div class="form-row">
                                        <div class="col-md-3 mb-3">
                                            <label>Impuesto general</label>
                                            <select v-model="tax_general" @change="aplicarGeneral('tax')" class="form-control">
                                                <option v-for="tax in tax_opts" :value="tax.id">{{ tax.label }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label>Retención general</label>
                                            <select v-model="retention_general" @change="aplicarGeneral('ret')" class="form-control">
                                                <option value="">Sin retención</option>
                                                <option v-for="ret in retention_opts" :value="ret.id">{{ ret.label }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label>Tipo general</label>
                                            <select v-model="tipo_general" @change="aplicarGeneral('type')" class="form-control">
                                                <option v-for="type in type_opts" :value="type.id">{{ type.label }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </fieldset>
                                <hr>

                                <!-- PARTIDAS -->
                                <fieldset>
                                    <legend class="mb-0 heading">Artículos / Servicios</legend>
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Producto / Código</th>
                                                <th>Descripción</th>
                                                <th>Cantidad</th>
                                                <th>Precio</th>
                                                <th>Impuesto</th>
                                                <th>Retención</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, idx) in partidas" :key="idx">
                                                <td>
                                                    <select v-model="item.tipo" class="form-control">
                                                        <option v-for="type in type_opts" :value="type.id">{{ type.label }}</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <div v-if="item.tipo === 'articulo'">
                                                        <input list="productos-lista" v-model="item.code_product" @change="autocompletarDescripcion(idx)" class="form-control">
                                                        <datalist id="productos-lista">
                                                            <option v-for="prod in all_products" :value="prod.code + ' - ' + prod.name"></option>
                                                        </datalist>
                                                    </div>
                                                    <div v-else>
                                                        <input type="text" v-model="item.code_product" class="form-control">
                                                    </div>
                                                </td>
                                                <td><input type="text" v-model="item.description" class="form-control" required></td>
                                                <td><input type="number" v-model.number="item.quantity" class="form-control" min="0.01" required></td>
                                                <td><input type="number" v-model.number="item.unit_price" class="form-control" min="0.01" required></td>
                                                <td>
                                                    <select v-model="item.tax_id" class="form-control">
                                                        <option value="">Sin impuesto</option>
                                                        <option v-for="tax in tax_opts" :value="tax.id">{{ tax.label }}</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select v-model="item.retention_id" class="form-control">
                                                        <option value="">Sin retención</option>
                                                        <option v-for="ret in retention_opts" :value="ret.id">{{ ret.label }}</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm" @click="eliminarPartida(idx)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" @click="agregarPartida">
                                        Agregar partida
                                    </button>
                                </fieldset>
                                <hr>

                                <!-- TOTALES -->
                                <div class="row mb-3">
                                    <div class="col-md-3 offset-md-5 text-right">
                                        <strong>Subtotal:</strong> <span class="badge badge-secondary">{{ subtotal | currency }}</span>
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <strong>Impuesto:</strong> <span class="badge badge-warning">{{ impuestoTotal | currency }}</span>
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <strong>Retención:</strong> <span class="badge badge-info">{{ retencionTotal | currency }}</span>
                                    </div>
                                    <div class="col-md-12 text-right mt-2">
                                        <strong>Total:</strong> <span class="badge badge-success">{{ total | currency }}</span>
                                    </div>
                                </div>
                                <hr>

                                <!-- BOTONES -->
                                <div class="btn-group mt-4">
                                    <button type="button" class="btn btn-primary" @click="guardarYVer" :disabled="loading">
                                        Guardar cambios
                                    </button>
                                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" :disabled="loading">
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#" @click.prevent="guardarYVer">Guardar y ver</a>
                                        <a class="dropdown-item" href="#" @click.prevent="guardarYCerrar">Guardar y cerrar</a>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-secondary ml-2" @click="cancelar" :disabled="loading">Cancelar</button>
                            </div>
                        </div>

                        <!-- Pasar datos PHP → JS -->
                        <script>
                            window.order_id = <?= (int)$order->id; ?>;
                            window.proveedor_id = <?= (int)$order->provider_id; ?>;
                            window.proveedor_nombre = <?= json_encode($order->provider->company_name); ?>;
                            window.proveedor_rfc = <?= json_encode($order->provider->rfc); ?>;
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= Asset::js('admin/compras/orden-compra-vue.js'); ?>
