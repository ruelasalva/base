<?php
/**
 * Controlador de Instalación
 *
 * Maneja la instalación y actualización de la base de datos.
 * Este instalador permite configurar la base de datos inicial y ejecutar
 * migraciones para mantener el esquema actualizado.
 *
 * @package    app
 * @extends    Controller
 */
class Controller_Install extends Controller
{
	/**
	 * @var  View  Template de instalación
	 */
	public $template;

	/**
	 * @var  string  Nombre del template
	 */
	public $template_name = 'install/template';

	/**
	 * @var  string  Ruta a las migraciones
	 */
	protected $migrations_path;

	/**
	 * @var  string  Archivo de bloqueo de instalación
	 */
	protected $lock_file;

	/**
	 * BEFORE
	 *
	 * Inicializa el instalador.
	 *
	 * @return  void
	 */
	public function before()
	{
		parent::before();

		// Definir rutas
		$this->migrations_path = APPPATH . 'migrations' . DIRECTORY_SEPARATOR;
		$this->lock_file = APPPATH . 'config' . DIRECTORY_SEPARATOR . '.installed';

		// Cargar template
		$this->template = View::forge($this->template_name);
		$this->template->title = 'Instalador del Sistema';
		$this->template->content = '';
	}

	/**
	 * AFTER
	 *
	 * Renderiza la respuesta.
	 *
	 * @param   Response  $response  Response object
	 * @return  Response
	 */
	public function after($response)
	{
		$response = Response::forge($this->template);
		return parent::after($response);
	}

	/**
	 * INDEX
	 *
	 * Página principal del instalador.
	 * Muestra el estado actual y opciones disponibles.
	 *
	 * @return  void
	 */
	public function action_index()
	{
		$data = array();

		// Verificar estado de instalación
		$data['is_installed'] = $this->is_installed();
		$data['db_connected'] = $this->check_database_connection();
		$data['pending_migrations'] = array();
		$data['executed_migrations'] = array();
		$data['error'] = null;

		if ($data['db_connected'])
		{
			try
			{
				$data['pending_migrations'] = $this->get_pending_migrations();
				$data['executed_migrations'] = $this->get_executed_migrations();
			}
			catch (\Exception $e)
			{
				$data['error'] = $e->getMessage();
			}
		}

		$this->template->title = 'Instalador del Sistema';
		$this->template->content = View::forge('install/index', $data);
	}

	/**
	 * CONFIGURAR
	 *
	 * Formulario para configurar la conexión a base de datos.
	 *
	 * @return  void
	 */
	public function action_configurar()
	{
		$data = array();
		$data['error'] = null;
		$data['success'] = null;

		if (Input::method() === 'POST')
		{
			// Validar CSRF
			if ( ! Security::check_token())
			{
				$data['error'] = 'Token de seguridad inválido. Intente nuevamente.';
			}
			else
			{
				// Obtener datos del formulario
				$db_host = trim(Input::post('db_host', 'localhost'));
				$db_name = trim(Input::post('db_name', ''));
				$db_user = trim(Input::post('db_user', ''));
				$db_pass = Input::post('db_pass', '');

				// Validar datos
				if (empty($db_name) || empty($db_user))
				{
					$data['error'] = 'El nombre de la base de datos y el usuario son requeridos.';
				}
				else
				{
					// Probar conexión
					try
					{
						$dsn = "mysql:host={$db_host};dbname={$db_name}";
						$pdo = new \PDO($dsn, $db_user, $db_pass, array(
							\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
						));

						// Conexión exitosa, guardar configuración
						$this->save_database_config($db_host, $db_name, $db_user, $db_pass);
						$data['success'] = 'Conexión exitosa. La configuración ha sido guardada.';

						// Redirigir al instalador
						Session::set_flash('success', 'Configuración de base de datos guardada correctamente.');
						Response::redirect('install');
					}
					catch (\PDOException $e)
					{
						$data['error'] = 'Error de conexión: ' . $e->getMessage();
					}
				}
			}
		}

		// Cargar configuración actual
		$current_config = Config::get('db.default');
		$data['current_host'] = 'localhost';
		$data['current_db'] = '';
		$data['current_user'] = 'root';

		if ($current_config && isset($current_config['connection']['dsn']))
		{
			preg_match('/host=([^;]+)/', $current_config['connection']['dsn'], $host_match);
			preg_match('/dbname=([^;]+)/', $current_config['connection']['dsn'], $db_match);

			$data['current_host'] = isset($host_match[1]) ? $host_match[1] : 'localhost';
			$data['current_db'] = isset($db_match[1]) ? $db_match[1] : '';
			$data['current_user'] = isset($current_config['connection']['username']) ? $current_config['connection']['username'] : 'root';
		}

		$this->template->title = 'Configurar Base de Datos';
		$this->template->content = View::forge('install/configurar', $data);
	}

