<!-- EDITAR NOTA DE CRÉDITO (ADMIN) -->
<div class="card">
  <div class="card-header bg-warning text-white">
    <h4 class="mb-0">Editar Nota de Crédito</h4>
  </div>
  <div class="card-body">
    <?php echo Form::open(); ?>
      <div class="form-group">
        <?php echo Form::label('Observaciones','observations'); ?>
        <?php echo Form::textarea('observations',$nota->observations,['class'=>'form-control']); ?>
      </div>
      <div class="form-group">
        <?php echo Form::submit('submit','Actualizar',['class'=>'btn btn-primary']); ?>
      </div>
    <?php echo Form::close(); ?>
  </div>
</div>
