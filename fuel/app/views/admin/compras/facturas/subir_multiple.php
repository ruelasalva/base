<!-- ========== ENCABEZADO Y BREADCRUMB ========== -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Facturas</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/compras', 'Compras'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/compras/facturas', 'Facturas'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Subir múltiples facturas
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========== PAGE CONTENT ========== -->
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col">
            <div class="card-wrapper">
                <!-- ========== FORMULARIO DE SUBIDA DE XMLs ========== -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Agregar archivos</h3>
                    </div>
                    <div class="card-body">
                        <!-- ========== MENSAJES FLASH ========== -->
                        <?php if (Session::get_flash('error')): ?>
                            <div class="alert alert-danger"><?php echo Session::get_flash('error'); ?></div>
                        <?php endif; ?>
                        <?php if (Session::get_flash('success')): ?>
                            <div class="alert alert-success"><?php echo Session::get_flash('success'); ?></div>
                        <?php endif; ?>

                        <!-- ========== FORMULARIO DE ARCHIVOS XML ========== -->
                        <form id="form-subir-xml" enctype="multipart/form-data" autocomplete="off">
                            <fieldset>
                                <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                        <legend class="mb-0 heading">Subir múltiples facturas (sólo XML)</legend>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group">
                                            <label class="form-control-label" for="facturas">
                                                Selecciona archivos XML (máx. 20MB c/u)
                                            </label>
                                            <div class="custom-file">
                                                <input type="file" id="facturas" name="facturas[]" class="custom-file-input" multiple accept=".xml">
                                                <label class="custom-file-label" for="facturas">Selecciona archivos XML</label>
                                            </div>
                                            <small class="form-text text-muted">
                                                Puedes seleccionar varios archivos XML (uno por factura). No se procesan PDFs aquí.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <div class="text-right">
                                <button id="btn-subir-xml" class="btn btn-primary" type="button">
                                    <i class="fas fa-upload"></i> Procesar XMLs
                                </button>
                            </div>
                        </form>

                        <!-- ========== RESUMEN DE VUE (OCULTO HASTA QUE HAY RESULTADO) ========== -->
                        <div id="app-facturas-import" style="display:none;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========== VARIABLE PARA URLBASE EN AJAX ========== -->
<div id="url-location" data-url="<?php echo Uri::base().'admin/compras/facturas/'; ?>"></div>

<!-- ========== SCRIPTS DE VUE Y COMPONENTE VUE ========== -->
<script src="<?= Asset::get_file('admin/facturas-proveedores-vue.js', 'js'); ?>"></script>
<!-- SweetAlert2 debe estar incluido globalmente -->

<!-- ========== AJAX PARA PROCESAR XMLs Y MONTAR VUE ========== -->
<script>
document.getElementById('btn-subir-xml').addEventListener('click', function(e) {
    e.preventDefault();
    let form = document.getElementById('form-subir-xml');
    let files = form.querySelector('input[type="file"]').files;
    if (files.length === 0) {
        Swal.fire('Atención', 'Selecciona al menos un archivo XML', 'warning');
        return;
    }
    let formData = new FormData(form);

    // OPCIÓN: Cambia a mensaje simple de progreso
    Swal.fire({
        title: 'Procesando archivos XML...',
        text: 'Esto puede tardar unos segundos.',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    const urlBase = document.getElementById('url-location').dataset.url;

    fetch(urlBase + 'ajax_parse_multiple', {
        method: 'POST',
        body: formData,
        headers: { 
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(resp => {
        if(resp.headers.get("content-type").indexOf("application/json") === -1) {
            return resp.text().then(texto => { throw "No es JSON: " + texto.substr(0, 100); });
        }
        return resp.json();
    })
    .then(data => {
        Swal.close();
        // Mostrar el resumen
        document.getElementById('app-facturas-import').style.display = '';
        if (typeof window.vueFacturasApp === 'undefined') {
            window.vueFacturasApp = new window.FacturasProveedoresImport({
                propsData: { resumen: data }
            }).$mount('#app-facturas-import');
        } else {
            window.vueFacturasApp.resumen = data;
        }
        // Notificación para usuario
        Swal.fire({
            icon: 'success',
            title: 'Carga finalizada',
            text: 'Los XML han sido procesados. Revisa el resumen abajo.',
            timer: 2500,
            showConfirmButton: false
        });
    })
    .catch(err => {
        Swal.close();
        Swal.fire('Error', 'Ocurrió un error al procesar los archivos: ' + err, 'error');
        console.error('Error AJAX/fetch:', err);
    });
});
</script>
