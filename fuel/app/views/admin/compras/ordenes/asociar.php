<div class="card shadow">
  <div class="card-header bg-primary text-white">
    <h3 class="mb-0">
      Asociar Facturas a la Orden 
      <span class="badge badge-light text-dark">#<?php echo $order->code_order; ?></span>
    </h3>
  </div>

  <div class="card-body">
    <?php
      // Calcular totales actuales y disponibles
      $facturas_asociadas = Model_Providers_Bill::query()
          ->where('order_id', $order->id)
          ->where('deleted', 0)
          ->get();

      $total_asociadas = 0;
      foreach ($facturas_asociadas as $f) {
          $total_asociadas += (float) $f->total;
      }

      $total_disponible = max(0, $order->total - $total_asociadas);
    ?>

    <!-- INFORMACIÓN DE TOTALES -->
    <div class="alert alert-info mb-4">
      <div class="row">
        <div class="col-md-4">
          <strong>Total de la orden:</strong><br>
          <span class="text-dark">$<?php echo number_format($order->total, 2); ?></span>
        </div>
        <div class="col-md-4">
          <strong>Facturas ya asociadas:</strong><br>
          <span class="text-dark">$<?php echo number_format($total_asociadas, 2); ?></span>
        </div>
        <div class="col-md-4">
          <strong>Disponible para asociar:</strong><br>
          <span class="font-weight-bold text-success">$<?php echo number_format($total_disponible, 2); ?></span>
        </div>
      </div>
    </div>

    <?php if (!empty($facturas_sin_orden)): ?>
      <?php echo Form::open(['method' => 'post', 'id' => 'form-asociar-facturas']); ?>

      <div class="table-responsive">
        <table class="table table-sm table-bordered align-items-center">
          <thead class="thead-light">
            <tr>
              <th width="5%"></th>
              <th>UUID</th>
              <th>Proveedor</th>
              <th class="text-right">Total</th>
              <th>Fecha de carga</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($facturas_sin_orden as $f): ?>
              <tr>
                <td class="text-center align-middle">
                  <?php echo Form::checkbox('facturas[]', $f->id, false, [
                      'class' => 'chk-factura',
                      'data-total' => $f->total
                  ]); ?>
                </td>
                <td><?php echo $f->uuid; ?></td>
                <td><?php echo $f->provider ? $f->provider->company_name : 'N/D'; ?></td>
                <td class="text-right">$<?php echo number_format($f->total, 2); ?></td>
                <td><?php echo date('d/m/Y', $f->created_at); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- SUMATORIA DINÁMICA -->
      <div class="mt-3">
        <div class="alert alert-secondary" id="alert-sumatoria">
          Total seleccionado: <strong id="total-seleccionado">$0.00</strong>
          <span class="ml-2 text-muted">(Debe ser menor o igual a $<?php echo number_format($total_disponible, 2); ?>)</span>
        </div>
      </div>

      <div class="text-right mt-3">
        <button type="submit" id="btn-asociar" class="btn btn-success" disabled>
          <i class="fas fa-link"></i> Asociar seleccionadas
        </button>
        <?php echo Html::anchor('admin/compras/ordenes/info/'.$order->id, 'Cancelar', ['class'=>'btn btn-secondary']); ?>
      </div>

      <?php echo Form::close(); ?>
    <?php else: ?>
      <div class="alert alert-info mb-0">
        <i class="fas fa-info-circle"></i> No hay facturas disponibles para asociar a este proveedor.
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- SCRIPTS -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const checkboxes = document.querySelectorAll('.chk-factura');
  const totalLabel = document.getElementById('total-seleccionado');
  const btnAsociar = document.getElementById('btn-asociar');
  const alertBox   = document.getElementById('alert-sumatoria');
  const totalDisponible = <?php echo $total_disponible; ?>;

  function actualizarTotal() {
    let total = 0;
    checkboxes.forEach(chk => {
      if (chk.checked) total += parseFloat(chk.dataset.total);
    });

    totalLabel.textContent = '$' + total.toLocaleString('es-MX', {minimumFractionDigits: 2});

    if (total > totalDisponible) {
      alertBox.classList.remove('alert-secondary', 'alert-success');
      alertBox.classList.add('alert-danger');
      alertBox.innerHTML = 
        '<strong>Atención:</strong> El total seleccionado ($' + 
        total.toLocaleString('es-MX', {minimumFractionDigits: 2}) + 
        ') supera el monto disponible para asociar ($' + 
        totalDisponible.toLocaleString('es-MX', {minimumFractionDigits: 2}) + ').';
      btnAsociar.disabled = true;
    } else if (total === 0) {
      alertBox.classList.remove('alert-danger', 'alert-success');
      alertBox.classList.add('alert-secondary');
      alertBox.innerHTML = 'Total seleccionado: <strong>$0.00</strong> ' +
        '<span class="ml-2 text-muted">(Debe ser menor o igual a $' + 
        totalDisponible.toLocaleString('es-MX', {minimumFractionDigits: 2}) + ')</span>';
      btnAsociar.disabled = true;
    } else {
      alertBox.classList.remove('alert-danger', 'alert-secondary');
      alertBox.classList.add('alert-success');
      alertBox.innerHTML = 
        'Total seleccionado: <strong>$' + total.toLocaleString('es-MX', {minimumFractionDigits: 2}) + '</strong> ' +
        '<span class="ml-2 text-muted">(Dentro del límite permitido)</span>';
      btnAsociar.disabled = false;
    }
  }

  checkboxes.forEach(chk => chk.addEventListener('change', actualizarTotal));

  // Confirmación con SweetAlert2 antes de enviar
  const form = document.getElementById('form-asociar-facturas');
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    Swal.fire({
      title: '¿Confirmar asociación?',
      html: "Se vincularán las facturas seleccionadas a la orden <b>#<?php echo $order->code_order; ?></b>.",
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sí, asociar',
      cancelButtonText: 'Cancelar',
      reverseButtons: true,
      customClass: {
        confirmButton: 'btn btn-success',
        cancelButton: 'btn btn-secondary ml-2'
      },
      buttonsStyling: false
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Procesando...',
          text: 'Asociando facturas, por favor espera.',
          allowOutsideClick: false,
          didOpen: () => Swal.showLoading()
        });
        form.submit();
      }
    });
  });
});
</script>
