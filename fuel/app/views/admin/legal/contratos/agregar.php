<!-- HEADER -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white mb-0">Agregar Contrato</h6>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?= Html::anchor('admin/legal/contratos', '<i class="fas fa-arrow-left"></i> Volver', ['class' => 'btn btn-secondary']); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CONTENIDO -->
<div class="container-fluid mt--6">

    <?php if (Session::get_flash('error')): ?>
        <div class="alert alert-danger"><?= Session::get_flash('error'); ?></div>
    <?php endif; ?>

    <?php if (Session::get_flash('success')): ?>
        <div class="alert alert-success"><?= Session::get_flash('success'); ?></div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-header">
            <h3 class="mb-0"><i class="fas fa-file-signature text-success"></i> Nuevo Contrato</h3>
        </div>
        <div class="card-body">

            <?= Form::open(['enctype' => 'multipart/form-data']); ?>

            <!-- ===========================
                 INFORMACIÓN GENERAL
            ============================ -->
            <fieldset class="mb-4">
                <legend class="heading mb-3"><i class="fas fa-info-circle"></i> Información General</legend>
                <div class="form-row">

                    <div class="col-md-6 mb-3">
                        <?= Form::label('Título', 'title'); ?>
                        <?= Form::input('title', '', ['class' => 'form-control', 'required']); ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <?= Form::label('Código', 'code'); ?>
                        <?= Form::input('code', '', ['class' => 'form-control', 'placeholder' => 'Opcional']); ?>
                    </div>

                    <div class="col-md-4 mb-3">
                        <?= Form::label('Categoría', 'category'); ?>
                        <?= Form::select('category', '', [
                            '' => 'Selecciona...',
                            'provider' => 'Proveedor',
                            'employee' => 'Empleado',
                            'customer' => 'Cliente',
                            'external' => 'Externo'
                        ], ['class' => 'form-control', 'id' => 'category', 'required']); ?>
                    </div>

                    <div class="col-md-4 mb-3">
                        <?= Form::label('Usuario asignado', 'user_id'); ?>
                        <select name="user_id" id="user_id" class="form-control" required>
                            <option value="">Selecciona usuario</option>
                            <?php foreach ($usuarios as $u): ?>
                                <option value="<?= $u->id; ?>" 
                                    data-group="<?= $u->group; ?>"
                                    data-username="<?= $u->username; ?>">
                                    <?= $u->username; ?> (<?= $u->email; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <?= Form::label('Tipo de Documento', 'document_type_id'); ?>
                        <select name="document_type_id" class="form-control">
                            <option value="">Selecciona</option>
                            <?php foreach ($tipos as $t): ?>
                                <option value="<?= $t->id; ?>"><?= $t->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>
            </fieldset>

            <!-- ===========================
                 FECHAS Y ARCHIVO
            ============================ -->
            <fieldset class="mb-4">
                <legend class="heading mb-3"><i class="fas fa-calendar-alt"></i> Fechas y Archivo</legend>
                <div class="form-row">

                    <div class="col-md-6 mb-3">
                        <?= Form::label('Fecha Inicio', 'start_date'); ?>
                        <?= Form::input('start_date', '', ['type' => 'date', 'class' => 'form-control']); ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <?= Form::label('Fecha Fin', 'end_date'); ?>
                        <?= Form::input('end_date', '', ['type' => 'date', 'class' => 'form-control']); ?>
                    </div>

                    <div class="col-md-12 mb-3">
                        <?= Form::label('Archivo PDF', 'contract_file'); ?>
                        <div class="custom-file">
                            <?= Form::file('contract_file', ['class' => 'custom-file-input', 'id' => 'contract_file']); ?>
                            <label class="custom-file-label" for="contract_file">Seleccionar archivo PDF...</label>
                        </div>
                        <small class="text-muted">Solo formato PDF (máx. 10 MB).</small>
                    </div>

                </div>
            </fieldset>

            <!-- ===========================
                 CONTENIDO DEL CONTRATO
            ============================ -->
            <fieldset class="mb-4">
                <legend class="heading mb-3"><i class="fas fa-align-left"></i> Contenido del Contrato</legend>
                <div class="form-group">
                    <?= Form::textarea('description', '', [
                        'id' => 'content',
                        'class' => 'form-control',
                        'rows' => 10,
                        'placeholder' => 'Escribe o pega aquí el texto del contrato...'
                    ]); ?>
                    <small class="text-muted">Puedes redactar el contrato directamente o pegarlo desde Word. Se guardará el texto editable además del PDF.</small>
                </div>
            </fieldset>

            <!-- ===========================
                 OPCIONES ADICIONALES
            ============================ -->
            <fieldset class="mb-4">
                <legend class="heading mb-3"><i class="fas fa-sliders-h"></i> Opciones Adicionales</legend>
                <div class="form-row">

                    <div class="col-md-6 mb-3">
                        <?= Form::label('Estado', 'status'); ?>
                        <?= Form::select('status', '0', [
                            '0' => 'Borrador',
                            '1' => 'Activo',
                            '2' => 'Vencido',
                            '3' => 'Cancelado'
                        ], ['class' => 'form-control']); ?>
                    </div>

                    <div class="col-md-6 mb-3 d-flex align-items-center">
                        <div class="form-check mt-4">
                            <input type="checkbox" name="is_global" id="is_global" value="1" class="form-check-input">
                            <label for="is_global" class="form-check-label">
                                Contrato global (aplica a todos los usuarios de la categoría)
                            </label>
                        </div>
                    </div>
                </div>
            </fieldset>

            <!-- BOTÓN GUARDAR -->
            <div class="text-right">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Contrato
                </button>
            </div>

            <?= Form::close(); ?>
        </div>
    </div>
</div>

<!-- ===========================
     SCRIPTS
============================ -->
<script>
document.addEventListener('DOMContentLoaded', function() {

    // === Mostrar nombre del archivo seleccionado ===
    const fileInput = document.getElementById('contract_file');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const fileName = e.target.files.length ? e.target.files[0].name : 'Seleccionar archivo PDF...';
            e.target.nextElementSibling.textContent = fileName;
        });
    }

    // === Filtrar usuarios según categoría ===
    const categorySelect = document.getElementById('category');
    const userSelect = document.getElementById('user_id');
    const allUsers = Array.from(userSelect.options);

    categorySelect.addEventListener('change', function() {
        const cat = this.value;
        userSelect.innerHTML = '<option value="">Selecciona usuario</option>';
        allUsers.forEach(opt => {
            const group = opt.dataset.group;
            if (!cat || groupMatchesCategory(cat, group)) {
                userSelect.appendChild(opt.cloneNode(true));
            }
        });
    });

    function groupMatchesCategory(cat, group) {
        const map = {
            'provider': [10],
            'employee': [20, 25, 50, 100],
            'customer': [15],
            'external': [0]
        };
        return map[cat] ? map[cat].includes(parseInt(group)) : true;
    }

    // === Activar CKEditor en descripción ===
    const contentField = document.querySelector('#content');
    if (contentField) {
        ClassicEditor
            .create(contentField, {
                language: 'es',
                toolbar: {
                    items: [
                        'heading', '|',
                        'fontFamily', 'fontSize', 'fontColor', 'fontBackgroundColor', '|',
                        'bold', 'italic', 'underline', '|',
                        'alignment', '|',
                        'link', 'bulletedList', 'numberedList', '|',
                        'blockQuote', 'insertTable', 'mediaEmbed', '|',
                        'undo', 'redo'
                    ]
                }
            })
            .then(editor => {
                window.contractEditor = editor;
            })
            .catch(error => console.error('Error al iniciar CKEditor:', error));
    }
});
</script>
