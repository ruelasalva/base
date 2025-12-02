<!-- CONTENT -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Preferencias de Cookies</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Cookies</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/legal/cookies/agregar', 'Agregar', ['class'=>'btn btn-sm btn-neutral']); ?>
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
                    <?php echo Form::open(['action'=>'admin/legal/cookies/buscar','method'=>'post']); ?>
                    <div class="form-row">
                        <div class="col-md-9">
                            <h3 class="mb-0">Lista de Preferencias de Cookies</h3>
                        </div>
                        <div class="col-md-3 mb-0">
                            <div class="input-group input-group-sm mt-3 mt-md-0">
                                <?php echo Form::input('search',(isset($search)?$search:''),[
                                    'id'=>'search',
                                    'class'=>'form-control',
                                    'placeholder'=>'Usuario, email o token',
                                    'aria-describedby'=>'button-addon'
                                ]); ?>
                                <div class="input-group-append">
                                    <?php echo Form::submit([
                                        'value'=>'Buscar',
                                        'name'=>'submit',
                                        'id'=>'button-addon',
                                        'class'=>'btn btn-outline-primary'
                                    ]); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php echo Form::close(); ?>
                </div>

                <!-- TABLE -->
                <div class="table-responsive" data-toggle="lists" data-list-values='["user","analytics","marketing","personal","updated","type"]'>
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col" class="sort" data-sort="user">ID</th>
                                <th scope="col" class="sort" data-sort="user">Usuario/Token</th>
                                <th scope="col" class="sort" data-sort="analytics">Analíticas</th>
                                <th scope="col" class="sort" data-sort="marketing">Marketing</th>
                                <th scope="col" class="sort" data-sort="personal">Personalización</th>
                                <th scope="col" class="sort" data-sort="updated">Última actualización</th>
                                <th scope="col" class="sort" data-sort="type">Tipo</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            <?php if(!empty($prefs)): ?>
                                <?php foreach($prefs as $p): ?>
                                    <tr>
                                        <td class="user"><?php echo $p['id']; ?></td>
                                        <td class="user"><?php echo $p['user']; ?></td>
                                        <td class="analytics"><?php echo $p['analytics']; ?></td>
                                        <td class="marketing"><?php echo $p['marketing']; ?></td>
                                        <td class="personal"><?php echo $p['personal']; ?></td>
                                        <td class="updated"><?php echo $p['updated']; ?></td>
                                        <td class="type"><?php echo ucfirst($p['type']); ?></td>
                                        <td class="text-right">
                                            <?php echo Html::anchor('admin/legal/cookies/info/'.$p['id'].'/'.$p['type'],'<i class="fa fa-eye"></i>',[
                                                'class'=>'btn btn-sm btn-info',
                                                'title'=>'Ver detalle'
                                            ]); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No existen registros</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- CARD FOOTER -->
                <?php if($pagination != ''): ?>
                    <div class="card-footer py-4">
                        <?php echo $pagination; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