	/**
	 * EJECUTAR
	 *
	 * Ejecuta las migraciones pendientes.
	 *
	 * @return  void
	 */
	public function action_ejecutar()
	{
		$data = array();
		$data['results'] = array();
		$data['error'] = null;

		if (Input::method() === 'POST')
		{
			// Validar CSRF
			if ( ! Security::check_token())
			{
				$data['error'] = 'Token de seguridad inválido.';
			}
			else
			{
				$migrations = Input::post('migrations', array());

				if (empty($migrations))
				{
					$data['error'] = 'No se seleccionaron migraciones para ejecutar.';
				}
				else
				{
					foreach ($migrations as $migration)
					{
						$result = $this->execute_migration($migration);
						$data['results'][] = $result;
					}

					// Marcar como instalado si todas las migraciones fueron exitosas
					$all_success = true;
					foreach ($data['results'] as $result)
					{
						if ( ! $result['success'])
						{
							$all_success = false;
							break;
						}
					}

					if ($all_success && ! empty($data['results']))
					{
						$this->mark_as_installed();
					}
				}
			}
		}

		$data['pending_migrations'] = $this->get_pending_migrations();

		$this->template->title = 'Ejecutar Migraciones';
		$this->template->content = View::forge('install/ejecutar', $data);
	}

	/**
	 * CREAR ADMIN
	 *
	 * Crea el usuario administrador inicial.
	 *
	 * @return  void
	 */
	public function action_crear_admin()
	{
		$data = array();
		$data['error'] = null;
		$data['success'] = null;

		if (Input::method() === 'POST')
		{
			// Validar CSRF
			if ( ! Security::check_token())
			{
				$data['error'] = 'Token de seguridad inválido.';
			}
			else
			{
				$username = trim(Input::post('username', ''));
				$email = trim(Input::post('email', ''));
				$password = Input::post('password', '');
				$confirm_password = Input::post('confirm_password', '');

				// Validaciones
				if (empty($username) || empty($email) || empty($password))
				{
					$data['error'] = 'Todos los campos son requeridos.';
				}
				elseif ( ! filter_var($email, FILTER_VALIDATE_EMAIL))
				{
					$data['error'] = 'El email no es válido.';
				}
				elseif (strlen($password) < 8)
				{
					$data['error'] = 'La contraseña debe tener al menos 8 caracteres.';
				}
				elseif ($password !== $confirm_password)
				{
					$data['error'] = 'Las contraseñas no coinciden.';
				}
				else
				{
					try
					{
						$result = $this->create_admin_user($username, $email, $password);

						if ($result)
						{
							$data['success'] = 'Usuario administrador creado correctamente.';
							Session::set_flash('success', 'Usuario administrador creado. La instalación está completa.');
							Response::redirect('install/completado');
						}
						else
						{
							$data['error'] = 'Error al crear el usuario administrador.';
						}
					}
					catch (\Exception $e)
					{
						$data['error'] = 'Error: ' . $e->getMessage();
					}
				}
			}
		}

		$this->template->title = 'Crear Usuario Administrador';
		$this->template->content = View::forge('install/crear_admin', $data);
	}

	/**
	 * COMPLETADO
	 *
	 * Muestra la página de instalación completada.
	 *
	 * @return  void
	 */
	public function action_completado()
	{
		$data = array();

		$this->template->title = 'Instalación Completada';
		$this->template->content = View::forge('install/completado', $data);
	}

	/**
	 * VERIFICAR BASE DATOS
	 *
	 * Verifica la conexión a la base de datos via AJAX.
	 *
	 * @return  void
	 */
	public function action_verificar_db()
	{
		if ( ! Input::is_ajax())
		{
			throw new HttpNotFoundException();
		}

		$db_host = Input::post('db_host', 'localhost');
		$db_name = Input::post('db_name', '');
		$db_user = Input::post('db_user', '');
		$db_pass = Input::post('db_pass', '');

		try
		{
			$dsn = "mysql:host={$db_host};dbname={$db_name}";
			$pdo = new \PDO($dsn, $db_user, $db_pass, array(
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
			));

			$response = array(
				'success' => true,
				'message' => 'Conexión exitosa',
			);
		}
		catch (\PDOException $e)
		{
			$response = array(
				'success' => false,
				'message' => 'Error: ' . $e->getMessage(),
			);
		}

		$this->template = Response::forge(json_encode($response), 200, array(
			'Content-Type' => 'application/json',
		));
	}

