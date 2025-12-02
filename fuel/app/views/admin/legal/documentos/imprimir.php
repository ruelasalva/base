<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $doc->title; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            color: #333;
            margin: 40px;
        }
        h1, h2, h3 {
            text-align: center;
            margin-bottom: 10px;
        }
        h2 {
            font-size: 18pt;
            margin-top: 0;
        }
        p {
            line-height: 1.6;
            margin: 8px 0;
        }
        strong {
            color: #000;
        }
        hr {
            margin: 20px 0;
            border: none;
            border-top: 1px solid #ccc;
        }
        .meta {
            margin-bottom: 20px;
        }
        .meta p {
            margin: 3px 0;
            font-size: 10pt;
        }
        .content {
            margin-top: 15px;
            font-size: 12pt;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }
        ul, ol {
            margin: 5px 0 5px 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 11pt;
        }
        table, th, td {
            border: 1px solid #ccc;
            padding: 6px;
        }
        th {
            background: #f2f2f2;
        }
    </style>
</head>
<body>

    <!-- ENCABEZADO -->
    <h2><?php echo $doc->title; ?></h2>

    <!-- METADATOS -->
    <div class="meta">
        <p><strong>Categoría:</strong> <?php echo ucfirst($doc->category); ?></p>
        <p><strong>Tipo:</strong> <?php echo str_replace('_',' ', ucfirst($doc->type)); ?></p>
        <p><strong>Versión:</strong> <?php echo $doc->version; ?></p>
        <p><strong>Última actualización:</strong> <?php echo Date::forge($doc->updated_at)->format('%d/%m/%Y %H:%M'); ?></p>
    </div>

    <hr>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="content">
        <?php echo $doc->content ? html_entity_decode($doc->content) : '<em>Sin contenido</em>'; ?>
    </div>

    <hr>

    <!-- PIE -->
    <div class="footer">
        Documento generado automáticamente desde el sistema <br>
        Distribuidora Sajor - <?php echo date('d/m/Y H:i'); ?>
    </div>

</body>
</html>
