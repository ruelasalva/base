-- Script para insertar productos y servicios SAT más comunes
-- Este es un conjunto básico de los códigos más utilizados

-- Productos y Servicios más comunes por categoría

-- ALIMENTOS Y BEBIDAS
INSERT INTO sat_productos_servicios (clave, descripcion, incluye_iva, incluye_ieps, is_active) VALUES
('50202300', 'Pan y productos de panadería', 'Opcional', 'No', 1),
('50121600', 'Agua embotellada', 'Opcional', 'No', 1),
('50121700', 'Bebidas gaseosas', 'Sí', 'Sí', 1),
('50131600', 'Carne de res', 'Opcional', 'No', 1),
('50131700', 'Carne de cerdo', 'Opcional', 'No', 1),
('50131800', 'Carne de pollo', 'Opcional', 'No', 1),
('50192100', 'Lácteos y productos lácteos', 'Opcional', 'No', 1),
('50192300', 'Leche y crema', 'Opcional', 'No', 1),
('50192400', 'Quesos', 'Opcional', 'No', 1)
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- PRODUCTOS DE FERRETERÍA Y CONSTRUCCIÓN
INSERT INTO sat_productos_servicios (clave, descripcion, incluye_iva, incluye_ieps, is_active) VALUES
('30161500', 'Cemento y concreto', 'Sí', 'No', 1),
('30161700', 'Agregados', 'Sí', 'No', 1),
('30162000', 'Varilla', 'Sí', 'No', 1),
('30171500', 'Ladrillos', 'Sí', 'No', 1),
('31161500', 'Cableado eléctrico', 'Sí', 'No', 1),
('40101500', 'Material de imprenta y escritura', 'Sí', 'No', 1)
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- SERVICIOS PROFESIONALES
INSERT INTO sat_productos_servicios (clave, descripcion, incluye_iva, incluye_ieps, is_active) VALUES
('80101500', 'Servicios de consultoría de gerencia corporativa', 'Sí', 'No', 1),
('80101600', 'Servicios de consultoría de administración', 'Sí', 'No', 1),
('81101500', 'Servicios de ingeniería', 'Sí', 'No', 1),
('81101600', 'Servicios de arquitectura', 'Sí', 'No', 1),
('81111500', 'Servicios de consultoría en sistemas', 'Sí', 'No', 1),
('81111600', 'Servicios de diseño de sistemas de cómputo', 'Sí', 'No', 1),
('81112000', 'Servicios de programación de software de computadora', 'Sí', 'No', 1)
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- SERVICIOS DE EDUCACIÓN
INSERT INTO sat_productos_servicios (clave, descripcion, incluye_iva, incluye_ieps, is_active) VALUES
('86101500', 'Educación inicial y preescolar', 'No', 'No', 1),
('86101600', 'Educación primaria', 'No', 'No', 1),
('86101700', 'Educación secundaria', 'No', 'No', 1),
('86111500', 'Educación preparatoria', 'No', 'No', 1),
('86111600', 'Educación profesional', 'No', 'No', 1),
('86111700', 'Educación superior', 'No', 'No', 1),
('86121500', 'Cursos de capacitación', 'Sí', 'No', 1)
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- SERVICIOS MÉDICOS Y DE SALUD
INSERT INTO sat_productos_servicios (clave, descripcion, incluye_iva, incluye_ieps, is_active) VALUES
('85121500', 'Servicios de médicos generales', 'No', 'No', 1),
('85121600', 'Servicios de médicos especialistas', 'No', 'No', 1),
('85121700', 'Servicios de cirugía', 'No', 'No', 1),
('85121800', 'Servicios de análisis clínicos', 'No', 'No', 1),
('85131500', 'Servicios de hospital', 'No', 'No', 1),
('85141600', 'Servicios dentales', 'No', 'No', 1)
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- SERVICIOS DE TRANSPORTE
INSERT INTO sat_productos_servicios (clave, descripcion, incluye_iva, incluye_ieps, is_active) VALUES
('78101500', 'Transporte de carga por carretera', 'Sí', 'No', 1),
('78101600', 'Transporte de pasajeros', 'Sí', 'No', 1),
('78111800', 'Servicios de mensajería y paquetería', 'Sí', 'No', 1),
('78121500', 'Servicios de almacenamiento', 'Sí', 'No', 1)
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- PRODUCTOS ELECTRÓNICOS
INSERT INTO sat_productos_servicios (clave, descripcion, incluye_iva, incluye_ieps, is_active) VALUES
('43211500', 'Computadoras', 'Sí', 'No', 1),
('43211600', 'Computadoras portátiles', 'Sí', 'No', 1),
('43211900', 'Tabletas', 'Sí', 'No', 1),
('43212100', 'Monitores', 'Sí', 'No', 1),
('43212200', 'Teclados', 'Sí', 'No', 1),
('43212300', 'Ratones de computadora', 'Sí', 'No', 1),
('43191500', 'Teléfonos celulares', 'Sí', 'No', 1)
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- MOBILIARIO Y EQUIPO DE OFICINA
INSERT INTO sat_productos_servicios (clave, descripcion, incluye_iva, incluye_ieps, is_active) VALUES
('56101500', 'Muebles de oficina', 'Sí', 'No', 1),
('56101600', 'Escritorios', 'Sí', 'No', 1),
('56101700', 'Sillas de oficina', 'Sí', 'No', 1),
('56111500', 'Archiveros', 'Sí', 'No', 1),
('44101500', 'Equipo de oficina', 'Sí', 'No', 1),
('44101800', 'Impresoras', 'Sí', 'No', 1),
('44101900', 'Fotocopiadoras', 'Sí', 'No', 1)
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- SERVICIOS INMOBILIARIOS
INSERT INTO sat_productos_servicios (clave, descripcion, incluye_iva, incluye_ieps, is_active) VALUES
('70151500', 'Arrendamiento de bienes inmuebles', 'Sí', 'No', 1),
('70151600', 'Arrendamiento de oficinas', 'Sí', 'No', 1),
('70151700', 'Arrendamiento de locales comerciales', 'Sí', 'No', 1),
('72151500', 'Construcción de edificios residenciales', 'Sí', 'No', 1),
('72151600', 'Construcción de edificios comerciales', 'Sí', 'No', 1)
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- SERVICIOS FINANCIEROS Y SEGUROS
INSERT INTO sat_productos_servicios (clave, descripcion, incluye_iva, incluye_ieps, is_active) VALUES
('84111500', 'Servicios de banca comercial', 'No', 'No', 1),
('84121500', 'Servicios de seguros', 'No', 'No', 1),
('84121600', 'Servicios de seguros de vida', 'No', 'No', 1),
('84131500', 'Servicios contables', 'Sí', 'No', 1),
('84131600', 'Servicios de auditoría', 'Sí', 'No', 1)
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- PUBLICIDAD Y MERCADOTECNIA
INSERT INTO sat_productos_servicios (clave, descripcion, incluye_iva, incluye_ieps, is_active) VALUES
('83111500', 'Servicios de publicidad', 'Sí', 'No', 1),
('83111600', 'Servicios de mercadotecnia', 'Sí', 'No', 1),
('83111700', 'Servicios de investigación de mercado', 'Sí', 'No', 1),
('83121500', 'Servicios de diseño gráfico', 'Sí', 'No', 1)
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- SERVICIOS JURÍDICOS
INSERT INTO sat_productos_servicios (clave, descripcion, incluye_iva, incluye_ieps, is_active) VALUES
('82111500', 'Servicios jurídicos', 'Sí', 'No', 1),
('82111600', 'Servicios de litigio', 'Sí', 'No', 1),
('82111700', 'Servicios de asesoría jurídica', 'Sí', 'No', 1)
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- RESTAURANTES Y ALIMENTACIÓN
INSERT INTO sat_productos_servicios (clave, descripcion, incluye_iva, incluye_ieps, is_active) VALUES
('90101500', 'Servicios de restaurantes', 'Sí', 'No', 1),
('90101600', 'Servicios de cafeterías', 'Sí', 'No', 1),
('90111500', 'Servicios de catering', 'Sí', 'No', 1)
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- VEHÍCULOS Y REFACCIONES
INSERT INTO sat_productos_servicios (clave, descripcion, incluye_iva, incluye_ieps, is_active) VALUES
('25101500', 'Vehículos de motor', 'Sí', 'No', 1),
('25101600', 'Automóviles', 'Sí', 'No', 1),
('25101700', 'Camionetas', 'Sí', 'No', 1),
('25101800', 'Camiones', 'Sí', 'No', 1),
('25171500', 'Llantas y neumáticos', 'Sí', 'No', 1),
('15111500', 'Combustibles', 'Sí', 'Sí', 1),
('15111600', 'Gasolina', 'Sí', 'Sí', 1),
('15111700', 'Diesel', 'Sí', 'Sí', 1)
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- Verificar inserciones
SELECT 'Productos insertados:', COUNT(*) FROM sat_productos_servicios WHERE clave IN (
	'50202300', '50121600', '80101500', '85121500', '78101500',
	'43211500', '56101500', '70151500', '84111500', '83111500',
	'82111500', '90101500', '25101500', '15111500'
);
