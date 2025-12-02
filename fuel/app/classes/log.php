<?php
/**
 * Helper para manejar logs de manera organizada
 * 
 * Uso:
 *   Log::info('Usuario logueado', array('user_id' => 123));
 *   Log::debug('Query ejecutada', array('sql' => $query));
 *   Log::warning('Intento de acceso no autorizado', array('ip' => $ip));
 *   Log::error('Error en base de datos', array('exception' => $e->getMessage()));
 */

class Log
{
	/**
	 * Log de nivel INFO
	 * Para información general del flujo de la aplicación
	 */
	public static function info($message, $context = array())
	{
		self::write('INFO', $message, $context);
	}

	/**
	 * Log de nivel DEBUG
	 * Para información detallada útil en desarrollo
	 */
	public static function debug($message, $context = array())
	{
		self::write('DEBUG', $message, $context);
	}

	/**
	 * Log de nivel WARNING
	 * Para advertencias que no detienen la ejecución
	 */
	public static function warning($message, $context = array())
	{
		self::write('WARNING', $message, $context);
	}

	/**
	 * Log de nivel ERROR
	 * Para errores que requieren atención
	 */
	public static function error($message, $context = array())
	{
		self::write('ERROR', $message, $context);
	}

	/**
	 * Escribe el log con formato personalizado
	 */
	protected static function write($level, $message, $context = array())
	{
		// Formato: [NIVEL] Mensaje | Context: {json}
		$contextStr = !empty($context) ? ' | Context: ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
		
		$logMessage = "[{$level}] {$message}{$contextStr}";
		
		// Usa el logger nativo de Fuel
		switch ($level) {
			case 'INFO':
				\Fuel\Core\Log::info($logMessage);
				break;
			case 'DEBUG':
				\Fuel\Core\Log::debug($logMessage);
				break;
			case 'WARNING':
				\Fuel\Core\Log::warning($logMessage);
				break;
			case 'ERROR':
				\Fuel\Core\Log::error($logMessage);
				break;
		}
	}

	/**
	 * Log de actividad de usuario
	 * Guarda en un archivo separado para auditoría
	 */
	public static function activity($action, $user_id, $details = array())
	{
		$logPath = APPPATH . 'logs/activity/';
		
		// Crear directorio si no existe
		if (!is_dir($logPath)) {
			mkdir($logPath, 0755, true);
		}
		
		$logFile = $logPath . date('Y-m-d') . '.log';
		$timestamp = date('Y-m-d H:i:s');
		
		$logEntry = "[{$timestamp}] User: {$user_id} | Action: {$action}";
		
		if (!empty($details)) {
			$logEntry .= ' | Details: ' . json_encode($details, JSON_UNESCAPED_UNICODE);
		}
		
		$logEntry .= PHP_EOL;
		
		file_put_contents($logFile, $logEntry, FILE_APPEND);
	}

	/**
	 * Log de consultas SQL
	 * Guarda en un archivo separado para análisis de rendimiento
	 */
	public static function sql($query, $execution_time = null, $params = array())
	{
		// Solo en desarrollo
		if (\Fuel::$env !== \Fuel::DEVELOPMENT) {
			return;
		}
		
		$logPath = APPPATH . 'logs/sql/';
		
		// Crear directorio si no existe
		if (!is_dir($logPath)) {
			mkdir($logPath, 0755, true);
		}
		
		$logFile = $logPath . date('Y-m-d') . '.log';
		$timestamp = date('Y-m-d H:i:s');
		
		$logEntry = "[{$timestamp}]";
		
		if ($execution_time !== null) {
			$logEntry .= " [{$execution_time}s]";
		}
		
		$logEntry .= " {$query}";
		
		if (!empty($params)) {
			$logEntry .= ' | Params: ' . json_encode($params, JSON_UNESCAPED_UNICODE);
		}
		
		$logEntry .= PHP_EOL;
		
		file_put_contents($logFile, $logEntry, FILE_APPEND);
	}
}