	// =========================================================================
	// MÉTODOS PROTEGIDOS
	// =========================================================================

	/**
	 * Verifica si el sistema está instalado
	 *
	 * @return  bool
	 */
	protected function is_installed()
	{
		return file_exists($this->lock_file);
	}

	/**
	 * Marca el sistema como instalado
	 *
	 * @return  void
	 */
	protected function mark_as_installed()
	{
		$content = "<?php\n// Sistema instalado el: " . date('Y-m-d H:i:s') . "\n// NO ELIMINE ESTE ARCHIVO\nreturn true;\n";
		file_put_contents($this->lock_file, $content);
	}

	/**
	 * Verifica la conexión a la base de datos
	 *
	 * @return  bool
	 */
	protected function check_database_connection()
	{
		try
		{
			$db_config = Config::get('db.default');

			if ( ! $db_config || ! isset($db_config['connection']['dsn']))
			{
				return false;
			}

			$pdo = new \PDO(
				$db_config['connection']['dsn'],
				$db_config['connection']['username'],
				$db_config['connection']['password'],
				array(
					\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				)
			);

			return true;
		}
		catch (\PDOException $e)
		{
			return false;
		}
	}

	/**
	 * Obtiene las migraciones pendientes
	 *
	 * @return  array
	 */
	protected function get_pending_migrations()
	{
		$all_migrations = $this->get_all_migrations();
		$executed = $this->get_executed_migrations();
		$executed_files = array_column($executed, 'migration');

		$pending = array();

		foreach ($all_migrations as $migration)
		{
			if ( ! in_array($migration['name'], $executed_files))
			{
				$pending[] = $migration;
			}
		}

		return $pending;
	}

	/**
	 * Obtiene todas las migraciones disponibles
	 *
	 * @return  array
	 */
	protected function get_all_migrations()
	{
		$migrations = array();

		if ( ! is_dir($this->migrations_path))
		{
			return $migrations;
		}

		$files = glob($this->migrations_path . '*.sql');

		if ($files === false)
		{
			return $migrations;
		}

		sort($files);

		foreach ($files as $file)
		{
			$filename = basename($file);
			$parts = explode('_', $filename, 2);

			$migrations[] = array(
				'name' => $filename,
				'path' => $file,
				'version' => isset($parts[0]) ? $parts[0] : '000',
				'description' => isset($parts[1]) ? str_replace('.sql', '', $parts[1]) : $filename,
			);
		}

		return $migrations;
	}

	/**
	 * Obtiene las migraciones ejecutadas
	 *
	 * @return  array
	 */
	protected function get_executed_migrations()
	{
		try
		{
			$db_config = Config::get('db.default');

			$pdo = new \PDO(
				$db_config['connection']['dsn'],
				$db_config['connection']['username'],
				$db_config['connection']['password'],
				array(
					\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				)
			);

			// Verificar si existe la tabla de migraciones
			$stmt = $pdo->query("SHOW TABLES LIKE 'migrations'");

			if ($stmt->rowCount() === 0)
			{
				// Crear tabla de migraciones
				$this->create_migrations_table($pdo);
				return array();
			}

			// Obtener migraciones ejecutadas
			$stmt = $pdo->query("SELECT * FROM migrations ORDER BY executed_at ASC");
			return $stmt->fetchAll(\PDO::FETCH_ASSOC);
		}
		catch (\PDOException $e)
		{
			return array();
		}
	}

