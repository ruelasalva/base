<?php
/**
 * Archivo de idioma Español - Común
 * 
 * Traducciones generales del sistema
 */

return array(
	// Acciones generales
	'actions' => array(
		'add' => 'Agregar',
		'edit' => 'Editar',
		'delete' => 'Eliminar',
		'save' => 'Guardar',
		'cancel' => 'Cancelar',
		'back' => 'Regresar',
		'search' => 'Buscar',
		'filter' => 'Filtrar',
		'export' => 'Exportar',
		'import' => 'Importar',
		'view' => 'Ver',
		'print' => 'Imprimir',
		'download' => 'Descargar',
		'upload' => 'Subir',
		'submit' => 'Enviar',
		'close' => 'Cerrar',
		'confirm' => 'Confirmar',
		'refresh' => 'Refrescar',
		'reset' => 'Restablecer',
	),
	
	// Mensajes de éxito
	'success' => array(
		'saved' => 'Registro guardado correctamente',
		'updated' => 'Registro actualizado correctamente',
		'deleted' => 'Registro eliminado correctamente',
		'created' => 'Registro creado correctamente',
		'uploaded' => 'Archivo subido correctamente',
		'sent' => 'Enviado correctamente',
	),
	
	// Mensajes de error
	'error' => array(
		'general' => 'Ha ocurrido un error. Por favor intente nuevamente.',
		'required' => 'Este campo es requerido',
		'invalid' => 'El valor ingresado no es válido',
		'not_found' => 'Registro no encontrado',
		'duplicate' => 'Ya existe un registro con estos datos',
		'permission' => 'No tienes permisos para realizar esta acción',
		'database' => 'Error de base de datos',
		'file_upload' => 'Error al subir el archivo',
		'file_size' => 'El archivo es demasiado grande',
		'file_type' => 'Tipo de archivo no permitido',
	),
	
	// Mensajes de advertencia
	'warning' => array(
		'unsaved' => 'Hay cambios sin guardar',
		'delete_confirm' => '¿Está seguro que desea eliminar este registro?',
		'irreversible' => 'Esta acción no se puede deshacer',
	),
	
	// Mensajes informativos
	'info' => array(
		'no_records' => 'No hay registros para mostrar',
		'loading' => 'Cargando...',
		'processing' => 'Procesando...',
		'select_option' => 'Seleccione una opción',
	),
	
	// Campos comunes
	'fields' => array(
		'id' => 'ID',
		'name' => 'Nombre',
		'email' => 'Correo Electrónico',
		'phone' => 'Teléfono',
		'address' => 'Dirección',
		'city' => 'Ciudad',
		'state' => 'Estado',
		'country' => 'País',
		'zipcode' => 'Código Postal',
		'description' => 'Descripción',
		'notes' => 'Notas',
		'status' => 'Estado',
		'active' => 'Activo',
		'inactive' => 'Inactivo',
		'date' => 'Fecha',
		'created_at' => 'Fecha de Creación',
		'updated_at' => 'Fecha de Actualización',
		'created_by' => 'Creado Por',
		'updated_by' => 'Actualizado Por',
		'password' => 'Contraseña',
		'password_confirm' => 'Confirmar Contraseña',
		'username' => 'Usuario',
		'role' => 'Rol',
		'permissions' => 'Permisos',
	),
	
	// Navegación
	'nav' => array(
		'home' => 'Inicio',
		'dashboard' => 'Panel de Control',
		'admin' => 'Administración',
		'settings' => 'Configuración',
		'profile' => 'Perfil',
		'logout' => 'Cerrar Sesión',
		'login' => 'Iniciar Sesión',
	),
	
	// Paginación
	'pagination' => array(
		'previous' => 'Anterior',
		'next' => 'Siguiente',
		'first' => 'Primera',
		'last' => 'Última',
		'showing' => 'Mostrando',
		'of' => 'de',
		'results' => 'resultados',
		'page' => 'Página',
	),
	
	// Validación
	'validation' => array(
		'required' => 'El campo :field es requerido',
		'min_length' => 'El campo :field debe tener al menos :param caracteres',
		'max_length' => 'El campo :field no puede tener más de :param caracteres',
		'exact_length' => 'El campo :field debe tener exactamente :param caracteres',
		'match_field' => 'El campo :field debe coincidir con :param',
		'valid_email' => 'El campo :field debe ser un email válido',
		'valid_emails' => 'El campo :field debe contener emails válidos',
		'valid_url' => 'El campo :field debe ser una URL válida',
		'valid_ip' => 'El campo :field debe ser una IP válida',
		'numeric' => 'El campo :field debe ser numérico',
		'numeric_min' => 'El campo :field debe ser mayor o igual a :param',
		'numeric_max' => 'El campo :field debe ser menor o igual a :param',
		'valid_string' => 'El campo :field solo puede contener caracteres válidos',
	),
	
	// Módulos
	'modules' => array(
		'admin' => 'Administración',
		'clients' => 'Clientes',
		'partners' => 'Socios',
		'providers' => 'Proveedores',
		'sellers' => 'Vendedores',
		'store' => 'Tienda',
		'landing' => 'Página de Inicio',
	),
	
	// Días de la semana
	'days' => array(
		'monday' => 'Lunes',
		'tuesday' => 'Martes',
		'wednesday' => 'Miércoles',
		'thursday' => 'Jueves',
		'friday' => 'Viernes',
		'saturday' => 'Sábado',
		'sunday' => 'Domingo',
	),
	
	// Meses
	'months' => array(
		'january' => 'Enero',
		'february' => 'Febrero',
		'march' => 'Marzo',
		'april' => 'Abril',
		'may' => 'Mayo',
		'june' => 'Junio',
		'july' => 'Julio',
		'august' => 'Agosto',
		'september' => 'Septiembre',
		'october' => 'Octubre',
		'november' => 'Noviembre',
		'december' => 'Diciembre',
	),
	
	// Formatos de fecha
	'date_format' => array(
		'short' => 'd/m/Y',
		'long' => 'd de F de Y',
		'time' => 'H:i:s',
		'datetime' => 'd/m/Y H:i:s',
	),
);
