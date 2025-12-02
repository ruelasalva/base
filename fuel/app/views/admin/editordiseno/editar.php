<!-- EDITOR VISUAL DE PLANTILLAS (GRAPESJS) -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">
                        Editar plantilla visual
                    </h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/editordiseno', 'Plantillas visuales'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Editar: <?php echo $plantilla->name; ?>
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/editordiseno', 'Regresar', ['class'=>'btn btn-sm btn-neutral']); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CONTENEDOR DEL EDITOR GRAPESJS -->
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header pb-0">
                    <h3 class="mb-0">Diseño visual de la plantilla: <span class="text-primary"><?php echo $plantilla->name; ?></span></h3>
                </div>
                <div class="card-body">
                    <div id="gjs" style="height:80vh; border:1px solid #aaa; background:#fff;"></div>
                    <button id="guardar-template" class="btn btn-primary mt-3">
                        GUARDAR CAMBIOS
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- GRAPESJS Y CONFIGURACIÓN -->
<link rel="stylesheet" href="https://unpkg.com/grapesjs/dist/css/grapes.min.css">
<script src="https://unpkg.com/grapesjs"></script>
<script src="https://unpkg.com/grapesjs-preset-webpage@1.0.3"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://unpkg.com/grapesjs-navbar"></script>
<script src="https://unpkg.com/grapesjs-tabs"></script>
<script src="https://unpkg.com/grapesjs-blocks-basic"></script>
<script src="https://unpkg.com/grapesjs-plugin-forms"></script>
<script src="https://unpkg.com/grapesjs-custom-code"></script>
<script src="https://unpkg.com/grapesjs-blocks-bootstrap4"></script>
<script src="https://unpkg.com/grapesjs-touch"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>





<script>
    window.plantilla_id_editor = <?php echo (int)$plantilla->id; ?>;
    console.log('ID PLANTILLA DESDE PHP:', window.plantilla_id_editor);
</script>
