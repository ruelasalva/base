<!-- ========================================================= -->
<!-- ENCABEZADO -->
<!-- ========================================================= -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">
                        <i class="fa-brands fa-mercadolibre"></i> Configuración ML del Producto
                    </h6>

                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <a href="<?php echo Uri::create('admin'); ?>"><i class="fas fa-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="<?php echo Uri::create('admin/plataforma/ml'); ?>">Mercado Libre</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="<?php echo Uri::create('admin/plataforma/ml/productos?config_id='.$config->id); ?>">
                                    Productos
                                </a>
                            </li>
                            <li class="breadcrumb-item active">Editar</li>
                        </ol>
                    </nav>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================= -->
<!-- CUERPO PRINCIPAL -->
<!-- ========================================================= -->
<div class="container-fluid mt--6">

    <!-- ========================================================= -->
    <!-- CARD PRINCIPAL DEL PRODUCTO -->
    <!-- ========================================================= -->
    <div class="card">
        <div class="card-header d-flex justify-content-between">

            <div>
                <h3 class="mb-0">
                    <i class="fas fa-box"></i> <?php echo $product->name; ?>
                </h3>
                <span class="text-muted">Código: <?php echo $product->code; ?></span>
            </div>

            <div>
                <button type="button" class="btn btn-info" id="btn-preview">
                    <i class="fa-solid fa-eye"></i> Ver Payload
                </button>

                <button type="button" class="btn btn-warning ml-2" id="btn-sync-ml-attrs">
                    <i class="fa-solid fa-rotate"></i> Sincronizar atributos ML
                </button>
            </div>

        </div>

        <div class="card-body">

            <?= Form::open(['method' => 'post']); ?>

            <div class="row">

                <!-- CATEGORÍA ML -->
                <div class="col-md-6 mb-3">
                    <?= Form::label('Categoría ML', 'ml_category_id', ['class' => 'form-control-label']); ?>

                    <div class="input-group">
                        <?= Form::input(
                            'ml_category_id',
                            $link->ml_category_id,
                            [
                                'class' => 'form-control',
                                'placeholder' => 'Ej. MLA1234',
                                'id' => 'ml_category_id'
                            ]
                        ); ?>

                        <div class="input-group-append">
                            <button type="button" class="btn btn-warning" id="btn-search-category">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <div id="category_info" class="text-muted mt-2"></div>
                </div>

                <!-- ESTADO ML -->
                <div class="col-md-6 mb-3">
                    <?= Form::label('Habilitado en ML', 'ml_enabled', ['class' => 'form-control-label']); ?>

                    <?= Form::select(
                        'ml_enabled',
                        $link->ml_enabled,
                        [
                            1 => 'Sí – Publicable',
                            0 => 'No – Deshabilitado'
                        ],
                        ['class' => 'form-control']
                    ); ?>
                </div>

                <!-- TÍTULO ML -->
                <div class="col-md-12 mb-3">
                    <?= Form::label('Título ML (override opcional)', 'ml_title_override', ['class' => 'form-control-label']); ?>
                    <?= Form::input('ml_title_override', $link->ml_title_override, ['class' => 'form-control', 'maxlength' => 60]); ?>
                </div>

                <!-- PLANTILLA DE DESCRIPCIÓN -->
                <div class="col-md-12 mb-3">

                    <?= Form::label('Plantilla de Descripción (ML)', 'ml_description_template_id', ['class' => 'form-control-label']); ?>

                    <div class="input-group">
                        <select id="ml_description_template_id"
                                name="ml_description_template_id"
                                class="form-control">
                            <option value="">Sin plantilla</option>

                            <?php
                            $plantillas = Model_Plataforma_Ml_Description_Template::query()
                                ->where('configuration_id', $config->id)
                                ->where('deleted', 0)
                                ->order_by('name', 'asc')
                                ->get();

                            foreach ($plantillas as $p):
                            ?>
                                <option value="<?= $p->id; ?>" <?= $link->ml_description_template_id == $p->id ? 'selected' : ''; ?>>
                                    <?= $p->name; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <!-- Nueva plantilla -->
                        <div class="input-group-append">
                            <a href="<?= Uri::create('admin/plataforma/ml/plantillas/agregar?config_id='.$config->id); ?>"
                               class="btn btn-secondary" title="Nueva plantilla">
                                <i class="fa-solid fa-file-circle-plus"></i>
                            </a>
                        </div>

                        <!-- Previsualizar -->
                        <div class="input-group-append">
                            <button type="button" id="btn-preview-plantilla" class="btn btn-info" title="Previsualizar plantilla">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>

                        <!-- Aplicar -->
                        <div class="input-group-append">
                            <button type="button" id="btn-aplicar-plantilla" class="btn btn-success" title="Aplicar plantilla">
                                <i class="fa-solid fa-download"></i>
                            </button>
                        </div>
                    </div>

                    <small class="text-muted">
                        Puedes crear, previsualizar y aplicar plantillas de descripción ML.
                    </small>
                </div>

                <!-- PRECIO -->
                <div class="col-md-6 mb-3">
                    <?= Form::label('Precio Override (opcional)', 'ml_price_override', ['class' => 'form-control-label']); ?>
                    <?= Form::input('ml_price_override', $link->ml_price_override, ['class' => 'form-control', 'placeholder' => 'Ej. 150.00']); ?>
                </div>

                <!-- STOCK -->
                <div class="col-md-6 mb-3">
                    <?= Form::label('Stock Override (opcional)', 'ml_stock_override', ['class' => 'form-control-label']); ?>
                    <?= Form::input('ml_stock_override', $link->ml_stock_override, ['class' => 'form-control', 'placeholder' => 'Ej. 30']); ?>
                </div>

                <!-- TIPO PUBLICACIÓN -->
                <div class="col-md-6 mb-3">
                    <?= Form::label('Tipo de Publicación', 'ml_listing_type_override', ['class' => 'form-control-label']); ?>
                    <?= Form::select(
                        'ml_listing_type_override',
                        $link->ml_listing_type_override,
                        [
                            '' => 'Predeterminado ML',
                            'gold_special' => 'Oro Premium',
                            'gold_pro' => 'Oro Pro'
                        ],
                        ['class' => 'form-control']
                    ); ?>
                </div>

                <!-- ESTATUS ML -->
                <div class="col-md-6 mb-3">
                    <?= Form::label('Estado de publicación', 'ml_status_override', ['class' => 'form-control-label']); ?>
                    <?= Form::select(
                        'ml_status_override',
                        $link->ml_status_override,
                        [
                            '' => 'Predeterminado',
                            'active' => 'Activo',
                            'paused' => 'Pausado',
                            'closed' => 'Cerrado'
                        ],
                        ['class' => 'form-control']
                    ); ?>
                </div>

            </div>

            <!-- BOTONES -->
            <div class="mt-4 text-right">
                <?= Html::anchor(
                    'admin/plataforma/ml/productos?config_id='.$config->id,
                    'Cancelar',
                    ['class' => 'btn btn-secondary']
                ); ?>

                <button class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </div>

            <?= Form::close(); ?>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- MODAL CATEGORÍAS ML -->
    <!-- ========================================================= -->
    <div class="modal fade" id="modalCategorySearch" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-search"></i> Buscar Categorías</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                </div>

                <div class="modal-body">
                    <input type="text" id="cat-search-query" class="form-control"
                           placeholder="Ej. papel china, colores, folders">
                    <button class="btn btn-primary mt-3" id="btn-cat-search">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                    <div id="cat-search-results" class="mt-4"></div>
                </div>

            </div>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- MODAL PAYLOAD -->
    <!-- ========================================================= -->
    <div class="modal fade" id="modalPayload" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="fa-solid fa-code"></i> Payload hacia Mercado Libre</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>×</span></button>
                </div>

                <div class="modal-body">
                    <pre id="payload-json" class="payload-pre">Cargando...</pre>

                    <button class="btn btn-primary btn-sm mt-3" id="btn-copy-json">
                        <i class="fa-solid fa-copy"></i> Copiar JSON
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- MODAL PREVISUALIZACIÓN PLANTILLA -->
    <!-- ========================================================= -->
    <div class="modal fade" id="modalPreviewTemplate" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fa-solid fa-eye"></i> Previsualización de Plantilla ML
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>×</span>
                    </button>
                </div>

                <div class="modal-body">
                    <iframe id="preview-template-iframe" class="ml-preview-iframe"></iframe>
                </div>

            </div>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- BLOQUE: IMÁGENES DEL SISTEMA (CON PLACEHOLDER) -->
    <!-- ========================================================= -->
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="fa-solid fa-image"></i> Imágenes del Producto (Sistema)</h3>
            <small class="text-muted">Estas imágenes ya existen en el catálogo.</small>
        </div>

        <div class="card-body">

            <?php
            $folder      = DOCROOT.'assets/uploads/';
            $baseUrl     = Uri::base(false).'assets/uploads/';
            $placeholder = Uri::base(false).'assets/img/no-image-ml.png';

            $hasMainImage = ($product->image && file_exists($folder.$product->image));
            $mainImageUrl = $hasMainImage ? $baseUrl.$product->image : $placeholder;

            $gallery = Model_Products_Image::get_product_gallery($product->id);
            ?>

            <!-- IMAGEN PRINCIPAL -->
            <div class="mb-3 d-flex align-items-center">
                <img src="<?= $mainImageUrl; ?>" class="ml-img-main">
                <?php if ($hasMainImage): ?>
                    <button class="btn btn-primary btn-sm ml-3"
                            onclick="ML_IMAGES_APP.addFromLocal('<?= $mainImageUrl; ?>')">
                        <i class="fa-solid fa-plus"></i> Usar en ML
                    </button>
                <?php else: ?>
                    <span class="text-muted ml-3">Sin imagen principal en sistema.</span>
                <?php endif; ?>
            </div>

            <!-- GALERÍA -->
            <?php if (!empty($gallery)): ?>
                <h5 class="mt-4 mb-2">Galería del Producto</h5>
                <div class="row">
                    <?php foreach ($gallery as $img): ?>
                        <?php
                        $fileExists = ($img->image && file_exists($folder.$img->image));
                        $url        = $fileExists ? $baseUrl.$img->image : $placeholder;
                        ?>
                        <div class="col-md-2 mb-3 text-center">
                            <img src="<?= $url; ?>" class="ml-img-gallery">
                            <?php if ($fileExists): ?>
                                <button class="btn btn-primary btn-sm mt-2"
                                        onclick="ML_IMAGES_APP.addFromLocal('<?= $url; ?>')">
                                    Usar en ML
                                </button>
                            <?php else: ?>
                                <span class="badge badge-secondary mt-2">Archivo no encontrado</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-secondary">No hay imágenes adicionales en el sistema.</div>
            <?php endif; ?>

        </div>
    </div>

    <!-- ========================================================= -->
    <!-- BLOQUE: IMÁGENES ML – ADMINISTRACIÓN -->
    <!-- ========================================================= -->
    <div id="ml-images-app" class="card mt-4">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="fa-brands fa-mercadolibre"></i> Imágenes Mercado Libre</h3>
            <small class="text-muted">Administra orden, principal y carga desde sistema o archivo.</small>
        </div>

        <div class="card-body">

            <div v-if="!mlProductId" class="alert alert-warning mb-0">
                Primero guarda la configuración ML del producto.
            </div>

            <div v-else>

                <!-- Subir archivo desde equipo -->
                <h5 class="text-uppercase text-muted mb-2">Agregar imagen desde archivo</h5>
                <div class="input-group mb-3">
                    <input type="file" class="form-control" @change="uploadImage">
                </div>

                <!-- Agregar desde URL -->
                <h5 class="text-uppercase text-muted mb-2">Agregar desde URL del sistema</h5>
                <div class="input-group mb-4">
                    <input type="text" class="form-control" v-model="newUrl" placeholder="URL de imagen">
                    <div class="input-group-append">
                        <button class="btn btn-primary" @click="addImage">
                            <i class="fa-solid fa-plus"></i> Agregar
                        </button>
                    </div>
                </div>

                <!-- Lista de imágenes -->
                <div v-if="images.length === 0" class="alert alert-secondary">
                    No hay imágenes asignadas al producto ML.
                </div>

                <div v-else class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:40px;"></th>
                                <th>Preview</th>
                                <th>URL</th>
                                <th>Principal</th>
                                <th>Orden</th>
                                <th style="width:70px;">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(img, index) in images" :key="img.id"
                                draggable="true"
                                @dragstart="onDragStart(index)"
                                @dragover.prevent
                                @drop="onDrop(index)">

                                <td class="text-center">
                                    <i class="fa-solid fa-grip-vertical text-muted"></i>
                                </td>

                                <td>
                                    <img :src="img.url"
                                         class="ml-img-preview">
                                </td>

                                <td>{{ img.url }}</td>

                                <td>
                                    <input type="radio"
                                           name="ml_primary_image"
                                           :value="img.id"
                                           v-model="primaryId"
                                           @change="setPrimary(img.id)">
                                </td>

                                <td>
                                    <input type="number" class="form-control form-control-sm"
                                           v-model.number="img.sort_order"
                                           @change="updateOrder(img)">
                                </td>

                                <td class="text-center">
                                    <button class="btn btn-sm btn-danger"
                                            @click="deleteImage(img)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>

                            </tr>
                        </tbody>

                    </table>
                </div>

            </div>

        </div>

    </div>

    <!-- ========================================================= -->
    <!-- BLOQUE DE ATRIBUTOS ML (VUE) -->
    <!-- ========================================================= -->
    <div id="url-location" data-url="<?= Uri::create('admin/plataforma/ml/'); ?>"></div>

    <div id="ml-attributes-app" class="card mt-4">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="fa-brands fa-mercadolibre"></i> Atributos Mercado Libre</h3>
            <span class="text-sm text-muted">
                Categoría ML: <?= $link->ml_category_id ?: 'Sin asignar'; ?>
            </span>
        </div>

        <div class="card-body">

            <div v-if="!category_id" class="alert alert-warning">
                Asigna y guarda una categoría ML primero.
            </div>

            <div v-else-if="loading" class="alert alert-info">
                Cargando atributos…
            </div>

            <div v-else>

                <div class="mb-3">
                    <span class="ml-summary-badge ml-summary-required">
                        Requeridos: {{ requiredCompleted }}/{{ requiredTotal }}
                    </span>
                    <span class="ml-summary-badge ml-summary-optional">
                        Opcionales: {{ optionalCount }}
                    </span>
                </div>

                <div v-if="attributes.length === 0" class="alert alert-secondary">
                    No hay atributos configurados para esta categoría.
                </div>

                <div v-for="attr in attributes"
                     :key="attr.category_attribute_id"
                     class="ml-attr-block"
                     :class="{
                         'ml-attr-required': attr.is_required == 1,
                         'ml-attr-catalog': attr.is_catalog_required == 1,
                         'ml-attr-missing': attr.is_required == 1 && (!values[attr.category_attribute_id])
                     }">

                    <label class="form-control-label"
                           :class="{ 'ml-required-asterisk': attr.is_required == 1 }">

                        {{ attr.name }}

                        <span v-if="attr.is_required == 1" class="ml-badge ml-badge-required">Obligatorio</span>
                        <span v-if="attr.is_catalog_required == 1" class="ml-badge ml-badge-catalog">Catálogo</span>
                        <span v-if="attr.is_variation == 1" class="ml-badge ml-badge-variation">Variación</span>
                    </label>

                    <div class="mt-2">

                        <input v-if="attr.value_type === 'string' || attr.value_type === 'number'"
                               type="text"
                               class="form-control"
                               v-model="values[attr.category_attribute_id]"
                               @change="onChange(attr)">

                        <select v-else-if="attr.value_type === 'boolean'"
                                class="form-control"
                                v-model="values[attr.category_attribute_id]"
                                @change="onChange(attr)">
                            <option value="">Seleccione…</option>
                            <option value="true">Sí</option>
                            <option value="false">No</option>
                        </select>

                        <select v-else-if="attr.value_type === 'list' && attr.values"
                                class="form-control"
                                v-model="values[attr.category_attribute_id]"
                                @change="onChange(attr)">
                            <option value="">Seleccione…</option>

                            <option v-for="v in attr.values" :value="v.ml_value_id">
                                {{ v.name }}
                            </option>
                        </select>

                        <input v-else
                               type="text"
                               class="form-control"
                               v-model="values[attr.category_attribute_id]"
                               @change="onChange(attr)">

                    </div>

                </div>

                <div v-if="errors.length" class="alert alert-danger mt-3">
                    <ul>
                        <li v-for="(err, i) in errors" :key="i">{{ err }}</li>
                    </ul>
                </div>

            </div>
        </div>

    </div>

