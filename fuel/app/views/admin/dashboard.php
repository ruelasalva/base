<!-- DASHBOARD HEADER -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <!-- TÍTULO -->
                    <h6 class="h2 text-white d-inline-block mb-0">
                        <i class="fa-solid fa-gauge-high mr-2"></i> Dashboard
                    </h6>
                    <!-- BREADCRUMB -->
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark mb-0">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fa-solid fa-house"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right d-none d-sm-block">
                    <h5 class="text-white d-inline-block mb-0">
                        <i class="fa-regular fa-clock mr-2"></i>
                        <?php echo $date; ?>
                    </h5><br>
                    <span class="h6 text-white">Actualización automática cada 5 minutos</span><br>
                    <span class="h6 text-white" id="last-update"></span><br>
                    <span class="h6 text-white" id="next-update"></span>
                </div>
            </div>
            <!-- CARD STATS -->
            <div class="row">
                <div class="col-xl-4 col-md-6">
                    <div class="card card-stats shadow">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Ventas</h5>
                                    <span class="h2 font-weight-bold mb-0"><?php echo $sales_count; ?></span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-gradient-danger text-white rounded-circle shadow">
                                        <i class="fa-solid fa-cart-shopping fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6">
                    <div class="card card-stats shadow">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Clientes</h5>
                                    <span class="h2 font-weight-bold mb-0"><?php echo $users_count; ?></span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-gradient-info text-white rounded-circle shadow">
                                        <i class="fa-solid fa-users fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-12">
                    <div class="card card-stats shadow">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Socios Actualizados</h5>
                                    <span class="h2 font-weight-bold mb-0"><?php echo $updated_partners_week . ' de ' . $total_partners; ?></span>
                                    <small class="text-muted">Actualizados esta semana</small>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-gradient-success text-white rounded-circle shadow">
                                        <i class="fa-solid fa-user-check fa-2x"></i>
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

<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
    <div class="row">
        <!-- Últimas Ventas -->
        <div class="col-xl-6">
            <div class="card card-height shadow">
                <div class="card-header bg-gradient-purple text-white">
                    <h6 class="text-uppercase ls-1 mb-1"><i class="fa-solid fa-receipt"></i> Últimas</h6>
                    <h5 class="h3 mb-0"><i class="fa-solid fa-cart-shopping"></i> Ventas</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php if(!empty($sales)): ?>
                            <?php foreach($sales as $sale): ?>
                                <li class="list-group-item flex-column align-items-start py-4 px-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fa-solid fa-user text-info mr-3 fa-lg"></i>
                                        <div class="flex-fill">
                                            <h5 class="mb-0"><?php echo Html::anchor('admin/ventas/info/'.$sale['id'], $sale['customer'].' - '.$sale['email']); ?></h5>
                                            <small class="text-muted"><?php echo $sale['total'].' - '.$sale['type']; ?></small> ·
                                            <small class="text-muted"><?php echo $sale['sale_date']; ?></small><br>
                                            <span class="badge badge-<?php echo ($sale['status'] == 'Cancelada') ? 'warning' : 'success'; ?>">
                                                <?php echo $sale['status']; ?>
                                            </span>
                                            <span class="badge badge-secondary"><?php echo $sale['order']; ?></span>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="list-group-item text-center text-muted">Sin ventas recientes.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Últimos Clientes -->
        <div class="col-xl-6">
            <div class="card card-height shadow">
                <div class="card-header bg-gradient-info text-white">
                    <h6 class="text-uppercase ls-1 mb-1"><i class="fa-solid fa-users"></i> Últimos</h6>
                    <h5 class="h3 mb-0"><i class="fa-solid fa-user-plus"></i> Clientes</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php if(!empty($users)): ?>
                            <?php foreach($users as $user): ?>
                                <li class="list-group-item flex-column align-items-start py-4 px-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fa-solid fa-user-circle text-primary mr-3 fa-lg"></i>
                                        <div class="flex-fill">
                                            <h5 class="mb-0"><?php echo $user['username']; ?></h5>
                                            <small><?php echo $user['email']; ?></small><br>
                                            <span class="badge badge-<?php echo ($user['connected'] == 'Conectado') ? 'success' : 'warning'; ?>">
                                                <?php echo $user['connected']; ?>
                                            </span>
                                            <small class="text-muted d-block"><?php echo $user['updated_at']; ?></small>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="list-group-item text-center text-muted">Sin clientes recientes.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ACTUALIZACIÓN -->
<script>
  function mostrarProximaActualizacion() {
    var nextUpdateElement = document.getElementById('next-update');
    var lastUpdateElement = document.getElementById('last-update');

    var currentDateTime = moment();
    var formattedCurrentDateTime = currentDateTime.format('DD-MM-YYYY h:mm:ss A');
    lastUpdateElement.textContent = 'Última actualización: ' + formattedCurrentDateTime;

    var nextUpdateDateTime = moment().add(5, 'minutes');
    var formattedNextUpdate = nextUpdateDateTime.format('DD-MM-YYYY h:mm:ss A');
    nextUpdateElement.textContent = ' | Próxima actualización: ' + formattedNextUpdate;

    var secondsUntilNextUpdate = nextUpdateDateTime.diff(moment(), 'seconds');
    setTimeout(function() { location.reload(); }, secondsUntilNextUpdate * 1000); 
  }
  window.addEventListener('DOMContentLoaded', mostrarProximaActualizacion);
</script>
