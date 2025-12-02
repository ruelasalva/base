
<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Encuestas</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/crm/survey/results', 'Resultado de las encuentas'); ?>
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
							<h3 class="mb-0">Resultado de la encuesta de satisfacion</h3>
						</div>
						<div class="col-md-3 mb-0">
						</div>
					</div>
				</div>
				<!-- LIGHT TABLE -->
				<div class="table-responsive table-hover" data-toggle="lists" data-list-values='["name", "id", "session_id", "ip", "survey_code", "email", "ratingventa", "ratingsurtido", "ratingentrega", "recomienda", "comment", "created_at"]'>
					<table class="table align-items-center table-flush">
						<thead class="thead-light">
							<tr>
								<th scope="col" class="sort" data-sort="id">ID</th>
								<th scope="col" class="sort" data-sort="sesssion_id">Session ID</th>
								<th scope="col" class="sort" data-sort="ip">IP</th>
								<th scope="col" class="sort" data-sort="survey_code">Codigo de Encuesta</th>
								<th scope="col" class="sort" data-sort="name">Nombre</th>
								<th scope="col" class="sort" data-sort="email">Correo</th>
								<th scope="col" class="sort" data-sort="ratingventa">Rating Venta</th>
								<th scope="col" class="sort" data-sort="ratingsurtido">Ranting Surtido</th>
								<th scope="col" class="sort" data-sort="ratingentrega">Rating Entrega</th>
								<th scope="col" class="sort" data-sort="recomienda">Nos recomendaria</th>
								<th scope="col" class="sort" data-sort="comment">Comentarios</th>
								<th scope="col" class="sort" data-sort="created_at">Fecha</th>
							</tr>
						</thead>
						<tbody class="list">
							<?php if(!empty($surveys)): ?>
								<?php foreach($surveys as $survey): ?>
									<tr>
									    <th class="id">
											<?php echo  $survey['id']; ?>
										</th>
										<th class="session_id">
											<?php echo  $survey['session_id']; ?>
										</th>
										<th class="ip">
											<?php echo  $survey['ip']; ?>
										</th>
										<th class="survey_code">
											<?php echo  $survey['survey_code']; ?>
										</th>
										<th class="name">
											<?php echo  $survey['name']; ?>
										</th>
                                        <th class="email">
											<?php echo  $survey['email']; ?>
										</th>
                                        <th class="ratingventa">
											<?php echo  $survey['ratingventa']; ?>
										</th>
                                        <th class="ratingsurtido">
											<?php echo  $survey['ratingsurtido']; ?>
										</th>
                                        <th class="ratingentrega">
											<?php echo  $survey['ratingentrega']; ?>
										</th>
										<th class="recomienda">
											<?php echo  $survey['recomienda']; ?>
										</th>
                                        <th class="comment">
											<?php echo  $survey['comment']; ?>
										</th>
                                        <th class="Fecha">
											<?php echo  $survey['created_at']; ?>
										</th>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<th scope="row">
										No existen registros
									</th>
								</tr>
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