</div> <!-- /container-fluid -->

<!-- ========================================================= -->
<!-- CONFIG GLOBAL PARA VUE -->
<!-- ========================================================= -->
<script>
window.ML_ATTR_CONFIG = {
    baseUrl   : '<?= Uri::base(true); ?>',
    configId  : <?= (int) $config->id; ?>,
    productId : <?= (int) $product->id; ?>,
    categoryId: '<?= $link->ml_category_id ?: ''; ?>'
};
</script>

<!-- ========================================================= -->
<!-- SCRIPTS DE MODAL, CATEGORÍAS, PAYLOAD Y PLANTILLAS -->
<!-- ========================================================= -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Abrir modal categorías
    var btnSearchCat = document.getElementById('btn-search-category');
    if (btnSearchCat) {
        btnSearchCat.addEventListener('click', function () {
            $('#modalCategorySearch').modal('show');
        });
    }

    // Buscar categorías ML
    var btnCatSearch = document.getElementById('btn-cat-search');
    if (btnCatSearch) {
        btnCatSearch.addEventListener('click', function () {

            let q = document.getElementById('cat-search-query').value.trim();
            if (!q) return;

            let resultsDiv = document.getElementById('cat-search-results');
            resultsDiv.innerHTML = '<div class="alert alert-info">Buscando categorías…</div>';

            fetch('https://api.mercadolibre.com/sites/MLM/domain_discovery/search?q=' +
                    encodeURIComponent(q))
                .then(r => r.json())
                .then(res => {

                    resultsDiv.innerHTML = '';

                    if (!res || res.length === 0) {
                        resultsDiv.innerHTML =
                            '<div class="alert alert-warning">No se encontraron categorías.</div>';
                        return;
                    }

                    res.forEach(item => {
                        resultsDiv.innerHTML += `
                            <div class="border p-2 mb-2">
                                <strong>${item.domain_name}</strong><br>
                                <small class="text-muted">${item.category_name}</small><br>
                                <button class="btn btn-sm btn-success mt-2"
                                    onclick="selectCategory('${item.category_id}',
                                                            '${item.category_name}')">
                                    Seleccionar
                                </button>
                            </div>`;
                    });

                });

        });
    }

    // Preview Payload
    var btnPreviewPayload = document.getElementById('btn-preview');
    if (btnPreviewPayload) {
        btnPreviewPayload.addEventListener('click', function () {

            const url = '<?= Uri::create("admin/plataforma/ml/productos/preview/".$link->id); ?>';

            let pre = document.getElementById('payload-json');
            pre.innerHTML = 'Cargando...';

            fetch(url)
                .then(r => r.json())
                .then(data => {
                    pre.textContent = JSON.stringify(data, null, 4);
                    $('#modalPayload').modal('show');
                })
                .catch(err => {
                    pre.textContent = 'Error: ' + err;
                    $('#modalPayload').modal('show');
                });
        });
    }

    // Copiar JSON
    var btnCopyJson = document.getElementById('btn-copy-json');
    if (btnCopyJson) {
        btnCopyJson.addEventListener('click', function () {
            let text = document.getElementById('payload-json').innerText;

            navigator.clipboard.writeText(text)
                .then(() => alert('JSON copiado'))
                .catch(() => alert('Error al copiar'));
        });
    }

});
    
