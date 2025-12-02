<!-- ... Código HTML anterior ... -->
<?php echo Form::open('admin/crm/tickets/update', array('class' => 'form-horizontal')); ?>
    <div class="form-group">
        <?php echo Form::label('Estado', 'status', array('class' => 'control-label col-sm-2')); ?>
        <div class="col-sm-10">
            <?php
            $status_options = array(
                'abierto' => 'Abierto',
                'en_proceso' => 'En Proceso',
                'cerrado' => 'Cerrado',
            );
            echo Form::select('status', Input::post('status'), $status_options, array('class' => 'form-control'));
            ?>
        </div>
    </div>
    <div class="form-group">
        <?php echo Form::label('Fecha de Finalización', 'completion_date', array('class' => 'control-label col-sm-2')); ?>
        <div class="col-sm-10">
            <?php echo Form::input('completion_date', Input::post('completion_date'), array('class' => 'form-control', 'type' => 'date')); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?php echo Form::submit('submit', 'Actualizar Ticket', array('class' => 'btn btn-primary')); ?>
        </div>
    </div>
<?php echo Form::close(); ?>
<!-- ... Código HTML posterior ... -->
