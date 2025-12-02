<?php
/**
 * Ejemplo de uso del sistema de logs en un controlador
 */

class Controller_Example extends Controller
{
	public function action_index()
	{
		// Log de información general
		Log::info('Acceso a página principal', array(
			'ip' => Input::real_ip(),
			'user_agent' => Input::user_agent()
		));

		return Response::forge(View::forge('welcome/index'));
	}

	public function action_login()
	{
		try {
			$username = Input::post('username');
			$password = Input::post('password');

			// Log de debug (solo visible en development)
			Log::debug('Intento de login', array(
				'username' => $username,
				'ip' => Input::real_ip()
			));

			// Simular validación
			if ($username && $password) {
				$user_id = 123; // ID del usuario autenticado

				// Log de actividad para auditoría
				Log::activity('login', $user_id, array(
					'ip' => Input::real_ip(),
					'user_agent' => Input::user_agent(),
					'timestamp' => date('Y-m-d H:i:s')
				));

				Log::info('Usuario autenticado exitosamente', array(
					'user_id' => $user_id,
					'username' => $username
				));

				return Response::forge(array('status' => 'success'));
			} else {
				// Log de advertencia
				Log::warning('Intento de login fallido', array(
					'username' => $username,
					'ip' => Input::real_ip(),
					'reason' => 'Credenciales inválidas'
				));

				return Response::forge(array('status' => 'error'), 401);
			}
		} catch (Exception $e) {
			// Log de error
			Log::error('Error en proceso de login', array(
				'exception' => $e->getMessage(),
				'file' => $e->getFile(),
				'line' => $e->getLine(),
				'trace' => $e->getTraceAsString()
			));

			return Response::forge(array('status' => 'error'), 500);
		}
	}

	public function action_database_example()
	{
		try {
			$query = 'SELECT * FROM users WHERE id = ?';
			$params = array(123);

			// Medir tiempo de ejecución
			$start = microtime(true);
			
			// Ejecutar query (ejemplo)
			// $result = DB::query($query)->parameters($params)->execute();
			
			$execution_time = microtime(true) - $start;

			// Log de SQL (solo en development)
			Log::sql($query, $execution_time, $params);

			return Response::forge(array('status' => 'success'));
		} catch (Database_Exception $e) {
			Log::error('Error en consulta de base de datos', array(
				'query' => $query,
				'params' => $params,
				'exception' => $e->getMessage()
			));

			return Response::forge(array('status' => 'error'), 500);
		}
	}

	public function action_create_record()
	{
		try {
			$user_id = 456; // Usuario actual
			$data = Input::post();

			Log::debug('Creando nuevo registro', array(
				'user_id' => $user_id,
				'data' => $data
			));

			// Simular creación de registro
			$new_id = 789;

			// Log de actividad
			Log::activity('create_record', $user_id, array(
				'table' => 'products',
				'record_id' => $new_id,
				'data' => $data
			));

			Log::info('Registro creado exitosamente', array(
				'record_id' => $new_id,
				'user_id' => $user_id
			));

			return Response::forge(array(
				'status' => 'success',
				'record_id' => $new_id
			));
		} catch (Exception $e) {
			Log::error('Error al crear registro', array(
				'user_id' => $user_id,
				'data' => $data,
				'exception' => $e->getMessage()
			));

			return Response::forge(array('status' => 'error'), 500);
		}
	}
}
