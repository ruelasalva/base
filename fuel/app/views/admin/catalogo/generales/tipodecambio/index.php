<!-- CONTENT -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Catálogo</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <?php echo Html::anchor('admin/catalogo/generales/tipodecambio', 'Tipos de Cambio'); ?>
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/catalogo/generales/tipodecambio/agregar', 'Agregar', array('class' => 'btn btn-sm btn-neutral')); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col">
            <div class="card">
                
                <!-- CARD HEADER -->
				<div class="card-header border-0">
					<?php echo Form::open(array('action' => 'admin/catalogo/generales/tipodecambio', 'method' => 'get', 'class' => 'form-inline w-100')); ?>
					<div class="form-row align-items-end w-100">
						<!-- FILTRO FECHA -->
						<div class="col-md-4 mb-2">
							<?php echo Form::label('Buscar por fecha', 'search', array('class' => 'form-control-label d-block')); ?>
							<?php echo Form::input('search', (isset($search) ? $search : ''), array(
								'id' => 'search',
								'class' => 'form-control',
								'placeholder' => 'Fecha (YYYY-MM-DD)',
								'type' => 'date'
							)); ?>
						</div>
						<!-- FILTRO MONEDA -->
						<div class="col-md-4 mb-2">
							<?php echo Form::label('Moneda', 'currency_id', array('class' => 'form-control-label d-block')); ?>
							<select name="currency_id" id="currency_id" class="form-control">
								<option value="0">Todas</option>
								<?php foreach($currencies as $currency): ?>
									<option value="<?php echo $currency->id; ?>" <?php echo (isset($selected_currency) && $selected_currency == $currency->id) ? 'selected' : ''; ?>>
										<?php echo $currency->name; ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
						<!-- BOTÓN ÚNICO -->
						<div class="col-md-4 mb-2 d-flex align-items-end">
							<?php echo Form::submit(array(
								'value'=> 'Buscar',
								'name'=>'submit',
								'class' => 'btn btn-primary btn-block'
							)); ?>
						</div>
					</div>
					<?php echo Form::close(); ?>
				</div>

                <!-- TABLE -->
                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Fecha</th>
                                <th scope="col">Moneda</th>
                                <th scope="col">Tipo de cambio</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($exchanges)): ?>
                                <?php foreach($exchanges as $exchange): ?>
                                    <tr>
                                        <td><?php echo $exchange['date']; ?></td>
                                        <td><?php echo $exchange['currency']; ?></td>
                                        <td><?php echo number_format($exchange['rate'], 6); ?></td>
                                        <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <?php echo Html::anchor('admin/catalogo/generales/tipodecambio/info/'.$exchange['id'], 'Ver', array('class' => 'dropdown-item')); ?>
                                                    <?php echo Html::anchor('admin/catalogo/generales/tipodecambio/editar/'.$exchange['id'], 'Editar', array('class' => 'dropdown-item')); ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">No existen registros</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if($pagination != ''): ?>
                    <div class="card-footer py-4">
                        <?php echo $pagination; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
