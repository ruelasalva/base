<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
body {
    font-family: 'DejaVu Sans', sans-serif;
    font-size: 11px;
    margin: 20px;
}
.header {
    text-align: center;
    border-bottom: 2px solid #008ad5;
    margin-bottom: 15px;
}
.header img {
    width: 120px;
}
.header h1 {
    font-size: 18px;
    margin: 5px 0 0 0;
    color: #008ad5;
}
.header h2 {
    font-size: 14px;
    margin: 0;
    color: #333;
}
.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}
.table th, .table td {
    border: 1px solid #ccc;
    padding: 5px;
    text-align: left;
}
.table th {
    background-color: #f2f2f2;
    color: #333;
    font-weight: bold;
}
.footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    font-size: 10px;
    color: #666;
    text-align: right;
    border-top: 1px solid #ccc;
    padding-top: 5px;
}
</style>
</head>
<body>

<div class="header">
    <img src="<?php echo Uri::base(false) . 'assets/img/admin/logo_sajor.png'; ?>" alt="Sajor">
    <h1>Distribuidora Sajor</h1>
    <h2>Reporte: <?php echo htmlspecialchars($reporte->query_name); ?></h2>
    <p><small><?php echo htmlspecialchars($reporte->description); ?></small></p>
</div>

<table class="table">
    <thead>
        <tr>
            <?php foreach (array_keys($rows[0]) as $col): ?>
                <th><?php echo htmlspecialchars($col); ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $r): ?>
        <tr>
            <?php foreach ($r as $v): ?>
                <td><?php echo htmlspecialchars($v); ?></td>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="footer">
    Generado automáticamente por Sajor ERP | <?php echo date('d/m/Y H:i:s'); ?>
</div>

</body>
</html>