// Seleccionar categoría
function selectCategory(id, name) {
    document.getElementById('ml_category_id').value = id;
    document.getElementById('category_info').innerHTML =
        '<span class="text-success"><i class="fas fa-check"></i> ' + name + '</span>';

    $('#modalCategorySearch').modal('hide');
}
</script>

<!-- ========================================================= -->
<!-- SCRIPT – Previsualizar y Aplicar Plantilla ML -->
<!-- ========================================================= -->
<script>
document.addEventListener("DOMContentLoaded", function () {

    const selectTpl  = document.getElementById("ml_description_template_id");
    const btnPreview = document.getElementById("btn-preview-plantilla");
    const btnApply   = document.getElementById("btn-aplicar-plantilla");

    if (!selectTpl) {
        return;
    }

    // 1. PREVISUALIZAR PLANTILLA
    if (btnPreview) {
        btnPreview.addEventListener("click", function () {

            let tplId = selectTpl.value;

            if (!tplId) {
                Swal.fire('Sin plantilla', 'Selecciona una plantilla primero', 'warning');
                return;
            }

            Swal.fire({
                title: 'Cargando plantilla…',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            axios.post(
                '<?= Uri::create("admin/ajax/get_ml_template_html"); ?>',
                {
                    template_id: tplId,
                    access_id: window.access_id,
                    access_token: window.access_token
                },
                {
                    headers: { "X-Requested-With": "XMLHttpRequest" }
                }
            )
            .then(res => {
                if (!res.data.success) {
                    Swal.fire('Error', res.data.msg, 'error');
                    return;
                }

                let iframe  = document.getElementById("preview-template-iframe");
                let content = res.data.html || '<p>Plantilla vacía</p>';

                iframe.contentDocument.open();
                iframe.contentDocument.write(content);
                iframe.contentDocument.close();

                Swal.close();
                $("#modalPreviewTemplate").modal("show");
            })
            .catch(err => {
                console.error("[TPL][PREVIEW ERROR]", err);
                Swal.fire('Error', 'No se pudo cargar la plantilla', 'error');
            });

        });
    }

    // 2. APLICAR PLANTILLA A CKEDITOR
    if (btnApply) {
        btnApply.addEventListener("click", function () {

            let tplId = selectTpl.value;

            if (!tplId) {
                Swal.fire('Sin plantilla', 'Selecciona una plantilla primero', 'warning');
                return;
            }

            Swal.fire({
                title: 'Aplicando plantilla…',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            axios.post(
                '<?= Uri::create("admin/ajax/get_ml_template_html"); ?>',
                {
                    template_id: tplId,
                    access_id: window.access_id,
                    access_token: window.access_token
                },
                {
                    headers: { "X-Requested-With": "XMLHttpRequest" }
                }
            )
            .then(res => {
                if (!res.data.success) {
                    Swal.fire('Error', res.data.msg, 'error');
                    return;
                }

                if (window.editorDescripcion) {
                    window.editorDescripcion.setData(res.data.html || '');
                } else {
                    console.warn("[TPL] CKEditor no está inicializado");
                }

                Swal.fire('Plantilla aplicada', '', 'success');
            })
            .catch(err => {
                console.error("[TPL][APPLY ERROR]", err);
                Swal.fire('Error', 'No se pudo aplicar la plantilla', 'error');
            });

        });
    }

});
</script>

<!-- ========================================================= -->
<!-- JS VUE (ATRIBUTOS E IMÁGENES ML) -->
<!-- ========================================================= -->
<?= Asset::js('admin/plataformas/ml/ml-attributes.js'); ?>
<?= Asset::js('admin/plataformas/ml/ml-images.js'); ?>
