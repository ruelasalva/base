<?php
// Fix module names encoding
$modules_fix = [
	['id' => 56, 'name' => 'almacenes', 'display' => 'Almacén'],
	['id' => 57, 'name' => 'categorias', 'display' => 'Categorías'],
	['id' => 63, 'name' => 'ordenes_compra', 'display' => 'Órdenes de Compra'],
	['id' => 65, 'name' => 'polizas', 'display' => 'Pólizas Contables'],
];

$conn = new mysqli('localhost', 'root', '', 'base');
$conn->set_charset('utf8mb4');

if ($conn->connect_error) {
	die("Error de conexión: " . $conn->connect_error);
}

echo "=== CORRECCIÓN DE ENCODING EN MODULES ===\n\n";

foreach ($modules_fix as $mod) {
	$stmt = $conn->prepare("UPDATE modules SET display_name = ? WHERE id = ?");
	$stmt->bind_param('si', $mod['display'], $mod['id']);
	if ($stmt->execute()) {
		echo "✓ {$mod['name']}: {$mod['display']}\n";
	} else {
		echo "✗ Error en {$mod['name']}: " . $stmt->error . "\n";
	}
	$stmt->close();
}

echo "\n✅ Módulos corregidos\n\n";
echo "=== VERIFICACIÓN ===\n";

// Verificar
$result = $conn->query("SELECT id, name, display_name FROM modules WHERE id BETWEEN 56 AND 66 ORDER BY id");
while ($row = $result->fetch_assoc()) {
	echo sprintf("%2d | %-20s | %s\n", $row['id'], $row['name'], $row['display_name']);
}

$conn->close();
echo "\n=== FIN ===\n";
