<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Calculadora de Cortes</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/crm/corte', 'Calculadora de Cortes'); ?>
							</li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
	<!-- TABLE -->
	<div class="row">
		<div class="col">
			<div class="card">
				<!-- CARD HEADER -->
				<div class="card-header border-0">
					<div class="form-row">
						<div class="col-md-9">
							<h3 class="mb-0">Calculadora de Cortes</h3>
						</div>
						<div class="col-md-3 mb-0">
						</div>
					</div>
				</div>
				<!-- LIGHT TABLE -->
					 <div class="container" id="calculadora_cortes">
						<div class="row">
							<div class="panel panel-default">
								<div class="panel-body">
									<form class="form-horizontal" role="form" id="form_calc">
										<div class="row">
											<div class="col-sm-6">
												<h4 class="text-center">Tamaño del papel extendido</h4>
												<div class="form-group">
													<label class="control-label col-sm-4" for="papel_ancho">Ancho:</label>
													<div class="col-sm-7">
														<div class="input-group" id="grupo_papel_ancho">
															<input class="form-control" id="papel_ancho" placeholder="Ancho" aria-describedby="basic-addon2" maxlength="5" tabindex="1" required="">
															<span class="input-group-addon" id="basic-addon2">cm</span>                                                
														</div>
													</div>
													<label class="control-label col-sm-4" for="papel_largo">Largo:</label>
													<div class="col-sm-7">
														<div class="input-group">
															<input class="form-control" id="papel_largo" placeholder="Largo" aria-describedby="basic-addon2" maxlength="5" tabindex="2" required="">
															<span class="input-group-addon" id="basic-addon2">cm</span>
														</div>                                   
													</div>
													<label class="control-label col-sm-4" for="papel_gramaje">Gramaje:</label>
													<div class="col-sm-7">
														<input class="form-control" id="papel_gramaje" maxlength="3" placeholder="Gramaje">
													</div>
												</div>
												<h4 class="text-center">Tamaño de corte</h4>
												<div class="form-group">
													<label class="control-label col-sm-4" for="corte_ancho">Ancho:</label>
													<div class="col-sm-7">
														<div class="input-group">
															<input class="form-control" id="corte_ancho" placeholder="Ancho" aria-describedby="basic-addon2" maxlength="5" tabindex="3" required="">
															<span class="input-group-addon" id="basic-addon2">cm</span>
														</div>
													</div>
													<label class="control-label col-sm-4" for="corte_largo">Largo:</label>
													<div class="col-sm-7">
														<div class="input-group">
															<input class="form-control" id="corte_largo" placeholder="Largo" aria-describedby="basic-addon2" maxlength="5" tabindex="4" required="">
															<span class="input-group-addon" id="basic-addon2">cm</span>
														</div>                                   
													</div>
												</div>
												<h4 class="text-center">Tamaños finales deseados</h4>
												<div class="form-group">
													<label class="control-label col-sm-4" for="numero_cortes">Tamaños:</label>
													<div class="col-sm-7">                                    
														<input class="form-control" id="cortes_deseados" placeholder="No. de tamaños">                                                                            
													</div>                               
												</div>
											</div>    
											<div class="col-sm-6">
												<h4 class="text-center">Área de corte</h4>
												<div id="dibujo" class="text-center">
													<canvas height="250" width="250" id="micanvas">
														Su navegador no soporta en elemento CANVAS
													</canvas>
												</div>
												<span class="label label-danger"><span id="area_inutilizada">0</span>% sin utilizar</span>
												<span class="label label-success"><span id="area_utilizada">0</span>% utilizado</span>
											</div>
										</div>
										<div class="row">
											<div class="col-sm-12">
												<button type="submit" class="btn btn-success" id="maximo">
													<span class="glyphicon glyphicon-th"></span> Máximo
												</button>
												<button type="button" class="btn btn-success" id="orientacion_h">
													Horizontal
												</button>
												<button type="button" class="btn btn-success" id="orientacion_v">
													Vertical
												</button>
												<button type="submit" class="btn btn-success" id="reset">
													<span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> Borrar
												</button>
											</div>
										</div>
									</form>
									<div class="" id="result_panel" style="">
										<hr>
										<div class="col-sm-12 text-center">
											<h4 class=""><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Reporte</h4>
										</div>                            
										<div class="form-horizontal">
											<div class="row">
												<div class="col-sm-6">
													<label class="control-label col-sm-9" for="papel_ancho">Cortes por pliego:</label>
													<div class="col-sm-3">
														<span class="label-result" id="cortes_pliego">0</span>
													</div>
													<label class="control-label col-sm-9" for="cortes_utilizables">No. utilizables:</label>
													<div class="col-sm-3">
														<span class="label-result" id="cortes_utilizables">0</span>
													</div>
													<label class="control-label col-sm-9" for="numero_cortes_horizontal">No. horizontales:</label>
													<div class="col-sm-3">
														<span class="label-result" id="numero_cortes_horizontal">0</span>
													</div>
													<label class="control-label col-sm-9" for="numero_cortes_vertical">No. verticales:</label>
													<div class="col-sm-3">
														<span class="label-result" id="numero_cortes_vertical">0</span>
													</div>
												</div>    
												<div class="col-sm-6">
													<label class="control-label col-sm-9" for="pliegos">No. de pliegos:</label>
													<div class="col-sm-3">
														<span class="label-result" id="pliegos">0</span>
													</div>
													<label class="control-label col-sm-9" for="numero_cortes">No. de tamaños finales:</label>
													<div class="col-sm-3">
														<span class="label-result" id="numero_cortes">0</span>
													</div>
													<label class="control-label col-sm-6" for="peso">Peso final:</label>
													<div class="col-sm-6" style="text-align: right;">
														<span class="label-result" id="peso">0</span><span class="label-result"> Kg</span>
													</div>
												</div>  
											</div>
										</div>
										
									</div>
								</div>
							</div>
						</div>
					</div>


		
			
			</div>
		</div>
	</div>
</div>