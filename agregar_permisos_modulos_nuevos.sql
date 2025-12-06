-- Script para agregar permisos de los nuevos módulos en la base de datos 'base'
-- Estructura: module, action, name, description

-- FACTURACIÓN CFDI
INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('facturacion', 'view', 'Ver Facturación', 'Acceso al módulo de facturación CFDI', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Ver Facturación', is_active = 1, updated_at = UNIX_TIMESTAMP();

INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('facturacion', 'create', 'Crear Facturas', 'Crear nuevas facturas CFDI', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Crear Facturas', is_active = 1, updated_at = UNIX_TIMESTAMP();

INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('facturacion', 'edit', 'Editar Facturas', 'Modificar facturas existentes', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Editar Facturas', is_active = 1, updated_at = UNIX_TIMESTAMP();

INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('facturacion', 'delete', 'Cancelar Facturas', 'Cancelar facturas CFDI', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Cancelar Facturas', is_active = 1, updated_at = UNIX_TIMESTAMP();

-- CATÁLOGOS SAT
INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('sat', 'view', 'Ver Catálogos SAT', 'Acceso a catálogos SAT', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Ver Catálogos SAT', is_active = 1, updated_at = UNIX_TIMESTAMP();

INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('sat', 'edit', 'Editar Catálogos SAT', 'Modificar catálogos SAT', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Editar Catálogos SAT', is_active = 1, updated_at = UNIX_TIMESTAMP();

-- CUENTAS CONTABLES
INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('cuentascontables', 'view', 'Ver Cuentas Contables', 'Acceso al catálogo de cuentas', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Ver Cuentas Contables', is_active = 1, updated_at = UNIX_TIMESTAMP();

INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('cuentascontables', 'create', 'Crear Cuentas', 'Crear nuevas cuentas contables', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Crear Cuentas', is_active = 1, updated_at = UNIX_TIMESTAMP();

INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('cuentascontables', 'edit', 'Editar Cuentas', 'Modificar cuentas contables', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Editar Cuentas', is_active = 1, updated_at = UNIX_TIMESTAMP();

INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('cuentascontables', 'delete', 'Eliminar Cuentas', 'Eliminar cuentas contables', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Eliminar Cuentas', is_active = 1, updated_at = UNIX_TIMESTAMP();

-- PÓLIZAS
INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('polizas', 'view', 'Ver Pólizas', 'Acceso al módulo de pólizas', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Ver Pólizas', is_active = 1, updated_at = UNIX_TIMESTAMP();

INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('polizas', 'create', 'Crear Pólizas', 'Crear nuevas pólizas contables', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Crear Pólizas', is_active = 1, updated_at = UNIX_TIMESTAMP();

INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('polizas', 'edit', 'Editar Pólizas', 'Modificar pólizas existentes', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Editar Pólizas', is_active = 1, updated_at = UNIX_TIMESTAMP();

INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('polizas', 'delete', 'Eliminar Pólizas', 'Eliminar pólizas', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Eliminar Pólizas', is_active = 1, updated_at = UNIX_TIMESTAMP();

-- LIBRO MAYOR
INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('libromayor', 'view', 'Ver Libro Mayor', 'Acceso al libro mayor', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Ver Libro Mayor', is_active = 1, updated_at = UNIX_TIMESTAMP();

INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('libromayor', 'export', 'Exportar Libro Mayor', 'Exportar reportes del libro mayor', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Exportar Libro Mayor', is_active = 1, updated_at = UNIX_TIMESTAMP();

-- REPORTES FINANCIEROS
INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('reportesfinancieros', 'view', 'Ver Reportes Financieros', 'Acceso a reportes financieros', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Ver Reportes Financieros', is_active = 1, updated_at = UNIX_TIMESTAMP();

INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('reportesfinancieros', 'export', 'Exportar Reportes', 'Exportar reportes financieros', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Exportar Reportes', is_active = 1, updated_at = UNIX_TIMESTAMP();

-- ALMACENES
INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('almacenes', 'view', 'Ver Almacenes', 'Acceso al catálogo de almacenes', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Ver Almacenes', is_active = 1, updated_at = UNIX_TIMESTAMP();

INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('almacenes', 'create', 'Crear Almacenes', 'Crear nuevos almacenes', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Crear Almacenes', is_active = 1, updated_at = UNIX_TIMESTAMP();

INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('almacenes', 'edit', 'Editar Almacenes', 'Modificar almacenes existentes', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Editar Almacenes', is_active = 1, updated_at = UNIX_TIMESTAMP();

INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('almacenes', 'delete', 'Eliminar Almacenes', 'Eliminar almacenes', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Eliminar Almacenes', is_active = 1, updated_at = UNIX_TIMESTAMP();

-- PROVEEDORES
INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('proveedores', 'view', 'Ver Proveedores', 'Acceso al catálogo de proveedores', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Ver Proveedores', is_active = 1, updated_at = UNIX_TIMESTAMP();

INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('proveedores', 'create', 'Crear Proveedores', 'Crear nuevos proveedores', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Crear Proveedores', is_active = 1, updated_at = UNIX_TIMESTAMP();

INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('proveedores', 'edit', 'Editar Proveedores', 'Modificar proveedores existentes', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Editar Proveedores', is_active = 1, updated_at = UNIX_TIMESTAMP();

INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('proveedores', 'delete', 'Eliminar Proveedores', 'Eliminar proveedores', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Eliminar Proveedores', is_active = 1, updated_at = UNIX_TIMESTAMP();

-- MARCAS
INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('marcas', 'view', 'Ver Marcas', 'Acceso al catálogo de marcas', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Ver Marcas', is_active = 1, updated_at = UNIX_TIMESTAMP();

INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('marcas', 'create', 'Crear Marcas', 'Crear nuevas marcas', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Crear Marcas', is_active = 1, updated_at = UNIX_TIMESTAMP();

INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('marcas', 'edit', 'Editar Marcas', 'Modificar marcas existentes', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Editar Marcas', is_active = 1, updated_at = UNIX_TIMESTAMP();

INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('marcas', 'delete', 'Eliminar Marcas', 'Eliminar marcas', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Eliminar Marcas', is_active = 1, updated_at = UNIX_TIMESTAMP();

-- CATEGORÍAS
INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('categorias', 'view', 'Ver Categorías', 'Acceso al catálogo de categorías', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Ver Categorías', is_active = 1, updated_at = UNIX_TIMESTAMP();

INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('categorias', 'create', 'Crear Categorías', 'Crear nuevas categorías', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Crear Categorías', is_active = 1, updated_at = UNIX_TIMESTAMP();

INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('categorias', 'edit', 'Editar Categorías', 'Modificar categorías existentes', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Editar Categorías', is_active = 1, updated_at = UNIX_TIMESTAMP();

INSERT INTO permissions (module, action, name, description, is_active, created_at, updated_at)
VALUES ('categorias', 'delete', 'Eliminar Categorías', 'Eliminar categorías', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE name = 'Eliminar Categorías', is_active = 1, updated_at = UNIX_TIMESTAMP();

-- Verificación
SELECT 'Permisos configurados correctamente' AS Resultado;
SELECT COUNT(*) AS 'Total Permisos Nuevos' FROM permissions 
WHERE module IN ('facturacion', 'sat', 'cuentascontables', 'polizas', 'libromayor', 'reportesfinancieros', 'almacenes', 'proveedores', 'marcas', 'categorias');
