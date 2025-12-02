<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización #<?php echo $quote->id; ?></title>
    <style>
        /* BASE GENERALES */
        body {
            font-family: 'Segoe UI', Verdana, sans-serif; /* Fuente más moderna */
            font-size: 12px; /* Ligeramente más pequeño para más contenido */
            margin: 25px; /* Margen uniforme */
            color: #333; /* Color de texto más suave */
            line-height: 1.5; /* Mayor legibilidad */
        }

        /* TABLAS GENERALES */
        .header-table, .info-table, .products-table, .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px; /* Espacio uniforme entre secciones */
        }

        /* CELDAS Y BORDES */
        .header-table td, .info-table td, .products-table td, .products-table th, .totals-table td {
            padding: 8px 10px; /* Mayor padding */
            border: 1px solid #e0e0e0; /* Bordes más suaves */
            vertical-align: top; /* Alineación superior por defecto */
        }

        /* ESTILOS DE ENCABEZADO DE TABLA / SECCIONES */
        .header-table .logo-td {
            background: #fff;
            border: none;
            text-align: center;
            padding: 5px; /* Menos padding para el logo si lo necesita */
        }
        .header-table .section-title, .info-table .section-title {
            background-color: #d90000; /* Rojo principal */
            color: #fff;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            padding: 5px 0;
            margin-bottom: 5px; /* Espacio después del título de sección */
            border-bottom: 2px solid #a70000; /* Borde inferior para destacar */
        }
        .info-table .section-title {
            background-color: #444; /* Gris oscuro para secciones info */
            border-bottom: 2px solid #222;
        }

        /* TABLA DE PRODUCTOS */
        .products-table th {
            background-color: #d90000; /* Rojo principal */
            color: #fff;
            text-align: left;
            font-size: 13px; /* Tamaño de fuente para encabezados de tabla */
            text-transform: uppercase; /* Mayúsculas */
            letter-spacing: 0.5px;
        }
        .products-table td {
            background-color: #fff;
            font-size: 12px;
        }
        .products-table tr:nth-child(even) {
            background-color: #f5f5f5; /* Un gris muy claro para filas pares */
        }

        /* TABLA DE TOTALES */
        .totals-table {
            width: 40%; /* Un poco más ancho */
            float: right;
            margin-top: 15px; /* Más espacio */
            border: 1px solid #ccc; /* Borde alrededor de la tabla de totales */
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1); /* Sombra suave */
        }
        .totals-table .label {
            text-align: right;
            font-weight: bold;
            padding-right: 15px; /* Más espacio a la derecha */
        }
        .totals-table td {
            border: none; /* Elimina bordes internos de totales */
            padding: 8px;
        }
        .totals-table tr:last-child td {
            font-size: 14px;
            font-weight: bold;
            background-color: #d90000; /* Fondo rojo para el total */
            color: #fff;
        }

        /* COMENTARIOS */
        .comments-section {
            margin-top: 25px;
            font-size: 12px;
            border-left: 4px solid #d90000; /* Barra lateral de color */
            padding-left: 10px;
            background-color: #fefefe; /* Fondo blanco sutil */
        }
        .comments-section strong {
            display: block; /* Para que el título esté en su propia línea */
            margin-bottom: 5px;
        }

        /* PIE DE PÁGINA (FIRMA Y PAGARÉ) */
        .footer {
            margin-top: 60px;
            padding: 20px 0;
            clear: both;
            font-size: 11px; /* Letra más pequeña para los detalles legales */
            color: #555;
        }
        .firma-line {
            border-bottom: 1px dotted #888; /* Línea punteada más suave */
            width: 250px; /* Ancho ajustable */
            margin: 10px 0;
        }
        .pagare {
            font-size: 11px;
            text-align: justify;
            margin-bottom: 30px;
            padding: 10px;
            border: 1px dashed #ccc; /* Borde punteado para el pagaré */
            background-color: #fcfcfc; /* Fondo muy claro */
        }
        .firma p {
            margin: 5px 0;
        }

        /* ========================================================= */
        /* OCULTAR BOTONES PARA IMPRESIÓN Y PARA DOMPDF */
        /* ========================================================= */
        .no-print {
            text-align: center;
            margin-top: 30px;
        }

        /* Esta media query sobrescribe 'display: none !important;' para el NAVEGADOR. */
        /* Es decir, en el navegador SÍ se mostrarán los botones. */
        /* Dompdf ignorará esta media query, por lo que mantendrá el 'display: none !important;' */
        @media screen {
            .no-print {
                display: block !important; /* Muestra los botones solo en pantalla */
            }
        }

        /* Estilos de botones (solo para la vista en navegador) */
        .no-print button {
            background-color: #d90000;
            color: #fff;
            border: none;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 4px;
            margin: 0 5px; /* Espacio entre botones */
            transition: background-color 0.3s ease;
        }
        .no-print button:hover {
            background-color: #a70000;
        }
        .no-print a.button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            margin: 0 5px;
            transition: background-color 0.3s ease;
        }
        .no-print a.button:hover {
            background-color: #0056b3;
        }

    </style>
