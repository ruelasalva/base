<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">
						<i class="fa-solid fa-ticket-alt mr-2"></i> Tickets - Gráficas
					</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fa-solid fa-house"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/helpdesk/ticket', 'Graficas'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Form::open(array('action' => 'admin/helpdesk/ticket/buscar_graficas', 'method' => 'post')); ?>
					<div class="form-row align-items-center justify-content-end">
						<div class="col-md-3 mb-0">
							<div class="input-group input-group-sm mt-3 mt-md-0">
								<?php echo Form::input('start_date', (isset($start_date) ? $start_date : ''), array('id' => 'start_date', 'class' => 'form-control datepicker', 'placeholder' => 'Fecha inicio')); ?>
							</div>
						</div>
						<div class="col-md-3 mb-0">
							<div class="input-group input-group-sm mt-3 mt-md-0">
								<?php echo Form::input('end_date', (isset($end_date) ? $end_date : ''), array('id' => 'end_date', 'class' => 'form-control datepicker', 'placeholder' => 'Fecha final')); ?>
							</div>
						</div>
						<div class="col-md-2 mb-0">
							<div class="input-group input-group-sm mt-3 mt-md-0">
								<?php echo Form::submit(array('value'=> 'Buscar', 'name'=>'submit', 'class' => 'btn btn-primary btn-sm btn-block')); ?>
							</div>
						</div>
					</div>
					<?php echo Form::close(); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow border-0">
                <div class="card-header bg-gradient-info text-white d-flex align-items-center">
                    <i class="fa-solid fa-users-cog fa-lg mr-2"></i>
                    <span class="h5 mb-0">Tickets por personal de TI</span>
                </div>
                <div class="card-body">
                    <canvas id="tick-user" style="min-height:280px"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow border-0">
                <div class="card-header bg-gradient-warning text-white d-flex align-items-center">
                    <i class="fa-solid fa-list-check fa-lg mr-2"></i>
                    <span class="h5 mb-0">Tickets por estatus</span>
                </div>
                <div class="card-body">
                    <canvas id="tick-status" style="min-height:280px"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow border-0">
                <div class="card-header bg-gradient-success text-white d-flex align-items-center">
                    <i class="fa-solid fa-layer-group fa-lg mr-2"></i>
                    <span class="h5 mb-0">Tickets por tipo</span>
                </div>
                <div class="card-body">
                    <canvas id="tick-types" style="min-height:280px"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow border-0">
                <div class="card-header bg-gradient-danger text-white d-flex align-items-center">
                    <i class="fa-solid fa-building fa-lg mr-2"></i>
                    <span class="h5 mb-0">Tickets por departamento</span>
                </div>
                <div class="card-body">
                    <canvas id="tick-department" style="min-height:280px"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Variables JS desde PHP -->
<script type="text/javascript">
	var $chart_labels   = <?php echo $users; ?>;
	var $chart_label    = 'Soporte';
	var $chart_data     = <?php echo $tickets; ?>;

	var $chart_labels1  = <?php echo $status; ?>;
	var $chart_label1   = 'Estatus';
	var $chart_data1    = <?php echo $tickets2; ?>;

	var $chart_labels3  = <?php echo $type; ?>;
	var $chart_label3   = 'Tipos';
	var $chart_data3    = <?php echo $tickets3; ?>;

	var $chart_labels4  = <?php echo $department; ?>;
	var $chart_label4   = 'Departamentos';
	var $chart_data4    = <?php echo $tickets4; ?>;
</script>
