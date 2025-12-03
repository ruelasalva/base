<?php
/**
 * Script de corrección de encoding en base de datos
 * Ejecutar desde navegador: http://localhost/base/fix_encoding.php
 */

// Configuración
$host = 'localhost';
$db   = 'base';
$user = 'root';
$pass = '';

// Conectar
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Forzar UTF-8
$conn->set_charset("utf8mb4");
$conn->query("SET NAMES utf8mb4");

echo "<h2>Corrección de Encoding UTF-8</h2>";
echo "<hr>";

// Correcciones
$updates = [
    [
        'table' => 'system_modules',
        'id' => 14,
        'display_name' => 'Órdenes de Compra',
        'description' => 'Gestión de órdenes'
    ],
    [
        'table' => 'system_modules',
        'id' => 15,
        'display_name' => 'Recepciones',
        'description' => 'Recepción de mercancía'
    ]
];

foreach ($updates as $update) {
    $sql = "UPDATE {$update['table']} 
            SET display_name = ?, description = ? 
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssi', $update['display_name'], $update['description'], $update['id']);
    
    if ($stmt->execute()) {
        echo "✓ Actualizado: {$update['display_name']}<br>";
    } else {
        echo "✗ Error: " . $stmt->error . "<br>";
    }
}

echo "<hr><h3>Verificación:</h3>";

// Verificar
$result = $conn->query("SELECT id, name, display_name, description FROM system_modules WHERE id IN (14, 15)");

echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr><th>ID</th><th>Name</th><th>Display Name</th><th>Description</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['name']}</td>";
    echo "<td>{$row['display_name']}</td>";
    echo "<td>{$row['description']}</td>";
    echo "</tr>";
}

echo "</table>";

$conn->close();

echo "<hr><p><strong>¡Corrección completada!</strong> Refresque su navegador en el panel admin.</p>";
?>
