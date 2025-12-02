<!-- CONTENT -->
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Consentimientos</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin/legal/consentimientos', 'Consentimientos'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Agregar</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <?php echo Html::anchor('admin/legal/consentimientos', '<i class="fa-solid fa-arrow-left"></i> Volver', ['class'=>'btn btn-sm btn-neutral']); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PAGE CONTENT -->
<div class="container-fluid mt--6" id="consent-app">
    <div class="row">
        <div class="col">
            <div class="card-wrapper">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <h3 class="mb-0"><i class="fa-solid fa-circle-plus text-success"></i> Nuevo Consentimiento</h3>
                    </div>
                    <div class="card-body">

                        <?php echo Form::open(['method'=>'post']); ?>

                        <!-- Usuario -->
                        <div class="form-group">
                            <?php echo Form::label('Usuario', 'user_id', ['class'=>'form-control-label']); ?>
                            <select name="user_id" id="user_id" v-model="user_id" class="form-control">
                                <option value="">-- Selecciona usuario --</option>
                                <option v-for="u in users" :key="u.id" :value="u.id">
                                    {{ u.username }} ({{ u.email }})
                                </option>
                            </select>
                            <small class="form-text text-muted">Selecciona el usuario relacionado.</small>
                        </div>

                        <!-- Documento Legal -->
                        <div class="form-group">
                            <?php echo Form::label('Documento Legal', 'document_id', ['class'=>'form-control-label']); ?>
                            <select name="document_id" id="document_id" v-model="document_id" class="form-control">
                                <option value="">-- Selecciona documento --</option>
                                <option v-for="d in documents" :key="d.id" :value="d.id">
                                    {{ d.title }} (Versión {{ d.version }})
                                </option>
                            </select>
                            <small class="form-text text-muted">Documento legal al que aplica el consentimiento.</small>
                        </div>

                        <!-- Shortcode -->
                        <div class="form-group">
                            <?php echo Form::label('Shortcode', 'shortcode', ['class'=>'form-control-label']); ?>
                            <?php echo Form::input('shortcode', Input::post('shortcode'), ['class'=>'form-control', 'placeholder'=>'Ej: aviso_clientes']); ?>
                        </div>

                        <!-- Estado -->
                        <div class="form-group">
                            <?php echo Form::label('Estado', 'accepted', ['class'=>'form-control-label']); ?>
                            <?php echo Form::select('accepted', Input::post('accepted', 0), [
                                0 => 'Aceptado',
                                1 => 'Rechazado'
                            ], ['class'=>'form-control']); ?>
                        </div>

                        <!-- Canal -->
                        <div class="form-group">
                            <?php echo Form::label('Canal', 'channel', ['class'=>'form-control-label']); ?>
                            <?php echo Form::select('channel', Input::post('channel', 'web'), [
                                'web'    => 'Web',
                                'app'    => 'App',
                                'fisico' => 'Físico',
                                'otro'   => 'Otro',
                            ], ['class'=>'form-control']); ?>
                        </div>

                        <!-- Extra -->
                        <div class="form-group">
                            <?php echo Form::label('Extra (JSON)', 'extra', ['class'=>'form-control-label']); ?>
                            <?php echo Form::textarea('extra', Input::post('extra'), [
                                'class'=>'form-control',
                                'rows'=>3,
                                'placeholder'=>'Ej: {"newsletter":1}'
                            ]); ?>
                        </div>

                        <!-- Botones -->
                        <div class="form-group">
                            <?php echo Form::submit('submit','Guardar Consentimiento',['class'=>'btn btn-primary']); ?>
                            <?php echo Html::anchor('admin/legal/consentimientos','Cancelar',['class'=>'btn btn-secondary']); ?>
                        </div>

                        <?php echo Form::close(); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPTS VUE Y DEPENDENCIAS -->
<!-- Asegúrate de que SweetAlert2, Axios y jQuery/Bootstrap JS estén cargados globalmente antes de este script -->
<script src="<?= Asset::get_file('admin/consentimientos-vue.js', 'js'); ?>"></script>

