<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Tickets Socios</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/crm/socios/ticket', 'Tickets'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					
					<br>
                    	<h6 class="h4 text-white d-inline-block mb-0"></h6><p class="h6 text-white" id="last-update"></p><p class="h6 text-white" id="next-update"></p>
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
					<?php echo Form::open(array('action' => 'admin/crm/socios/ticket/buscar', 'method' => 'post')); ?>
					<div class="form-row">
						<div class="col-md-9">
							<h3 class="mb-0">Mis Tickets</h3>
						</div>
						<div class="col-md-3 mb-0">
							<div class="input-group input-group-sm mt-3 mt-md-0">
								<?php echo Form::input('search', (isset($search) ? $search : ''), array('id' => 'search', 'class' => 'form-control', 'placeholder' => 'ID o Asunto', 'aria-describedby' => 'button-addon')); ?>
								<div class="input-group-append">
									<?php echo Form::submit(array('value'=> 'Buscar', 'name'=>'submit', 'id' => 'button-addon', 'class' => 'btn btn-outline-primary')); ?>
								</div>
							</div>
						</div>
					</div>
					<?php echo Form::close(); ?>
				</div>

				<!-- TABLA DE TICKETS -->
				<!-- TABLA -->
        <div class="table-responsive" data-toggle="lists" data-list-values='["id", "partner_id", "subject", "message", "asig_user_id", "created_at"]'>
          <table class="table align-items-center table-flush">
            <thead class="thead-light">
              <tr>
                <th class="sort" data-sort="id">ID</th>
                <th class="sort" data-sort="partner_id">Socio</th>
                <th class="sort" data-sort="subject">Título</th>
                <th class="sort" data-sort="message">Descripción<br>Corta</th>
                <th class="sort" data-sort="asig_user_id">Atendido por</th>
                <th class="sort" data-sort="created_at">Creado</th>
                <th></th>
              </tr>
            </thead>
            <tbody class="list">
              <?php if(!empty($tickets)): ?>
                <?php foreach($tickets as $ticket): ?>
                <tr>
                  <td><?php echo Html::anchor('admin/crm/socios/ticket/info/'.$ticket['id'], $ticket['id']); ?></td>
                  <td><?php echo $ticket['partner_id']; ?></td>
                  <td><?php echo $ticket['subject']; ?></td>
                  <td><?php echo $ticket['message']; ?></td>
                  <td><?php echo $ticket['asig_user_id']; ?></td>
                <td><?php echo $ticket['created_at']; ?></td>
                  <td class="text-right">
                    <div class="dropdown">
                      <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i>
                      </a>
                      <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                        <?php echo Html::anchor('admin/crm/socios/ticket/info/'.$ticket['id'], 'Ver', array('class' => 'dropdown-item')); ?>
                        <?php echo Html::anchor('admin/crm/socios/ticket/cancelar/'.$ticket['id'], 'Cancelar', array('class' => 'dropdown-item')); ?>
                      </div>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="8">No hay tickets disponibles.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

				<?php if($pagination != ''): ?>
				<!-- CARD FOOTER -->
				<div class="card-footer py-4">
					<?php echo $pagination; ?>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<script>
  function mostrarProximaActualizacion() {
    var nextUpdateElement = document.getElementById('next-update');
    var lastUpdateElement = document.getElementById('last-update');

    var currentDateTime = moment();
    var formattedCurrentDateTime = currentDateTime.format('DD-MM-YYYY h:mm:ss A');
   
    lastUpdateElement.textContent = 'Última actualización: ' + formattedCurrentDateTime;
   
    var nextUpdateDateTime = moment().add(5, 'minutes');
    var formattedNextUpdate = nextUpdateDateTime.format('DD-MM-YYYY h:mm:ss A');
    nextUpdateElement.textContent = 'Próxima actualización: ' + formattedNextUpdate;
   
    var secondsUntilNextUpdate = nextUpdateDateTime.diff(moment(), 'seconds');
 
    setTimeout(function() {
      location.reload(); 
    }, secondsUntilNextUpdate * 1000); 
  }
  window.addEventListener('DOMContentLoaded', mostrarProximaActualizacion);
</script>

