<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Imprimir - <?php echo $doc->title; ?></title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <style>
        body { font-size: 14px; margin: 20px; }
        .doc-title { text-align: center; margin-bottom: 20px; }
        .doc-content { margin-top: 20px; }
        .btn-print { margin-bottom: 20px; }
    </style>
</head>
<body onload="window.print()">

    <div class="doc-title">
        <h2><?php echo $doc->title; ?></h2>
        <small>Versión <?php echo $doc->version; ?></small>
    </div>

    <div class="doc-content">
        <?php echo html_entity_decode($doc->content); ?>
    </div>

</body>
</html>
