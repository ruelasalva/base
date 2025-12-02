<!-- ENCABEZADO -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0"><i class="fas fa-user-tie"></i> Proveedor</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item"><?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?></li>
							<li class="breadcrumb-item"><?php echo Html::anchor('admin/proveedores', 'Proveedores'); ?></li>
							<li class="breadcrumb-item active"><?php echo $code_sap; ?></li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
          <?php echo Html::anchor('admin/compras/ordenes/agregar?provider_id='.$id,'<i class="fas fa-file-invoice"></i> Crear orden de compra',['class' => 'btn btn-sm btn-success']); ?>
					<?php echo Html::anchor('admin/proveedores/csv/exportar_info/'.$provider_id, 'Exportar CSV', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php echo Html::anchor('admin/proveedores/recuperar_contrasena_proveedores/'.$id, '<i class="fas fa-id-card"></i> Recuperacion de contraseña', ['class' => 'btn btn-sm btn-neutral']); ?>
					<?php echo Html::anchor('admin/proveedores/editar/'.$id, '<i class="fas fa-edit"></i> Editar', ['class' => 'btn btn-sm btn-neutral']); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- CONTENIDO -->
<div class="container-fluid mt--6">
  <div class="row">
    <div class="col">
      <div class="card-wrapper">

        <!-- Título principal -->
        <div class="card shadow mb-4">
          <div class="card shadow-sm border-0 mb-3" style="background: #f5f9ff;">
            <div class="card-body d-flex justify-content-between align-items-center py-3 px-4">
              <div class="d-flex align-items-center">
                <span class="rounded-circle bg-primary d-flex justify-content-center align-items-center mr-3" style="width:40px;height:40px;">
                  <i class="fa fa-truck text-white"></i>
                </span>
                <div>
                  <span class="font-weight-bold text-primary" style="font-size:1.15rem;">Proveedor:</span>
                  <span class="badge badge-light font-weight-bold px-3 py-2 ml-2" style="font-size:1.05rem;letter-spacing:1px;">
                    <?php echo strtoupper($name); ?>
                  </span>
                  <span class="text-muted ml-2" style="font-size:0.97rem;"><?php echo $code_sap; ?></span>
                </div>
              </div>
              <div>
                <button class="btn btn-outline-info btn-sm font-weight-bold mr-2 btn-edit-generales-provider" data-id="<?php echo $provider_id; ?>">
                  <i class="fa fa-edit"></i> Editar generales
                </button>
                <button class="btn btn-outline-danger btn-sm font-weight-bold" id="btn-cargar-csf-provider" data-provider-id="<?php echo $provider_id; ?>">
                  <i class="fa fa-file-pdf-o"></i> Importar CSF
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- TABS COLORIDOS CON ICONOS -->
        <div class="mb-4">
          <ul class="nav justify-content-center" id="proveedorTabs" role="tablist">
            <li class="nav-item mr-2">
              <a class="btn btn-primary rounded-pill shadow-sm active" id="tab-generales-proveedor" data-toggle="pill" href="#panel-generales-proveedor" role="tab">
                <i class="fa fa-info-circle mr-2"></i> Datos generales
              </a>
            </li>
            <li class="nav-item mr-2">
              <a class="btn btn-success rounded-pill shadow-sm" id="tab-contactos-proveedor" data-toggle="pill" href="#panel-contactos-proveedor" role="tab">
                <i class="fa fa-users mr-2"></i> Contactos
              </a>
            </li>
            <li class="nav-item mr-2">
              <a class="btn btn-warning rounded-pill shadow-sm text-white" id="tab-entregas-proveedor" data-toggle="pill" href="#panel-entregas-proveedor" role="tab">
                <i class="fa fa-truck mr-2"></i> Sucursales
              </a>
            </li>
            <li class="nav-item mr-2">
              <a class="btn btn-info rounded-pill shadow-sm text-white" id="tab-fiscales-proveedor" data-toggle="pill" href="#panel-fiscales-proveedor" role="tab">
                <i class="fa fa-file-invoice-dollar mr-2"></i>Datos Fiscales
              </a>
            </li>
            <li class="nav-item mr-2">
              <a class="btn btn-primary rounded-pill shadow-sm text-white" id="tab-bancos-proveedor" data-toggle="pill" href="#panel-bancos-proveedor" role="tab">
                <i class="fa fa-file-invoice-dollar mr-2"></i>Cuentas Bancarias
              </a>
            </li>
            <li class="nav-item mr-2">
              <a class="btn btn-info rounded-pill shadow-sm text-white" id="tab-departamentos-proveedor" data-toggle="pill" href="#panel-departamentos-proveedor" role="tab">
                <i class="fa fa-building mr-2"></i>Departamentos que surte
              </a>
            </li>
            <li class="nav-item">
              <a class="btn btn-warning rounded-pill shadow-sm text-white" id="tab-contratos-proveedor" data-toggle="pill" href="#panel-contratos-proveedor" role="tab">
                <i class="fa fa-building mr-2"></i>Contratos
              </a>
            </li>


          </ul>
        </div>


        <!-- CONTENIDO DE LOS TABS -->
        <div class="tab-content" id="proveedorTabsContent">

          <!-- TAB: DATOS GENERALES -->
          <div class="tab-pane fade show active" id="panel-generales-proveedor" role="tabpanel">
			<div class="card shadow mb-4">
				<div class="card-body">
				<fieldset>
					<legend class="heading mb-3">Información del Proveedor</legend>
					<div class="form-row">
					<div class="col-md-6 mb-3">
						<div class="form-group">
						<?php echo Form::label('Código SAP', 'code_sap', ['class' => 'form-control-label']); ?>
						<span class="form-control"><?php echo $code_sap; ?></span>
						</div>
					</div>
					<div class="col-md-6 mb-3">
						<div class="form-group">
						<?php echo Form::label('Razón Social', 'name', ['class' => 'form-control-label']); ?>
						<span class="form-control"><?php echo $name; ?></span>
						</div>
					</div>
					<div class="col-md-6 mb-3">
						<div class="form-group">
						<?php echo Form::label('RFC', 'rfc', ['class' => 'form-control-label']); ?>
						<span class="form-control"><?php echo $rfc; ?></span>
						</div>
					</div>
					<div class="col-md-6 mb-3">
						<div class="form-group">
						<?php echo Form::label('Email', 'email', ['class' => 'form-control-label']); ?>
						<span class="form-control"><?php echo $email; ?></span>
						</div>
					</div>
					</div>
				</fieldset>
				<fieldset>
					<legend class="heading mb-3">Información de Compras</legend>
					<div class="form-row">
            <div class="col-md-6 mb-3">
              <div class="form-group">
              <?php echo Form::label('Comprador', 'employee_id', ['class' => 'form-control-label']); ?>
              <span class="form-control"><?php echo $employee_id; ?></span>
              </div>
            </div>       
            <div class="col-md-6 mb-3">
              <div class="form-group">
              <?php echo Form::label('Dias de credito', 'payment_terms_id', ['class' => 'form-control-label']); ?>
              <span class="form-control"><?php echo $payment_terms_id; ?></span>
              </div>
            </div>

					</div>
				</fieldset>
        <fieldset>
          <legend class="heading mb-3">Información de Clasificación</legend>
          <div class="form-row">
            <div class="col-md-4 mb-3">
              <div class="form-group">
                <?php echo Form::label('Tipo de Proveedor', 'provider_type', ['class' => 'form-control-label']); ?>
                <span class="form-control">
                  <?php echo ($provider_type == 1 ? 'Mercancía' : 'Servicio'); ?>
                </span>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div class="form-group">
                <?php echo Form::label('Procedencia', 'origin', ['class' => 'form-control-label']); ?>
                <span class="form-control">
                  <?php echo ($origin == 1 ? 'Extranjero' : 'Nacional'); ?>
                </span>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div class="form-group">
                <?php echo Form::label('Departamento principal', 'employees_department_id', ['class' => 'form-control-label']); ?>
                <span class="form-control"><?php echo $employees_department_name; ?></span>
              </div>
            </div>
          </div>
        </fieldset>

				<fieldset>
					<legend class="heading mb-3">Información Adicional</legend>
					<div class="form-row">
					<div class="col-md-6 mb-3">
						<div class="form-group">
						<?php echo Form::label('Bloqueado', 'banned', ['class' => 'form-control-label']); ?>
						<span class="form-control">
							<?php echo ($banned == 1 ? 'Sí' : 'No'); ?>
						</span>
						</div>
					</div>
					</div>
				</fieldset>
				</div>
			</div>
			</div>


          <!-- TAB: CONTACTOS -->
          <div class="tab-pane fade" id="panel-contactos-proveedor" role="tabpanel">
            <div class="card shadow mb-4">
              <div class="card-body">
                <button class="btn btn-success btn-sm float-right mb-3 btn-add-contacto-provider" id="" data-provider-id="<?php echo $provider_id; ?>">
                  <i class="fa fa-plus"></i> Agregar contacto
                </button>
                <div class="clearfix"></div>
                <?php if (!empty($contact)): ?>
                  <?php $contact = array_values($contact); ?>
                  <?php foreach ($contact as $index => $c): ?>
                    <fieldset class="mb-4">
                      <legend class="heading">
                        Contacto #<?php echo ($index + 1); ?>
                        <button class="btn btn-info btn-xs ml-2 btn-edit-contacto-provider" data-id="<?php echo $c->id; ?>" data-provider-id="<?php echo $provider_id; ?>"> 
                          <i class="fa fa-edit"></i>
                        </button>
                      </legend>
                      <div class="form-row">
                        <div class="col-md-6 mb-3">
                          <div class="form-group">
                            <?php echo Form::label('Identificador', 'idcontact', array('class' => 'form-control-label')); ?>
                            <span class="form-control"><?php echo $c->idcontact; ?></span>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="form-group">
                            <?php echo Form::label('Nombre', 'name', array('class' => 'form-control-label')); ?>
                            <span class="form-control"><?php echo $c->name; ?></span>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="form-group">
                            <?php echo Form::label('Apellido', 'last_name', array('class' => 'form-control-label')); ?>
                            <span class="form-control"><?php echo $c->last_name; ?></span>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="form-group">
                            <?php echo Form::label('Teléfono', 'phone', array('class' => 'form-control-label')); ?>
                            <span class="form-control"><?php echo $c->phone; ?></span>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="form-group">
                            <?php echo Form::label('Correo', 'email', array('class' => 'form-control-label')); ?>
                            <span class="form-control"><?php echo $c->email; ?></span>
                          </div>
                        </div>
                        <?php if (!empty($c->updated_at)): ?>
                          <div class="col-md-12 mb-3">
                            <small class="text-muted">Última modificación: <?php echo date('d/m/Y H:i', $c->updated_at); ?></small>
                          </div>
                        <?php endif; ?>
                      </div>
                    </fieldset>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div class="alert alert-info">No hay contactos registrados.</div>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <!-- TAB: ENTREGAS -->
          <div class="tab-pane fade" id="panel-entregas-proveedor" role="tabpanel">
            <div class="card shadow mb-4">
              <div class="card-body">
                <button class="btn btn-warning btn-sm float-right mb-3 btn-add-entrega-provider" id="" data-provider-id="<?php echo $provider_id; ?>">
                  <i class="fa fa-plus"></i> Agregar sucursal
                </button>
                <div class="clearfix"></div>
                <?php if (!empty($delivery)): ?>
                  <?php $delivery = array_values($delivery); ?>
                  <?php foreach ($delivery as $index => $d): ?>
                    <fieldset class="mb-4">
                      <legend class="heading">
                        Sucursal #<?php echo ($index + 1); ?>
                        <button class="btn btn-info btn-xs ml-2 btn-edit-entrega-provider" data-id="<?php echo $d->id; ?>" data-provider-id="<?php echo $provider_id; ?>">
                          <i class="fa fa-edit"></i>
                        </button>
                      </legend>
                      <div class="form-row">
                        <div class="col-md-6 mb-3">
                          <div class="form-group">
                            <?php echo Form::label('Identificador', 'iddelivery', array('class' => 'form-control-label')); ?>
                            <span class="form-control"><?php echo $d->iddelivery; ?></span>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="form-group">
                            <?php echo Form::label('Calle', 'street', array('class' => 'form-control-label')); ?>
                            <span class="form-control"><?php echo $d->street; ?></span>
                          </div>
                        </div>
                        <div class="col-md-3 mb-3">
                          <div class="form-group">
                            <?php echo Form::label('Número', 'number', array('class' => 'form-control-label')); ?>
                            <span class="form-control"><?php echo $d->number; ?></span>
                          </div>
                        </div>
                        <div class="col-md-3 mb-3">
                          <div class="form-group">
                            <?php echo Form::label('Interior', 'internal_number', array('class' => 'form-control-label')); ?>
                            <span class="form-control"><?php echo $d->internal_number; ?></span>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="form-group">
                            <?php echo Form::label('Colonia', 'colony', array('class' => 'form-control-label')); ?>
                            <span class="form-control"><?php echo $d->colony; ?></span>
                          </div>
                        </div>
                        <div class="col-md-3 mb-3">
                          <div class="form-group">
                            <?php echo Form::label('Código Postal', 'zipcode', array('class' => 'form-control-label')); ?>
                            <span class="form-control"><?php echo $d->zipcode; ?></span>
                          </div>
                        </div>
                        <div class="col-md-3 mb-3">
                          <div class="form-group">
                            <?php echo Form::label('Ciudad', 'city', array('class' => 'form-control-label')); ?>
                            <span class="form-control"><?php echo $d->city; ?></span>
                          </div>
                        </div>
                        <div class="col-md-3 mb-3">
                          <div class="form-group">
                            <?php echo Form::label('Municipio', 'municipality', array('class' => 'form-control-label')); ?>
                            <span class="form-control"><?php echo $d->municipality; ?></span>
                          </div>
                        </div>
                        <div class="col-md-3 mb-3">
                          <div class="form-group">
                            <?php echo Form::label('Estado', 'state_id', array('class' => 'form-control-label')); ?>
                            <span class="form-control"><?php echo $d->state->name ?? ''; ?></span>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="form-group">
                            <?php echo Form::label('Horario de recepción', 'reception_hours', array('class' => 'form-control-label')); ?>
                            <span class="form-control"><?php echo $d->reception_hours; ?></span>
                          </div>
                        </div>
                        <div class="col-md-12 mb-3">
                          <div class="form-group">
                            <?php echo Form::label('Notas', 'delivery_notes', array('class' => 'form-control-label')); ?>
                            <span class="form-control"><?php echo $d->delivery_notes; ?></span>
                          </div>
                        </div>
                        <?php if (!empty($d->updated_at)): ?>
                          <div class="col-md-12 mb-3">
                            <small class="text-muted">Última modificación: <?php echo date('d/m/Y H:i', $d->updated_at); ?></small>
                          </div>
                        <?php endif; ?>
                        <div class="col-md-6 mb-3">
                        <div class="form-group">
                          <?php echo Form::label('Nombre contacto', 'name', array('class' => 'form-control-label')); ?>
                          <span class="form-control"><?php echo !empty($d->contact) ? $d->contact->name : ''; ?></span>
                        </div>
                        </div>
                        <div class="col-md-6 mb-3">
                        <div class="form-group">
                          <?php echo Form::label('Apellido contacto', 'last_name', array('class' => 'form-control-label')); ?>
                          <span class="form-control"><?php echo !empty($d->contact) ? $d->contact->last_name : ''; ?></span>
                        </div>
                        </div>
                        <div class="col-md-6 mb-3">
                        <div class="form-group">
                          <?php echo Form::label('Teléfono contacto', 'phone', array('class' => 'form-control-label')); ?>
                          <span class="form-control"><?php echo !empty($d->contact) ? $d->contact->phone : ''; ?></span>
                        </div>
                        </div>
                      </div>
                    </fieldset>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div class="alert alert-info">No hay domicilios de entrega registrados.</div>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <!-- TAB: FISCALES -->
          <div class="tab-pane fade" id="panel-fiscales-proveedor" role="tabpanel">
            <div class="card shadow mb-4">
              <div class="card-body">
                <?php if (!empty($tax_data)): ?>
                  <fieldset>
                    <legend class="heading mb-3">
                      Datos Fiscales
                      <button class="btn btn-info btn-xs ml-2 btn-edit-fiscal-provider" data-provider-id="<?php echo $provider_id; ?>">
                        <i class="fa fa-edit"></i>
                      </button>
                      <?php if (!empty($tax_data->updated_at)): ?>
                        <small class="text-muted float-right">Última modificación: <?php echo date('d/m/Y - H:i', $tax_data->updated_at); ?></small>
                      <?php endif; ?>
                    </legend>
                    <div class="form-row">
                      <div class="col-md-6 mb-3">
                        <div class="form-group">
                          <?php echo Form::label('Razón social', 'business_name', ['class' => 'form-control-label']); ?>
                          <span class="form-control"><?php echo $tax_data->business_name; ?></span>
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <div class="form-group">
                          <?php echo Form::label('RFC', 'rfc', ['class' => 'form-control-label']); ?>
                          <span class="form-control"><?php echo $tax_data->rfc; ?></span>
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <div class="form-group">
                          <?php echo Form::label('Calle', 'street', ['class' => 'form-control-label']); ?>
                          <span class="form-control"><?php echo $tax_data->street; ?></span>
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <div class="form-group">
                          <?php echo Form::label('Número', 'number', ['class' => 'form-control-label']); ?>
                          <span class="form-control"><?php echo $tax_data->number; ?></span>
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <div class="form-group">
                          <?php echo Form::label('Interior', 'internal_number', ['class' => 'form-control-label']); ?>
                          <span class="form-control"><?php echo $tax_data->internal_number; ?></span>
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <div class="form-group">
                          <?php echo Form::label('Colonia', 'colony', ['class' => 'form-control-label']); ?>
                          <span class="form-control"><?php echo $tax_data->colony; ?></span>
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <div class="form-group">
                          <?php echo Form::label('Código Postal', 'zipcode', ['class' => 'form-control-label']); ?>
                          <span class="form-control"><?php echo $tax_data->zipcode; ?></span>
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <div class="form-group">
                          <?php echo Form::label('Ciudad', 'city', ['class' => 'form-control-label']); ?>
                          <span class="form-control"><?php echo $tax_data->city; ?></span>
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <div class="form-group">
                          <?php echo Form::label('Estado', 'state_id', ['class' => 'form-control-label']); ?>
                          <span class="form-control"><?php echo $tax_data->state->name ?? ''; ?></span>
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <div class="form-group">
                          <?php echo Form::label('Régimen Fiscal', 'sat_tax_regime_id', ['class' => 'form-control-label']); ?>
                          <span class="form-control"><?php echo $tax_data->sat_tax_regime->name ?? ''; ?></span>
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <div class="form-group">
                          <?php echo Form::label('Uso de CFDI', 'cfdi_id', ['class' => 'form-control-label']); ?>
                          <span class="form-control"><?php echo $tax_data->cfdi->name ?? ''; ?></span>
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <div class="form-group">
                          <?php echo Form::label('Método de Pago', 'payment_method_id', ['class' => 'form-control-label']); ?>
                          <span class="form-control"><?php echo $tax_data->payment_method->name ?? ''; ?></span>
                        </div>
                      </div>
                      <?php if (!empty($tax_data->csf)): ?>
                        <div class="col-md-12 mb-3">
                          <div class="form-group">
                            <?php echo Form::label('Constancia de Situación Fiscal', 'csf', ['class' => 'form-control-label']); ?>
                            <span class="form-control">
                              <?php echo Html::anchor(Uri::base(false) . $tax_data->csf, 'Ver archivo', ['target' => '_blank']); ?>
                            </span>
                          </div>
                        </div>
                      <?php endif; ?>
                      <?php if (!empty($tax_data->opc)): ?>
                        <div class="col-md-12 mb-3">
                          <div class="form-group">
                            <?php echo Form::label('Opinion de cumplimiento', 'opc', ['class' => 'form-control-label']); ?>
                            <span class="form-control">
                              <?php echo Html::anchor(Uri::base(false) . $tax_data->opc, 'Ver archivo', ['target' => '_blank']); ?>
                            </span>
                          </div>
                        </div>
                      <?php endif; ?>
                    </div>
                  </fieldset>
                <?php else: ?>
                  <button class="btn btn-purple btn-sm mb-3 btn-add-fiscal-provider" style="background:#7b3ff2;color:white;" id="" data-provider-id="<?php echo $provider_id; ?>">
                    <i class="fa fa-plus"></i> Agregar datos fiscales
                  </button>
                  <div class="alert alert-info">No hay datos fiscales registrados.</div>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <!-- TAB: BANCOS -->
          <div class="tab-pane fade" id="panel-bancos-proveedor" role="tabpanel">
            <div class="card shadow mb-4">
              <div class="card-body">
                <button class="btn btn-primary btn-sm float-right mb-3 btn-add-banco-provider" data-provider-id="<?php echo $provider_id; ?>">
                  <i class="fa fa-plus"></i> Agregar cuenta bancaria
                </button>
                <div class="clearfix"></div>
                <?php if (!empty($bank_accounts)): ?>
                  <?php $bank_accounts = array_values($bank_accounts); ?>
                  <?php foreach ($bank_accounts as $index => $bank): ?>
                    <fieldset class="mb-4">
                      <legend class="heading">
                        Cuenta Bancaria #<?php echo ($index + 1); ?>
                        <button class="btn btn-info btn-xs ml-2 btn-edit-banco-provider" data-id="<?php echo $bank->id; ?>" data-provider-id="<?php echo $provider_id; ?>">
                          <i class="fa fa-edit"></i>
                        </button>
                        <?php if (!empty($bank->default) && $bank->default == 1): ?>
                          <span class="badge badge-success ml-2">Principal</span>
                        <?php endif; ?>
                        <?php if (!empty($bank->updated_at)): ?>
                          <small class="text-muted float-right">Última modificación: <?php echo date('d/m/Y H:i', $bank->updated_at); ?></small>
                        <?php endif; ?>
                      </legend>
                      <div class="form-row">
                        <div class="col-md-4 mb-3">
                          <div class="form-group">
                            <?php echo Form::label('Banco', 'bank_id', ['class' => 'form-control-label']); ?>
                            <span class="form-control"><?php echo $bank->bank->name ?? $bank->bank_id; ?></span>
                          </div>
                        </div>
                        <div class="col-md-4 mb-3">
                          <div class="form-group">
                            <?php echo Form::label('Cuenta', 'account_number', ['class' => 'form-control-label']); ?>
                            <span class="form-control"><?php echo $bank->account_number; ?></span>
                          </div>
                        </div>
                        <div class="col-md-4 mb-3">
                          <div class="form-group">
                            <?php echo Form::label('CLABE', 'clabe', ['class' => 'form-control-label']); ?>
                            <span class="form-control"><?php echo $bank->clabe; ?></span>
                          </div>
                        </div>
                        <div class="col-md-4 mb-3">
                          <div class="form-group">
                            <?php echo Form::label('Moneda', 'currency_id', ['class' => 'form-control-label']); ?>
                            <span class="form-control"><?php echo $bank->currency->name ?? $bank->currency_id; ?></span>
                          </div>
                        </div>

                        <div class="col-md-4 mb-3" style="display:none;">
                          <div class="form-group">
                              <?php echo Form::label('Días de pago', 'pay_days', ['class' => 'form-control-label']); ?>
                              <?php
                              $weekdays = [
                                  'sunday'    => 'Domingo',
                                  'monday'    => 'Lunes',
                                  'tuesday'   => 'Martes',
                                  'wednesday' => 'Miércoles',
                                  'thursday'  => 'Jueves',
                                  'friday'    => 'Viernes',
                                  'saturday'  => 'Sábado',
                              ];
                              $dias = isset($bank->pay_days) ? (is_array($bank->pay_days) ? $bank->pay_days : explode(',', $bank->pay_days)) : [];
                              if (count($dias)) {
                                  $dias_legibles = [];
                                  foreach ($dias as $day) {
                                      if (isset($weekdays[$day])) $dias_legibles[] = $weekdays[$day];
                                  }
                                  echo '<span class="form-control">' . implode(', ', $dias_legibles) . '</span>';
                              } else {
                                  echo '<span class="form-control">No capturado</span>';
                              }
                              ?>
                          </div>
                        </div>

                        <div class="col-md-4 mb-3">
                          <div class="form-group">
                            <?php echo Form::label('Beneficiario', 'name', ['class' => 'form-control-label']); ?>
                            <span class="form-control"><?php echo $bank->name; ?></span>
                          </div>
                        </div>
                        <div class="col-md-4 mb-3">
                          <div class="form-group">
                            <?php echo Form::label('Email', 'email', ['class' => 'form-control-label']); ?>
                            <span class="form-control"><?php echo $bank->email; ?></span>
                          </div>
                        </div>
                        <div class="col-md-4 mb-3">
                          <div class="form-group">
                            <?php echo Form::label('Teléfono', 'phone', ['class' => 'form-control-label']); ?>
                            <span class="form-control"><?php echo $bank->phone; ?></span>
                          </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                          <div class="form-group">
                            <?php echo Form::label('Carátula bancaria', 'bank_cover', ['class' => 'form-control-label']); ?>

                            <?php if (!empty($bank_cover_url)): ?>
                              <span class="form-control">
                                <?php echo Html::anchor($bank_cover_url, 'Ver documento', [
                                  'target' => '_blank',
                                  'class'  => 'text-primary'
                                ]); ?>
                              </span>
                            <?php else: ?>
                              <span class="form-control text-muted">No capturada</span>
                            <?php endif; ?>
                          </div>
                        </div>

                        
                      </div>
                    </fieldset>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div class="alert alert-info">No hay cuentas bancarias registradas.</div>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="panel-departamentos-proveedor" role="tabpanel">
            <div class="card shadow mb-4">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h4 class="mb-0">Departamentos que surte</h4>
                  <button class="btn btn-sm btn-primary btn-add-depto-provider" data-id="<?php echo $provider_id; ?>">
                    <i class="fas fa-plus"></i> Agregar departamento
                  </button>
                </div>
                <div class="table-responsive">
                  <table class="table table-bordered align-items-center">
                    <thead class="thead-light">
                      <tr>
                        <th>Departamento</th>
                        <th>Principal</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (!empty($departments)): ?>
                        <?php foreach ($departments as $d): ?>
                          <tr>
                            <td><?php echo $d->department->name; ?></td>
                            <td><?php echo $d['main'] ? '<span class="badge badge-success">Sí</span>' : 'No'; ?></td>
                            <td><?php echo date('d/m/Y', $d['created_at']); ?></td>
                            <td>
                              <?php if (!$d['main']): ?>
                                <button class="btn btn-sm btn-success btn-set-main-depto" data-id="<?php echo $d['id']; ?>">
                                  <i class="fas fa-check"></i> Principal
                                </button>
                              <?php endif; ?>
                              <button class="btn btn-sm btn-danger btn-delete-depto" data-id="<?php echo $d['id']; ?>">
                                <i class="fas fa-trash"></i>
                              </button>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <tr><td colspan="4" class="text-center">Sin departamentos registrados.</td></tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>


          <!-- TAB: CONTRATOS -->
          <div class="tab-pane fade" id="panel-contratos-proveedor" role="tabpanel">
            <div class="card shadow mb-4">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h4 class="mb-0">Contratos del proveedor</h4>
                  <button class="btn btn-sm btn-dark btn-add-contrato-provider"
                          data-provider-id="<?php echo $provider_id; ?>"
                          data-user-id="<?php echo (int)($provider_user_id ?? 0); ?>">
                    <i class="fa fa-plus"></i> Nuevo contrato
                  </button>
                </div>

                <?php if (!empty($contracts)): ?>
                  <div class="table-responsive">
                    <table class="table table-bordered align-items-center">
                      <thead class="thead-light">
                        <tr>
                          <th>Folio</th>
                          <th>Título</th>
                          <th>Categoría</th>
                          <th>Vigencia</th>
                          <th>Estatus</th>
                          <th>Archivo</th>
                          <th>Actualizado</th>
                          <th style="width:140px;">Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($contracts as $c): ?>
                          <tr>
                            <td><?php echo $c->code ?: '-'; ?></td>
                            <td><?php echo $c->title; ?></td>
                            <td><?php echo $c->category ?: 'N/A'; ?></td>
                            <td>
                              <?php
                                $ini = $c->start_date ?: '';
                                $fin = $c->end_date   ?: '';
                                echo ($ini ? date('d/m/Y', strtotime($ini)) : '-') . ' - ' . ($fin ? date('d/m/Y', strtotime($fin)) : '-');
                              ?>
                            </td>
                            <td>
                              <?php
                                $labels = [0=>'Borrador',1=>'Vigente',2=>'Vencido',3=>'Cancelado'];
                                $badge  = [0=>'secondary',1=>'success',2=>'warning',3=>'danger'];
                                $st = (int)$c->status;
                                echo '<span class="badge badge-'.$badge[$st].'">'.$labels[$st].'</span>';
                              ?>
                            </td>
                            <td>
                              <?php if (!empty($c->file_path)): ?>
                                <?php echo Html::anchor(Uri::base(false).$c->file_path, 'Ver PDF', ['target'=>'_blank', 'class'=>'btn btn-sm btn-neutral']); ?>
                              <?php else: ?>
                                <span class="text-muted">Sin archivo</span>
                              <?php endif; ?>
                            </td>
                            <td><?php echo $c->updated_at ? date('d/m/Y H:i', $c->updated_at) : '-'; ?></td>
                            <td>
                              <button class="btn btn-sm btn-info btn-edit-contrato-provider"
                                      data-id="<?php echo $c->id; ?>"
                                      data-provider-id="<?php echo $provider_id; ?>">
                                <i class="fa fa-edit"></i>
                              </button>
                              <button class="btn btn-sm btn-danger btn-delete-contrato-provider"
                                      data-id="<?php echo $c->id; ?>">
                                <i class="fa fa-trash"></i>
                              </button>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php else: ?>
                  <div class="alert alert-info">No hay contratos registrados.</div>
                <?php endif; ?>
              </div>
            </div>
          </div>





        </div>
        <!-- /tab-content -->
      </div>
    </div>
  </div>
</div>