</head>
<body>

<!-- ==== ENCABEZADO LOGO Y DATOS === -->
<table class="header-table" style="margin-bottom:20px;">
    <tr>
        <td class="logo-td" style="width:30%; vertical-align:middle;">
            <?php
                echo Asset::img('admin/logo.png', array(
                    'class' => 'navbar-brand-img',
                    'style' => 'max-height:60px; height:60px; width:auto; display:block; margin:auto;'
                ));
            ?>
        </td>
        <td style="width:42%; vertical-align:top;">
            <strong>DISTRIBUIDORA SAJOR</strong><br>
            <strong>RFC:</strong> DSA8412043Q2<br>
            <strong>Régimen Fiscal:</strong> 601 General de Ley Personas Morales<br>
            <strong>Tipo de Comprobante:</strong> Cotización<br>
            <strong>Lugar de Expedición:</strong> Guadalajara, Jalisco
        </td>
        <td style="width:28%; vertical-align:top;">
            <div class="section-title">COTIZACIÓN</div>
            <strong>Folio #:</strong> <?php echo $quote->id; ?><br>
            <strong>Fecha:</strong> <?php echo date('d/m/Y', $quote->created_at); ?><br>
            <strong>Válido hasta:</strong> <?php echo $quote->valid_date ? date('d/m/Y', $quote->valid_date) : '—'; ?><br>
            <strong>Moneda:</strong> MXN<br>
            <strong>Vendedor:</strong> <?php echo $quote->employee ? $quote->employee->name : '—'; ?>
        </td>
    </tr>
</table>

<!-- ==== CLIENTE Y ENTREGA ==== -->
<table class="info-table" style="margin-bottom:20px;">
    <tr>
        <td style="width:50%;">
            <div class="section-title" style="background-color:#444;">CLIENTE</div>
            <strong><?php echo $quote->partner->name; ?></strong><br>
            <strong>RFC:</strong> <?php echo $quote->partner->rfc ?? '—'; ?><br>
            <strong>Correo:</strong> <?php echo $quote->partner->email; ?><br>
            <strong>Contacto:</strong> <?php echo $quote->contact ? $quote->contact->name : '—'; ?><br>
            <strong>Código SAP:</strong> <?php echo $quote->partner->code_sap; ?>
        </td>
        <td style="width:50%;">
            <div class="section-title" style="background-color:#444;">ENTREGA</div>
            <strong>Dirección:</strong><br>
            <?php if ($quote->address): ?>
                <?php echo $quote->address->street . ' ' . $quote->address->number . ' ' . $quote->address->colony; ?><br>
                <?php echo $quote->address->zipcode . ' ' . $quote->address->city; ?><br>
            <?php else: ?>
                Cotización sin domicilio registrado.
            <?php endif; ?>
            <br>
            <strong>Forma de pago:</strong> <?php echo $quote->payment ? $quote->payment->token : '—'; ?><br>
            <strong>Referencia:</strong> <?php echo $quote->reference ?: '—'; ?>
        </td>
    </tr>
</table>

<?php
    // ===== DETERMINAR SI HAY DESCUENTOS O RETENCIONES =====
    $show_discount = false;
    $show_retention = false;
    foreach ($quote->products as $prod) {
        if (isset($prod->discount) && floatval($prod->discount) > 0) $show_discount = true;
        if (isset($prod->retention) && floatval($prod->retention) > 0) $show_retention = true;
    }
?>

