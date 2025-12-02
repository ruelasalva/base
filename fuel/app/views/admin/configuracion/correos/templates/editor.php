<!-- CONTENT -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Editar contenido de plantilla</h6>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt--6">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <?php echo Form::open(array('action' => 'admin/configuracion/correos/templates/guardar_editor', 'method' => 'post')); ?>
                    <?php echo Form::hidden('id', $id); ?>

                    <div class="form-group">
                        <label>Código</label>
                        <input type="text" class="form-control" value="<?php echo $code; ?>" disabled>
                    </div>

                    <div class="row">
                        <!-- Editor -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Contenido</label>
                                <textarea id="editor" name="content" rows="20" class="form-control"><?php echo htmlentities($content); ?></textarea>
                            </div>
                        </div>

                        <!-- Preview -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Vista previa</label>
                                <iframe id="preview" style="width:100%; height:500px; border:1px solid #ccc; border-radius:4px;"></iframe>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">Guardar</button>
                    <?php echo Html::anchor('admin/configuracion/correos/templates', 'Cancelar', array('class'=>'btn btn-secondary')); ?>
                    <?php echo Form::close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CodeMirror -->
<?= Asset::css('admin/codemirror/lib/codemirror.css') ?>
<?= Asset::js('admin/codemirror/lib/codemirror.js') ?>
<?= Asset::js('admin/codemirror/mode/xml/xml.js') ?>
<?= Asset::js('admin/codemirror/mode/javascript/javascript.js') ?>
<?= Asset::js('admin/codemirror/mode/css/css.js') ?>
<?= Asset::js('admin/codemirror/mode/htmlmixed/htmlmixed.js') ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var editor = CodeMirror.fromTextArea(document.getElementById("editor"), {
        mode: "htmlmixed",
        lineNumbers: true,
        theme: "default",
        tabSize: 2,
        lineWrapping: true
    });

    var previewFrame = document.getElementById("preview");
    var previewDoc = previewFrame.contentDocument || previewFrame.contentWindow.document;

    function updatePreview() {
        previewDoc.open();
        previewDoc.write(editor.getValue());
        previewDoc.close();
    }

    editor.on("change", updatePreview);
    updatePreview(); // primera carga
});
</script>
