# SCRIPT DE LIMPIEZA - ARCHIVOS DUPLICADOS DEL MÓDULO INVENTORY
# Fecha: 4 de Diciembre de 2024
# Propósito: Eliminar archivos creados por error que duplican funcionalidad existente

Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  LIMPIEZA DE ARCHIVOS INVENTORY DUPLICADOS" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

# Definir rutas base
$baseDir = "c:\xampp\htdocs\base\fuel\app"

# Lista de directorios a eliminar
$directoriesToDelete = @(
    "$baseDir\classes\model\inventory",
    "$baseDir\classes\helper\inventory",
    "$baseDir\classes\controller\admin\inventory",
    "$baseDir\views\admin\inventory"
)

# Mostrar archivos que serán eliminados
Write-Host "ARCHIVOS A ELIMINAR:" -ForegroundColor Yellow
Write-Host ""

foreach ($dir in $directoriesToDelete) {
    if (Test-Path $dir) {
        Write-Host "📁 $dir" -ForegroundColor Red
        $files = Get-ChildItem -Path $dir -Recurse -File
        foreach ($file in $files) {
            Write-Host "   ❌ $($file.FullName)" -ForegroundColor DarkRed
        }
        Write-Host ""
    } else {
        Write-Host "✅ Ya no existe: $dir" -ForegroundColor Green
        Write-Host ""
    }
}

# Contar total de archivos
$totalFiles = 0
foreach ($dir in $directoriesToDelete) {
    if (Test-Path $dir) {
        $totalFiles += (Get-ChildItem -Path $dir -Recurse -File).Count
    }
}

Write-Host "============================================" -ForegroundColor Yellow
Write-Host "TOTAL DE ARCHIVOS A ELIMINAR: $totalFiles" -ForegroundColor Yellow
Write-Host "============================================" -ForegroundColor Yellow
Write-Host ""

# Solicitar confirmación
$confirmation = Read-Host "¿Deseas eliminar estos archivos? (S/N)"

if ($confirmation -eq 'S' -or $confirmation -eq 's') {
    Write-Host ""
    Write-Host "Eliminando archivos..." -ForegroundColor Yellow
    Write-Host ""
    
    foreach ($dir in $directoriesToDelete) {
        if (Test-Path $dir) {
            try {
                Remove-Item -Path $dir -Recurse -Force
                Write-Host "✅ Eliminado: $dir" -ForegroundColor Green
            } catch {
                Write-Host "❌ Error al eliminar: $dir" -ForegroundColor Red
                Write-Host "   Error: $($_.Exception.Message)" -ForegroundColor Red
            }
        }
    }
    
    Write-Host ""
    Write-Host "============================================" -ForegroundColor Green
    Write-Host "  LIMPIEZA COMPLETADA" -ForegroundColor Green
    Write-Host "============================================" -ForegroundColor Green
    
} else {
    Write-Host ""
    Write-Host "❌ Operación cancelada por el usuario" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "Presiona cualquier tecla para continuar..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")

