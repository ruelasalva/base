<!-- ENCABEZADO -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Vista de plantilla visual</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/editordiseno', 'Plantillas visuales'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Ver
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/editordiseno', 'Regresar', ['class'=>'btn btn-sm btn-neutral']); ?>
                    <?php echo Html::anchor('admin/editordiseno/editar/'.$plantilla['id'], '<i class="fas fa-edit"></i> Editar', ['class'=>'btn btn-sm btn-primary']); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col-xl-10 col-lg-12 mx-auto">
            <div class="card shadow">

                <div class="card mb-4">
                <div class="card-header">
                    <strong><?php echo $plantilla->name; ?></strong>
                    <?php if($plantilla['updated_at']): ?>
                    <div class="small text-muted">Última actualización: <?php echo date('d/m/Y H:i', is_numeric($plantilla->updated_at) ? $plantilla->updated_at : strtotime($plantilla->updated_at)); ?></div>
                    <?php endif; ?>
                </div>
                <div class="card-body" style="background:#f4f6fa;">
                    <!-- Preview centrado -->
                    <div style="display:flex;justify-content:center;">
                        <iframe
                            srcdoc='
                                <html>
                                <head>
                                    <style>
                                    body { background: #f4f6fa; margin: 0; }
                                    .demo-wrapper {
                                        margin: 30px auto;
                                        background: #fff;
                                        max-width: 1200px;
                                        min-height: 600px;
                                        box-shadow: 0 2px 12px 0 rgba(0,0,0,.08);
                                        border-radius: 8px;
                                        overflow: auto;
                                        padding: 20px 30px;
                                    }
                                    <?php echo addslashes($css_preview); ?>
                                    </style>
                                </head>
                                <body>
                                    <div class="demo-wrapper">
                                    <?php echo addslashes($html_preview); ?>
                                    </div>
                                </body>
                                </html>'
                            style="width:100%; max-width:1200px; height:700px; border:0; border-radius:8px; background:#fff; box-shadow:0 2px 12px 0 rgba(0,0,0,.08);"
                            sandbox
                        ></iframe>
                    </div>
                </div>
                <div class="card-footer">
                    ID Plantilla: <?php echo $plantilla->id; ?>
                </div>
            </div>

            </div>
        </div>
    </div>
</div>
