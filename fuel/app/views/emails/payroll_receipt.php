<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Nómina Disponible</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background: #ffffff;
            padding: 30px 20px;
            border: 1px solid #e0e0e0;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .info-box h3 {
            margin-top: 0;
            color: #667eea;
        }
        .amount {
            font-size: 32px;
            font-weight: bold;
            color: #28a745;
            text-align: center;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        .button:hover {
            background: #5568d3;
        }
        .details {
            margin: 20px 0;
        }
        .details table {
            width: 100%;
            border-collapse: collapse;
        }
        .details td {
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
        }
        .details td:first-child {
            font-weight: bold;
            width: 40%;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-radius: 0 0 10px 10px;
        }
        .alert {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 Tu Recibo de Nómina está Disponible</h1>
    </div>

    <div class="content">
        <p>Hola <strong><?php echo htmlspecialchars($employee_name, ENT_QUOTES, 'UTF-8'); ?></strong>,</p>
        
        <p>Te informamos que tu recibo de nómina correspondiente al período <strong><?php echo htmlspecialchars($period_name, ENT_QUOTES, 'UTF-8'); ?></strong> ya está disponible para su consulta.</p>

        <div class="info-box">
            <h3>📋 Detalles del Pago</h3>
            <div class="details">
                <table>
                    <tr>
                        <td>Período:</td>
                        <td><?php echo htmlspecialchars($period_name, ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <tr>
                        <td>Fecha de Pago:</td>
                        <td><?php echo $payment_date; ?></td>
                    </tr>
                    <tr>
                        <td>Código de Empleado:</td>
                        <td><?php echo htmlspecialchars($receipt->employee_code, ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <tr>
                        <td>Días Trabajados:</td>
                        <td><?php echo number_format($receipt->worked_days, 1); ?> días</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="amount">
            💰 $<?php echo $net_payment; ?>
            <div style="font-size: 14px; color: #6c757d; font-weight: normal; margin-top: 5px;">
                Neto a Pagar
            </div>
        </div>

        <div class="info-box">
            <h3>📊 Resumen</h3>
            <div class="details">
                <table>
                    <tr>
                        <td>Total Percepciones:</td>
                        <td style="color: #28a745;">$<?php echo number_format($receipt->total_perceptions, 2); ?></td>
                    </tr>
                    <tr>
                        <td>Total Deducciones:</td>
                        <td style="color: #dc3545;">$<?php echo number_format($receipt->total_deductions, 2); ?></td>
                    </tr>
                    <tr style="font-weight: bold; background: #f8f9fa;">
                        <td>Neto a Pagar:</td>
                        <td style="color: #28a745;">$<?php echo number_format($receipt->net_payment, 2); ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <?php if ($receipt->is_stamped): ?>
            <div class="alert">
                <strong>✓ Recibo Timbrado</strong><br>
                <small>UUID: <?php echo htmlspecialchars($receipt->cfdi_uuid, ENT_QUOTES, 'UTF-8'); ?></small><br>
                <small>Este recibo cuenta con validez fiscal ante el SAT</small>
            </div>
        <?php endif; ?>

        <div style="text-align: center; margin: 30px 0;">
            <a href="<?php echo $receipt_url; ?>" class="button">
                📄 Ver Recibo Completo
            </a>
            <br>
            <a href="<?php echo $pdf_url; ?>" class="button" style="background: #28a745;">
                📥 Descargar PDF
            </a>
        </div>

        <p style="color: #6c757d; font-size: 14px; text-align: center;">
            También puedes acceder a tu recibo desde el portal de empleados ingresando con tu código de empleado.
        </p>
    </div>

    <div class="footer">
        <p><strong>Sistema de Nómina</strong></p>
        <p>Este es un correo automático, por favor no responder.</p>
        <p>Si tienes dudas o comentarios, contacta al departamento de Recursos Humanos.</p>
        <p style="margin-top: 15px; color: #adb5bd;">
            © <?php echo date('Y'); ?> Todos los derechos reservados
        </p>
    </div>
</body>
</html>
