<!-- CONTENT -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Creador de Plantillas</h6>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt--6">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="mb-0">Plantillas Base</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($templates as $tpl): ?>
                            <div class="col-md-4 mb-3">
                                <div class="border p-3 rounded shadow-sm h-100 d-flex flex-column justify-content-between">
                                    
                                    <!-- Título -->
                                    <h5 class="font-weight-bold mb-2"><?= $tpl['title'] ?></h5>
                                    
                                    <!-- Descripción -->
                                    <p class="text-muted mb-2">
                                        <?= $tpl['desc'] ?>
                                    </p>
                                    
                                    <!-- Asunto sugerido -->
                                    <p class="mb-3">
                                        <strong>Asunto sugerido:</strong><br>
                                        <?= $tpl['subject'] ?>
                                    </p>
                                    
                                    <!-- Botones -->
                                    <div class="mt-auto">
                                        <?php echo Html::anchor(
                                            'admin/configuracion/correos/creator/preview/'.$tpl['code'],
                                            'Ver Ejemplo',
                                            ['class'=>'btn btn-info btn-sm mr-1']
                                        ); ?>
                                        <?php echo Html::anchor(
                                            'admin/configuracion/correos/creator/clonar/'.$tpl['code'],
                                            'Clonar y Editar',
                                            ['class'=>'btn btn-success btn-sm']
                                        ); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