	/**
	 * Crea la tabla de migraciones
	 *
	 * @param   \PDO  $pdo  Conexión PDO
	 * @return  void
	 */
	protected function create_migrations_table($pdo)
	{
		$sql = "CREATE TABLE IF NOT EXISTS `migrations` (
			`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			`migration` VARCHAR(255) NOT NULL COMMENT 'Nombre del archivo de migración',
			`batch` INT(11) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Número de lote de ejecución',
			`executed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`),
			UNIQUE KEY `idx_migration` (`migration`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de migraciones ejecutadas';";

		$pdo->exec($sql);
	}

	/**
	 * Ejecuta una migración
	 *
	 * @param   string  $migration_name  Nombre del archivo de migración
	 * @return  array   Resultado de la ejecución
	 */
	protected function execute_migration($migration_name)
	{
		$file_path = $this->migrations_path . $migration_name;

		if ( ! file_exists($file_path))
		{
			return array(
				'migration' => $migration_name,
				'success' => false,
				'message' => 'Archivo no encontrado',
			);
		}

		try
		{
			$db_config = Config::get('db.default');

			$pdo = new \PDO(
				$db_config['connection']['dsn'],
				$db_config['connection']['username'],
				$db_config['connection']['password'],
				array(
					\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				)
			);

			// Leer y ejecutar el SQL
			$sql = file_get_contents($file_path);

			// Ejecutar múltiples statements
			$pdo->exec($sql);

			// Registrar la migración
			$batch = $this->get_next_batch($pdo);
			$stmt = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (:migration, :batch)");
			$stmt->execute(array(
				':migration' => $migration_name,
				':batch' => $batch,
			));

			return array(
				'migration' => $migration_name,
				'success' => true,
				'message' => 'Ejecutada correctamente',
			);
		}
		catch (\PDOException $e)
		{
			return array(
				'migration' => $migration_name,
				'success' => false,
				'message' => $e->getMessage(),
			);
		}
	}

	/**
	 * Obtiene el siguiente número de lote
	 *
	 * @param   \PDO  $pdo  Conexión PDO
	 * @return  int
	 */
	protected function get_next_batch($pdo)
	{
		$stmt = $pdo->query("SELECT MAX(batch) as max_batch FROM migrations");
		$result = $stmt->fetch(\PDO::FETCH_ASSOC);

		return (int) $result['max_batch'] + 1;
	}

	/**
	 * Guarda la configuración de base de datos
	 *
	 * @param   string  $host  Host de la base de datos
	 * @param   string  $name  Nombre de la base de datos
	 * @param   string  $user  Usuario
	 * @param   string  $pass  Contraseña
	 * @return  void
	 */
	protected function save_database_config($host, $name, $user, $pass)
	{
		// Crear el contenido del archivo de configuración
		$config_content = "<?php
/**
 * Configuración de Base de Datos del Tenant
 *
 * Generado automáticamente por el instalador
 * Fecha: " . date('Y-m-d H:i:s') . "
 *
 * NOTA: Este archivo sobrescribe la configuración por defecto.
 */

return array(
	'default' => array(
		'type'        => 'pdo',
		'connection'  => array(
			'dsn'      => 'mysql:host=" . addslashes($host) . ";dbname=" . addslashes($name) . "',
			'username' => '" . addslashes($user) . "',
			'password' => '" . addslashes($pass) . "',
		),
		'identifier'  => '`',
		'table_prefix' => '',
		'charset'     => 'utf8mb4',
		'collation'   => false,
		'enable_cache' => true,
		'profiling'   => false,
		'readonly'    => false,
	),
);
";

		// Guardar en el directorio de configuración del entorno actual
		$env = \Fuel::$env;
		$config_path = APPPATH . 'config' . DIRECTORY_SEPARATOR . $env . DIRECTORY_SEPARATOR . 'db.php';

		// Crear directorio si no existe
		$config_dir = dirname($config_path);
		if ( ! is_dir($config_dir))
		{
			mkdir($config_dir, 0755, true);
		}

		file_put_contents($config_path, $config_content);
	}

	/**
	 * Crea el usuario administrador
	 *
	 * @param   string  $username  Nombre de usuario
	 * @param   string  $email     Email
	 * @param   string  $password  Contraseña
	 * @return  bool
	 */
	protected function create_admin_user($username, $email, $password)
	{
		try
		{
			$db_config = Config::get('db.default');

			$pdo = new \PDO(
				$db_config['connection']['dsn'],
				$db_config['connection']['username'],
				$db_config['connection']['password'],
				array(
					\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				)
			);

			// Generar hash de contraseña
			$salt = Config::get('simpleauth.salt', 'ERP_MULTI_TENANT_SALT');
			$password_hash = base64_encode(hash_pbkdf2('sha256', hash('sha256', $password), $salt, 10000, 32, true));

			// Verificar si el usuario ya existe
			$stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
			$stmt->execute(array(':username' => $username, ':email' => $email));

			if ($stmt->rowCount() > 0)
			{
				throw new \Exception('El usuario o email ya existe');
			}

			// Insertar usuario administrador (group_id = 100 para super admin)
			$stmt = $pdo->prepare("
				INSERT INTO users (username, password, group_id, email, first_name, last_name, is_active, is_verified, created_at)
				VALUES (:username, :password, 100, :email, 'Administrador', 'Sistema', 1, 1, NOW())
			");

			$stmt->execute(array(
				':username' => $username,
				':password' => $password_hash,
				':email' => $email,
			));

			return true;
		}
		catch (\PDOException $e)
		{
			throw $e;
		}
	}
}
