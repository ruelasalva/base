<!-- ENCABEZADO -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0"><i class="fas fa-user-tie"></i> Socio de Negocio</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item"><?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?></li>
							<li class="breadcrumb-item"><?php echo Html::anchor('admin/socios', 'Socios de Negocio'); ?></li>
							<li class="breadcrumb-item active"><?php echo $code_sap; ?></li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/socios/csv/exportar_info/'.$user_id, 'Exportar CSV', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php echo Html::anchor('admin/socios/recuperar_contrasena_socios/'.$id, '<i class="fas fa-id-card"></i> Recuperacion de contraseña', ['class' => 'btn btn-sm btn-neutral']); ?>
					<?php echo Html::anchor('admin/socios/editar/'.$id, '<i class="fas fa-edit"></i> Editar', ['class' => 'btn btn-sm btn-neutral']); ?>
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
          <div class="card-body pb-0">
            <h5 class="card-title">
              Socio: <?php echo $code_sap; ?> - <?php echo $name; ?>
              <!-- Botón editar generales -->
              <button class="btn btn-info btn-xs ml-2 btn-edit-generales" data-id="<?php echo $user_id; ?>">
                <i class="fa fa-edit"></i>
              </button>
            </h5>
          </div>
        </div>

        <!-- TABS COLORIDOS CON ICONOS -->
        <div class="mb-4">
          <ul class="nav nav-pills justify-content-center" id="socioTabs" role="tablist">
            <li class="nav-item">
              <a class="nav-link active bg-primary text-white mr-2" id="tab-generales" data-toggle="pill" href="#panel-generales" role="tab">
                <i class="fa fa-info-circle"></i> Datos generales
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link bg-success text-white mr-2" id="tab-contactos" data-toggle="pill" href="#panel-contactos" role="tab">
                <i class="fa fa-users"></i> Contactos
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link bg-warning text-white mr-2" id="tab-entregas" data-toggle="pill" href="#panel-entregas" role="tab">
                <i class="fa fa-truck"></i> Entregas
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" style="background:#7b3ff2;" id="tab-fiscales" data-toggle="pill" href="#panel-fiscales" role="tab">
                <i class="fa fa-file-invoice-dollar"></i> Fiscales
              </a>
            </li>
          </ul>
        </div>

        <!-- CONTENIDO DE LOS TABS -->
        <div class="tab-content" id="socioTabsContent">

          <!-- TAB: DATOS GENERALES -->
          <div class="tab-pane fade show active" id="panel-generales" role="tabpanel">
			<div class="card shadow mb-4">
				<div class="card-body">
				<fieldset>
					<legend class="heading mb-3">Información del Socio</legend>
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
					<legend class="heading mb-3">Información de Ventas</legend>
					<div class="form-row">
					<div class="col-md-6 mb-3">
						<div class="form-group">
						<?php echo Form::label('Usuario Web', 'customer_id', ['class' => 'form-control-label']); ?>
						<span class="form-control"><?php echo $customer_id; ?></span>
						</div>
					</div>
					<div class="col-md-6 mb-3">
						<div class="form-group">
						<?php echo Form::label('Vendedor', 'employee_id', ['class' => 'form-control-label']); ?>
						<span class="form-control"><?php echo $employee_id; ?></span>
						</div>
					</div>
					<div class="col-md-6 mb-3">
						<div class="form-group">
						<?php echo Form::label('Lista de Precios', 'type_id', ['class' => 'form-control-label']); ?>
						<span class="form-control"><?php echo $type_id; ?></span>
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
          <div class="tab-pane fade" id="panel-contactos" role="tabpanel">
            <div class="card shadow mb-4">
              <div class="card-body">
                <button class="btn btn-primary btn-sm float-right mb-3" id="btn-agregar-contacto" data-partner-id="<?php echo $partner_id; ?>">
                  <i class="fa fa-plus"></i> Agregar contacto
                </button>
                <div class="clearfix"></div>
                <?php if (!empty($contact)): ?>
                  <?php $contact = array_values($contact); ?>
                  <?php foreach ($contact as $index => $c): ?>
                    <fieldset class="mb-4">
                      <legend class="heading">
                        Contacto #<?php echo ($index + 1); ?>
                        <button class="btn btn-info btn-xs ml-2 btn-edit-contacto" data-id="<?php echo $c->id; ?>" data-partner-id="<?php echo $partner_id; ?>"> 
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
          <div class="tab-pane fade" id="panel-entregas" role="tabpanel">
            <div class="card shadow mb-4">
              <div class="card-body">
                <button class="btn btn-warning btn-sm float-right mb-3" id="btn-agregar-entrega" data-partner-id="<?php echo $partner_id; ?>">
					<i class="fa fa-plus"></i> Agregar domicilio de entrega
				</button>
                <div class="clearfix"></div>
                <?php if (!empty($delivery)): ?>
                  <?php $delivery = array_values($delivery); ?>
                  <?php foreach ($delivery as $index => $d): ?>
                    <fieldset class="mb-4">
                      <legend class="heading">
                        Entrega #<?php echo ($index + 1); ?>
                        <?php
                          $partes = array_filter([
                            $d->street ?? '',
                            $d->number ?? '',
                            $d->internal_number ?? '',
                            $d->colony ?? '',
                            $d->city ?? '',
                            $d->municipality ?? '',
                            $d->state->name ?? '',
                            'México',
                            'CP ' . ($d->zipcode ?? ''),
                          ]);

                          $direccion = implode(', ', $partes);
                          $direccion_encoded = urlencode($direccion);
                        ?>
                        <button class="btn btn-info btn-xs ml-2 btn-edit-entrega" data-id="<?php echo $d->id; ?>" data-partner-id="<?php echo $partner_id; ?>">
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
                      <div class="col-md-12 mb-3">
                        <button class="btn btn-info btn-sm" type="button" data-toggle="collapse" data-target="#mapa-<?php echo $index; ?>">
                          <i class="fas fa-map-marked-alt"></i> Ver ubicación aproximada
                        </button>
                        <div class="collapse mt-3" id="mapa-<?php echo $index; ?>">
                          <div class="embed-responsive embed-responsive-16by9" style="border:1px solid #ccc; border-radius:8px;">
                            <iframe
                              class="embed-responsive-item"
                              style="border:0;"
                              loading="lazy"
                              allowfullscreen
                              referrerpolicy="no-referrer-when-downgrade"
                              src="https://www.google.com/maps?q=<?php echo urlencode($direccion); ?>&output=embed">
                            </iframe>
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
          <div class="tab-pane fade" id="panel-fiscales" role="tabpanel">
            <div class="card shadow mb-4">
              <div class="card-body">
                <?php if (!empty($tax_data)): ?>
                  <fieldset>
                    <legend class="heading mb-3">
                      Datos Fiscales
                      <button class="btn btn-info btn-xs ml-2 btn-edit-fiscal" data-partner-id="<?php echo $partner_id; ?>">
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
                    </div>
                  </fieldset>
                <?php else: ?>
                  <button class="btn btn-purple btn-sm mb-3" style="background:#7b3ff2;color:white;" id="btn-agregar-fiscal" data-partner-id="<?php echo $partner_id; ?>">
                    <i class="fa fa-plus"></i> Agregar datos fiscales
                  </button>
                  <div class="alert alert-info">No hay datos fiscales registrados.</div>
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



