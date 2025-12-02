<div class="card">
    <div class="card-header bg-default text-white">
        <h3 class="mb-0">
            <i class="fa-brands fa-mercadolibre"></i> 
            <?php echo $title; ?>
        </h3>
    </div>

    <div class="card-body">

        <div class="form-group">
            <?php echo Form::label('Nombre interno', 'name', ['class' => 'form-control-label']); ?>
            <?php echo Form::input('name', $config->name ?? '', [
                'class' => 'form-control',
                'required' => true
            ]); ?>
        </div>

        <div class="form-group">
            <?php echo Form::label('Client ID', 'client_id', ['class' => 'form-control-label']); ?>
            <?php echo Form::input('client_id', $config->client_id ?? '', [
                'class' => 'form-control',
                'required' => true
            ]); ?>
        </div>

        <div class="form-group">
            <?php echo Form::label('Client Secret', 'client_secret', ['class' => 'form-control-label']); ?>
            <?php echo Form::input('client_secret', $config->client_secret ?? '', [
                'class' => 'form-control',
                'required' => true
            ]); ?>
        </div>

        <div class="form-group">
            <?php echo Form::label('Redirect URI', 'redirect_uri', ['class' => 'form-control-label']); ?>
            <?php echo Form::input('redirect_uri', $config->redirect_uri ?? '', [
                'class' => 'form-control',
                'required' => true
            ]); ?>
        </div>

        <div class="form-group">
            <?php echo Form::label('Correo de la cuenta', 'account_email', ['class' => 'form-control-label']); ?>
            <?php echo Form::input('account_email', $config->account_email ?? '', [
                'class' => 'form-control',
                'placeholder' => 'Se llenará tras conectar'
            ]); ?>
        </div>

        <div class="form-group">
            <?php echo Form::label('Modo', 'mode', ['class' => 'form-control-label']); ?>
            <?php echo Form::select('mode',
                $config->mode ?? 'production',
                ['production' => 'Production', 'sandbox' => 'Sandbox'],
                ['class' => 'form-control']
            ); ?>
        </div>

        <div class="text-right">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i> Guardar
            </button>
        </div>

    </div>
</div>