<!-- ==== PRODUCTOS ==== -->
<table class="products-table" style="margin-bottom:20px;">
    <thead>
        <tr>
            <th>CANTIDAD</th>
            <th>CÓDIGO</th>
            <th>DESCRIPCIÓN</th>
            <th>IMAGEN</th>
            <th>PRECIO</th>
            <?php if ($show_discount): ?><th>DESCUENTO</th><?php endif; ?>
            <?php if ($show_retention): ?><th>RETENCIÓN</th><?php endif; ?>
            <th>VALOR UNITARIO</th>
            <th>IMPORTE</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($quote->products as $product): ?>
            <tr>
                <td><?php echo $product->quantity; ?></td>
                <td><?php echo $product->product->code; ?></td>
                <td><?php echo $product->product->name; ?></td>
                <td>
                    <?php
                        $img_name = $product->product->image;
                        $img_path = DOCROOT.'assets/uploads/thumb_'.$img_name;
                        if (!empty($img_name) && file_exists($img_path)) {
                            echo Html::img('assets/uploads/thumb_'.$img_name, array(
                                'alt' => $product->product->name,
                                'style' => 'max-width: 80px; height: auto; display: block; margin: 0 auto;'
                            ));
                        } else {
                            echo Html::img('assets/uploads/thumb_no_image.png', array(
                                'alt' => $product->product->name,
                                'style' => 'max-width: 80px; height: auto; display: block; margin: 0 auto;'
                            ));
                        }
                    ?>
                </td>
                <td>$<?php echo number_format($product->price, 2); ?></td>
                <?php if ($show_discount): ?>
                    <td><?php echo isset($product->discount) ? number_format($product->discount,2).'%' : '-'; ?></td>
                <?php endif; ?>
                <?php if ($show_retention): ?>
                    <td><?php echo isset($product->retention) ? number_format($product->retention,2).'%' : '-'; ?></td>
                <?php endif; ?>
                <td>$<?php echo number_format($product->price, 2); ?></td>
                <td>$<?php echo number_format($product->total, 2); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- ==== TOTALES ==== -->
<?php
    $subtotal = $quote->total;
    $iva = $subtotal * 0.16;
    $total = $subtotal + $iva;
?>
<table class="totals-table">
    <tr>
        <td class="label">Subtotal:</td>
        <td>$<?php echo number_format($subtotal , 2); ?></td>
    </tr>
    <tr>
        <td class="label">IVA (16%):</td>
        <td>$<?php echo number_format($iva, 2); ?></td>
    </tr>
    <tr>
        <td class="label">TOTAL:</td>
        <td><strong>$<?php echo number_format($total, 2); ?></strong></td>
    </tr>
</table>

<!-- ==== COMENTARIOS ==== -->
<?php if (!empty($quote->comments)): ?>
    <div style="margin-top:20px;font-size:13px;"><strong>Comentarios:</strong><br>
        <?php echo nl2br(htmlspecialchars($quote->comments)); ?>
    </div>
<?php endif; ?>

<!-- ==== FIRMA Y PAGARÉ ==== -->
<div class="footer">
    <div class="pagare">
        Esta cotización es una propuesta comercial sujeta a disponibilidad de producto y vigencia de precios.
        <strong>No representa una factura ni puede ser utilizada como comprobante fiscal.</strong><br><br>
        Cualquier modificación o cancelación posterior a la aceptación deberá ser notificada por escrito.
        Una vez confirmada la orden de compra, esta cotización no podrá ser cancelada sin autorización previa.
    </div>
    <div class="firma">
        <p><strong>Nombre y Firma de aceptación:</strong></p>
        <div class="firma-line"></div>
        <p>Nombre y firma / Fecha</p>
    </div>
</div>

<!-- ==== BOTÓN IMPRIMIR ==== -->
<?php
// AQUI ES DONDE USAMOS LA NUEVA VARIABLE $is_pdf_export
// Los botones SOLO se mostrarán si NO estamos generando un PDF (es decir, es la vista normal en el navegador).
if (!isset($is_pdf_export) || !$is_pdf_export):
?>
<div class="footer no-print">
    <button onclick="window.print()">Imprimir</button>
    <a href="<?php echo \Uri::create('admin/cotizaciones/descargar_pdf/' . $quote->id); ?>" class="button">Descargar PDF</a>
</div>
<?php endif; // Fin del if para $is_pdf_export ?>
</body>
</html>
