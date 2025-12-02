<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-5">
					<h6 class="h2 text-white d-inline-block mb-0">Ventas</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/ventas/buscar_cliente', 'Ventas - Agregar venta'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-7 text-right">
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
				<!-- CUSTOM FORM VALIDATION -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Pedido</h3>
                        <button type="button" class="btn btn-danger" id="reiniciar">Reiniciar</button>
                    </div>
					<!-- CARD BODY -->
                    <div class="card-body">
                        <!-- Selección del cliente y productos -->
                        <fieldset>
                            <div class="form-row">
                                <div class="col-md-8 mb-3">
                                    <div class="form-group">
                                        <?php echo Form::label('Cliente', 'customer_id'); ?>
                                        <?php echo Form::select('customer_id', '', $customer_opts, ['id' => 'customer_id', 'class' => 'form-control']); ?>
                                    </div>
                                </div>
                                <div class="col-md-7 mb-3">
                                    <div class="form-group">
                                        <?php echo Form::label('Producto', 'product_id'); ?>
                                        <?php echo Form::select('product_id', '', $product_opts, ['id' => 'product_id', 'class' => 'form-control']); ?>
                                    </div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-control-label">Precio:</label>
                                    <p id="product-price">$0.00</p>
                                </div>
                                <div class="col-md-1 mb-3">
                                    <div class="form-group">
                                        <?php echo Form::label('Cantidad', 'quantity', ['class' => 'form-control-label']); ?>
                                        <?php echo Form::input('quantity', '1', ['id' => 'quantity', 'class' => 'form-control']); ?>
                                    </div>
                                </div>
                                <div class="col-md-2 mb-3 align-self-end">
                                    <div class="form-group">
                                        <button type="button" class="btn btn-primary btn-block" id="agregar-producto">Agregar</button>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
						<!-- TABLE -->
                        <!-- Tabla de productos -->
                        <div class="row">
                            <div class="col">
                                <div class="card">
                                    <div class="card-header border-0">
                                        <h3 class="mb-0">Lista de productos</h3>
                                    </div>
									<!-- LIGHT TABLE -->
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>Cantidad</th>
                                                <th>Precio Unitario</th>
                                                <th>Total</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="product-list"></tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" align="right">Total Piezas Productos:</td>
                                                <td class="text-right" id="total-piezas">0</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" align="right">Subtotal:</td>
                                                <td class="text-right" id="subtotal">$0.00</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" align="right">IVA (16%):</td>
                                                <td class="text-right" id="iva">$0.00</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" align="right">Total:</td>
                                                <td class="text-right" id="total">$0.00</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <!-- Sección: Domicilio de entrega -->
                            <div class="col-md-4">
                                <h5 class="mb-3">Domicilio de Entrega</h5>
                                <div class="form-group">
                                    <?php echo Form::label('Domicilio seleccionado', 'selected_address'); ?>
                                    <?php echo Form::textarea('selected_address', '', [
                                        'id' => 'selected_address',
                                        'class' => 'form-control',
                                        'rows' => '3',
                                        'readonly' => true
                                    ]); ?>
                                </div>
                                <div class="form-group">
                                    <?php echo Form::label('Cambiar domicilio', 'address_id'); ?>
                                    <?php echo Form::select('address_id', '', ['0' => 'Selecciona un domicilio'], [
                                        'id' => 'address_id',
                                        'class' => 'form-control',
                                        'style' => 'width: 100%;'
                                    ]); ?>
                                </div>
                            </div>
                            <!-- Sección: Facturación -->
                            <div class="col-md-4">
                                <h5 class="mb-3">Facturación</h5>
                                <div>
                                    <label for="">¿Requiere factura?</label>
                                    <label class="custom-toggle">
                                        <input type="checkbox" class="toggle-fsi">
                                        <span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Sí"></span>
                                    </label>
                                </div>
                                <div class="form-group mt-3" id="invoice-container" style="display: none;">
                                    <label for="invoice-select">Seleccionar Datos de Facturación</label>
                                    <select id="invoice-select" class="form-control">
                                        <option value="0">Selecciona una opción</option>
                                    </select>
                                    <textarea id="invoice-data" class="form-control mt-2" rows="3" readonly></textarea>
                                </div>
                            </div>
                            <!-- Sección: Método de pago -->
                            <div class="col-md-4">
                                <h5 class="mb-3">Método de Pago</h5>
                                <div class="form-group">
                                    <?php echo Form::label('Información del pago', 'payment_id'); ?>
                                    <?php echo Form::select('payment_id', Input::post('payment_id', isset($payment_id) ? $payment_id : 'none'), $payment_opts, [
                                        'id' => 'payment_id',
                                        'class' => 'form-control'
                                    ]); ?>
                                </div>
                            </div>
                        </div>
                        <!-- Botón Finalizar Pedido alineado al centro -->
                        <div class="row mt-4">
                            <div class="col text-center">
                                <?php echo Form::button('finalizar_pedido', 'Finalizar pedido', [
                                    'id' => 'finalizar-pedido',
                                    'class' => 'btn btn-primary',
                                    'type' => 'button'
                                ]); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var product_opts = <?php echo json_encode($product_opts); ?>;
</script>
