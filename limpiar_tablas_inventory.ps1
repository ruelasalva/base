# SCRIPT DE LIMPIEZA - TABLAS INVENTORY EN BASE DE DATOS
# Fecha: 4 de Diciembre de 2024
# Propósito: Eliminar tablas inventory_* que no se utilizan

Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  LIMPIEZA DE TABLAS INVENTORY" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

# Configuración de conexión
$mysqlPath = "c:\xampp\mysql\bin\mysql.exe"
$database = "base"
$username = "root"

# Tablas a eliminar
$tablesToDrop = @(
    "inventory_product_logs",
    "inventory_product_categories", 
    "inventory_products"
)

Write-Host "⚠️  ADVERTENCIA: Esta operación es IRREVERSIBLE" -ForegroundColor Red
Write-Host ""
Write-Host "Tablas que serán eliminadas:" -ForegroundColor Yellow
foreach ($table in $tablesToDrop) {
    Write-Host "   ❌ $table" -ForegroundColor Red
}
Write-Host ""

# Verificar si las tablas existen
Write-Host "Verificando existencia de tablas..." -ForegroundColor Cyan
Write-Host ""

$checkQuery = "SHOW TABLES LIKE 'inventory_%'"
$existingTables = & $mysqlPath -u $username -e $checkQuery $database 2>&1

if ($LASTEXITCODE -eq 0) {
    Write-Host "Tablas encontradas en la base de datos:" -ForegroundColor Green
    Write-Host $existingTables
    Write-Host ""
} else {
    Write-Host "❌ Error al conectar con MySQL" -ForegroundColor Red
    Write-Host $existingTables
    exit 1
}

# Mostrar contenido de las tablas antes de eliminar
Write-Host "============================================" -ForegroundColor Yellow
Write-Host "  CONTENIDO ACTUAL DE LAS TABLAS" -ForegroundColor Yellow
Write-Host "============================================" -ForegroundColor Yellow
Write-Host ""

foreach ($table in $tablesToDrop) {
    $countQuery = "SELECT COUNT(*) as total FROM $table"
    $result = & $mysqlPath -u $username -e $countQuery $database 2>&1
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "📊 Tabla '$table':" -ForegroundColor Cyan
        Write-Host $result
        Write-Host ""
    }
}

# Solicitar confirmación
Write-Host "============================================" -ForegroundColor Yellow
Write-Host ""
$confirmation = Read-Host "¿CONFIRMAS que deseas ELIMINAR estas tablas? (ESCRIBE 'ELIMINAR' para confirmar)"

if ($confirmation -eq 'ELIMINAR') {
    Write-Host ""
    Write-Host "Eliminando tablas..." -ForegroundColor Yellow
    Write-Host ""
    
    # Eliminar en orden correcto (respetando foreign keys)
    foreach ($table in $tablesToDrop) {
        $dropQuery = "DROP TABLE IF EXISTS $table"
        Write-Host "Ejecutando: $dropQuery" -ForegroundColor Cyan
        
        $result = & $mysqlPath -u $username -e $dropQuery $database 2>&1
        
        if ($LASTEXITCODE -eq 0) {
            Write-Host "✅ Tabla '$table' eliminada correctamente" -ForegroundColor Green
        } else {
            Write-Host "❌ Error al eliminar tabla '$table'" -ForegroundColor Red
            Write-Host "   Error: $result" -ForegroundColor Red
        }
        Write-Host ""
    }
    
    Write-Host "============================================" -ForegroundColor Green
    Write-Host "  LIMPIEZA DE TABLAS COMPLETADA" -ForegroundColor Green
    Write-Host "============================================" -ForegroundColor Green
    
    # Verificar que se eliminaron
    Write-Host ""
    Write-Host "Verificando eliminación..." -ForegroundColor Cyan
    $checkQuery = "SHOW TABLES LIKE 'inventory_%'"
    $remainingTables = & $mysqlPath -u $username -e $checkQuery $database 2>&1
    
    if ($remainingTables -match "inventory_") {
        Write-Host "⚠️  Aún quedan algunas tablas inventory_*:" -ForegroundColor Yellow
        Write-Host $remainingTables
    } else {
        Write-Host "✅ Todas las tablas inventory_* fueron eliminadas" -ForegroundColor Green
    }
    
} else {
    Write-Host ""
    Write-Host "❌ Operación cancelada - No se escribió 'ELIMINAR'" -ForegroundColor Yellow
    Write-Host "   Las tablas NO fueron eliminadas" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "Presiona cualquier tecla para continuar..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")

