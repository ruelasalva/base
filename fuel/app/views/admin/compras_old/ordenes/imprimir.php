<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Orden de Compra <?= $order->code_order; ?></title>

<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
.header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
.table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
.table th, .table td { border: 1px solid #777; padding: 4px 6px; }
.table th { background: #f0f0f0; }
.section-title { font-size: 14px; margin: 15px 0 5px 0; font-weight: bold; }
.totales { width: 40%; float: right; margin-top: 20px; }
.totales td { padding: 6px; }
.firmas { margin-top: 60px; width: 100%; }
.firmas td { text-align: center; padding-top: 40px; }
.btn-bar { margin-bottom: 20px; }
button, a.btn { padding: 8px 14px; background: #0069d9; color: #fff; border: none; text-decoration: none; border-radius: 4px; }
button:hover, a.btn:hover { background: #0053b3; }
</style>

</head>
<body>

<?php if (!empty($vista_previa)): ?>
<div class="btn-bar">
    <button onclick="window.print()">Imprimir</button>
    <a class="btn" href="?pdf=1">Descargar PDF</a>
    <a class="btn" href="<?= Uri::create('admin/compras/ordenes/info/'.$order->id); ?>">Regresar</a>
</div>
<?php endif; ?>

<!-- ENCABEZADO -->
<div class="header">
    <h2>ORDEN DE COMPRA</h2>
    <h3><?= $order->code_order; ?></h3>
</div>

<!-- PROVEEDOR -->
<div class="section-title">Proveedor</div>
<table class="table">
    <tr>
        <td><strong>Nombre:</strong> <?= $proveedor->name; ?></td>
    </tr>
    <tr>
        <td><strong>Código SAP:</strong> <?= $proveedor->code_sap; ?></td>
    </tr>
    <tr>
        <td><strong>RFC:</strong> <?= $proveedor->rfc; ?></td>
    </tr>
</table>

<!-- INFORMACIÓN GENERAL -->
<div class="section-title">Información General</div>
<table class="table">
    <tr>
        <td><strong>Fecha creación:</strong> <?= date('d/m/Y', $order->date_order); ?></td>
        <td><strong>Moneda:</strong> <?= $order->currency ? $order->currency->code : ''; ?></td>
    </tr>
    <tr>
        <td colspan="2"><strong>Notas:</strong><br><?= nl2br($order->notes); ?></td>
    </tr>
</table>

<!-- PARTIDAS -->
<div class="section-title">Partidas</div>
<table class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Tipo</th>
            <th>Producto / Código</th>
            <th>Descripción</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th>Subtotal</th>
            <th>IVA</th>
            <th>Retención</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php $i=1; foreach ($detalles as $p): ?>
        <tr>
            <td><?= $i++; ?></td>
            <td><?= strtoupper($p['tipo']); ?></td>
            <td><?= $p['code_product']; ?></td>
            <td><?= $p['description']; ?></td>
            <td><?= number_format($p['quantity'], 2); ?></td>
            <td>$<?= number_format($p['unit_price'], 2); ?></td>
            <td>$<?= number_format($p['subtotal'], 2); ?></td>
            <td>$<?= number_format($p['iva'], 2); ?></td>
            <td>$<?= number_format($p['retencion'], 2); ?></td>
            <td><strong>$<?= number_format($p['total'], 2); ?></strong></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- TOTALES -->
<table class="totales">
    <tr><td><strong>Subtotal:</strong></td><td>$<?= number_format($subtotal_general, 2); ?></td></tr>
    <tr><td><strong>IVA:</strong></td><td>$<?= number_format($iva_general, 2); ?></td></tr>
    <tr><td><strong>Retención:</strong></td><td>$<?= number_format($ret_general, 2); ?></td></tr>
    <tr><td><strong>Total:</strong></td><td><strong>$<?= number_format($total_general, 2); ?></strong></td></tr>
</table>

<div style="clear: both;"></div>

<!-- FIRMAS -->
<table class="firmas">
    <tr>
        <td>__________________________<br>Solicitó</td>
        <td>__________________________<br>Autorizó</td>
    </tr>
</table>

</body>
</html>
