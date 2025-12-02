<?php

/**
 * HELPER ENCRYPTION
 * 
 * Helper para encriptación/desencriptación de datos sensibles
 * - Encriptación de certificados digitales (.cer/.key)
 * - Encriptación de contraseñas y credenciales
 * - Método: AES-256-CBC con IV único por registro
 * 
 * @package  app
 * @author   Base Multi-Tenant System
 */
class Helper_Encryption
{
	/**
	 * MÉTODO DE ENCRIPTACIÓN
	 */
	const CIPHER_METHOD = 'AES-256-CBC';

	/**
	 * OBTENER CLAVE DE ENCRIPTACIÓN
	 * 
	 * @param string $custom_key Clave personalizada (opcional)
	 * @return string
	 */
	private static function get_encryption_key($custom_key = null)
	{
		if ($custom_key)
		{
			return hash('sha256', $custom_key);
		}

		// Usar clave del config o generar una por defecto
		$config_key = \Config::get('security.encryption_key', null);
		
		if (!$config_key)
		{
			// IMPORTANTE: Generar y guardar en config/production/security.php
			\Log::warning('No se encontró encryption_key en config. Usando clave por defecto (NO SEGURO PARA PRODUCCIÓN)');
			$config_key = 'BASE_MULTI_TENANT_DEFAULT_KEY_CHANGE_ME_IN_PRODUCTION';
		}

		return hash('sha256', $config_key);
	}

	/**
	 * ENCRIPTAR TEXTO
	 * 
	 * @param string $data Texto a encriptar
	 * @param string $key Clave personalizada (opcional)
	 * @return string Base64 encoded: iv::encrypted_data
	 */
	public static function encrypt($data, $key = null)
	{
		if (empty($data))
		{
			return '';
		}

		try
		{
			$encryption_key = self::get_encryption_key($key);
			
			// Generar IV único (16 bytes para AES-256-CBC)
			$iv_length = openssl_cipher_iv_length(self::CIPHER_METHOD);
			$iv = openssl_random_pseudo_bytes($iv_length);
			
			// Encriptar
			$encrypted = openssl_encrypt(
				$data,
				self::CIPHER_METHOD,
				$encryption_key,
				OPENSSL_RAW_DATA,
				$iv
			);

			if ($encrypted === false)
			{
				throw new \Exception('Error al encriptar datos');
			}

			// Retornar IV + datos encriptados en base64
			return base64_encode($iv . $encrypted);
		}
		catch (\Exception $e)
		{
			\Log::error('Error en Helper_Encryption::encrypt: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * DESENCRIPTAR TEXTO
	 * 
	 * @param string $encrypted_data Base64 encoded: iv::encrypted_data
	 * @param string $key Clave personalizada (opcional)
	 * @return string|false Texto desencriptado o false si falla
	 */
	public static function decrypt($encrypted_data, $key = null)
	{
		if (empty($encrypted_data))
		{
			return '';
		}

		try
		{
			$encryption_key = self::get_encryption_key($key);
			
			// Decodificar base64
			$decoded = base64_decode($encrypted_data);
			
			if ($decoded === false)
			{
				throw new \Exception('Error al decodificar base64');
			}

			// Extraer IV (primeros 16 bytes)
			$iv_length = openssl_cipher_iv_length(self::CIPHER_METHOD);
			$iv = substr($decoded, 0, $iv_length);
			$encrypted = substr($decoded, $iv_length);

			// Desencriptar
			$decrypted = openssl_decrypt(
				$encrypted,
				self::CIPHER_METHOD,
				$encryption_key,
				OPENSSL_RAW_DATA,
				$iv
			);

			if ($decrypted === false)
			{
				throw new \Exception('Error al desencriptar datos');
			}

			return $decrypted;
		}
		catch (\Exception $e)
		{
			\Log::error('Error en Helper_Encryption::decrypt: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * ENCRIPTAR ARCHIVO
	 * 
	 * @param string $source_path Ruta del archivo original
	 * @param string $dest_path Ruta del archivo encriptado (opcional)
	 * @param string $key Clave personalizada (opcional)
	 * @return string|false Ruta del archivo encriptado o false si falla
	 */
	public static function encrypt_file($source_path, $dest_path = null, $key = null)
	{
		if (!file_exists($source_path))
		{
			\Log::error("Archivo no encontrado: {$source_path}");
			return false;
		}

		try
		{
			// Leer contenido del archivo
			$file_content = file_get_contents($source_path);
			
			if ($file_content === false)
			{
				throw new \Exception("No se pudo leer el archivo: {$source_path}");
			}

			// Encriptar contenido
			$encrypted_content = self::encrypt($file_content, $key);

			if ($encrypted_content === false)
			{
				throw new \Exception('Error al encriptar contenido del archivo');
			}

			// Determinar ruta de destino
			if (!$dest_path)
			{
				$dest_path = $source_path . '.encrypted';
			}

			// Crear directorio si no existe
			$dest_dir = dirname($dest_path);
			if (!is_dir($dest_dir))
			{
				mkdir($dest_dir, 0755, true);
			}

			// Guardar archivo encriptado
			$bytes_written = file_put_contents($dest_path, $encrypted_content);

			if ($bytes_written === false)
			{
				throw new \Exception("No se pudo escribir archivo encriptado: {$dest_path}");
			}

			\Log::info("Archivo encriptado exitosamente: {$dest_path}");
			return $dest_path;
		}
		catch (\Exception $e)
		{
			\Log::error('Error en Helper_Encryption::encrypt_file: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * DESENCRIPTAR ARCHIVO
	 * 
	 * @param string $encrypted_path Ruta del archivo encriptado
	 * @param string $dest_path Ruta del archivo desencriptado (opcional)
	 * @param string $key Clave personalizada (opcional)
	 * @return string|false Ruta del archivo desencriptado o false si falla
	 */
	public static function decrypt_file($encrypted_path, $dest_path = null, $key = null)
	{
		if (!file_exists($encrypted_path))
		{
			\Log::error("Archivo encriptado no encontrado: {$encrypted_path}");
			return false;
		}

		try
		{
			// Leer contenido encriptado
			$encrypted_content = file_get_contents($encrypted_path);
			
			if ($encrypted_content === false)
			{
				throw new \Exception("No se pudo leer el archivo encriptado: {$encrypted_path}");
			}

			// Desencriptar contenido
			$decrypted_content = self::decrypt($encrypted_content, $key);

			if ($decrypted_content === false)
			{
				throw new \Exception('Error al desencriptar contenido del archivo');
			}

			// Determinar ruta de destino
			if (!$dest_path)
			{
				$dest_path = str_replace('.encrypted', '', $encrypted_path);
			}

			// Crear directorio si no existe
			$dest_dir = dirname($dest_path);
			if (!is_dir($dest_dir))
			{
				mkdir($dest_dir, 0755, true);
			}

			// Guardar archivo desencriptado
			$bytes_written = file_put_contents($dest_path, $decrypted_content);

			if ($bytes_written === false)
			{
				throw new \Exception("No se pudo escribir archivo desencriptado: {$dest_path}");
			}

			\Log::info("Archivo desencriptado exitosamente: {$dest_path}");
			return $dest_path;
		}
		catch (\Exception $e)
		{
			\Log::error('Error en Helper_Encryption::decrypt_file: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * GENERAR HASH SEGURO (para passwords)
	 * 
	 * @param string $password Password en texto plano
	 * @return string Hash bcrypt
	 */
	public static function hash_password($password)
	{
		return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
	}

	/**
	 * VERIFICAR HASH
	 * 
	 * @param string $password Password en texto plano
	 * @param string $hash Hash a verificar
	 * @return bool
	 */
	public static function verify_password($password, $hash)
	{
		return password_verify($password, $hash);
	}

	/**
	 * GENERAR TOKEN ALEATORIO
	 * 
	 * @param int $length Longitud del token (bytes)
	 * @return string Token en hexadecimal
	 */
	public static function generate_token($length = 32)
	{
		return bin2hex(openssl_random_pseudo_bytes($length));
	}

	/**
	 * ENCRIPTAR CERTIFICADO DIGITAL SAT
	 * 
	 * Método especializado para certificados .cer y .key
	 * 
	 * @param string $source_file Ruta del archivo .cer o .key
	 * @param int $tenant_id ID del tenant (para namespace)
	 * @return array ['success' => bool, 'encrypted_path' => string, 'message' => string]
	 */
	public static function encrypt_certificate($source_file, $tenant_id)
	{
		try
		{
			if (!file_exists($source_file))
			{
				return ['success' => false, 'message' => 'Archivo no encontrado'];
			}

			// Obtener extensión y nombre del archivo
			$extension = pathinfo($source_file, PATHINFO_EXTENSION);
			$filename = pathinfo($source_file, PATHINFO_FILENAME);

			// Crear estructura de directorios: fuel/app/tmp/certificates/{tenant_id}/
			$cert_dir = APPPATH . "tmp/certificates/{$tenant_id}/";
			
			if (!is_dir($cert_dir))
			{
				mkdir($cert_dir, 0755, true);
			}

			// Generar nombre único para el archivo encriptado
			$encrypted_filename = $filename . '_' . time() . '.' . $extension . '.encrypted';
			$encrypted_path = $cert_dir . $encrypted_filename;

			// Encriptar archivo
			$result = self::encrypt_file($source_file, $encrypted_path);

			if (!$result)
			{
				return ['success' => false, 'message' => 'Error al encriptar certificado'];
			}

			// Eliminar archivo original por seguridad
			if (file_exists($source_file) && $source_file !== $encrypted_path)
			{
				unlink($source_file);
			}

			return [
				'success' => true,
				'encrypted_path' => $encrypted_path,
				'relative_path' => str_replace(APPPATH, '', $encrypted_path),
				'message' => 'Certificado encriptado correctamente'
			];
		}
		catch (\Exception $e)
		{
			\Log::error('Error en encrypt_certificate: ' . $e->getMessage());
			return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
		}
	}

	/**
	 * DESENCRIPTAR CERTIFICADO DIGITAL SAT
	 * 
	 * @param string $encrypted_path Ruta del archivo encriptado
	 * @param string $dest_path Ruta temporal de salida (opcional)
	 * @return array ['success' => bool, 'decrypted_path' => string, 'message' => string]
	 */
	public static function decrypt_certificate($encrypted_path, $dest_path = null)
	{
		try
		{
			// Si encrypted_path es relativa, convertir a absoluta
			if (strpos($encrypted_path, APPPATH) === false)
			{
				$encrypted_path = APPPATH . $encrypted_path;
			}

			if (!file_exists($encrypted_path))
			{
				return ['success' => false, 'message' => 'Certificado encriptado no encontrado'];
			}

			// Si no se especifica destino, crear archivo temporal
			if (!$dest_path)
			{
				$temp_dir = APPPATH . 'tmp/certificates_temp/';
				if (!is_dir($temp_dir))
				{
					mkdir($temp_dir, 0755, true);
				}

				$dest_path = $temp_dir . uniqid('cert_') . '_' . basename(str_replace('.encrypted', '', $encrypted_path));
			}

			// Desencriptar archivo
			$result = self::decrypt_file($encrypted_path, $dest_path);

			if (!$result)
			{
				return ['success' => false, 'message' => 'Error al desencriptar certificado'];
			}

			return [
				'success' => true,
				'decrypted_path' => $dest_path,
				'message' => 'Certificado desencriptado correctamente'
			];
		}
		catch (\Exception $e)
		{
			\Log::error('Error en decrypt_certificate: ' . $e->getMessage());
			return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
		}
	}

	/**
	 * LIMPIAR ARCHIVOS TEMPORALES DE CERTIFICADOS
	 * 
	 * Elimina archivos temporales desencriptados mayores a X minutos
	 * 
	 * @param int $minutes Edad mínima en minutos para eliminar
	 * @return int Número de archivos eliminados
	 */
	public static function clean_temp_certificates($minutes = 30)
	{
		$temp_dir = APPPATH . 'tmp/certificates_temp/';
		
		if (!is_dir($temp_dir))
		{
			return 0;
		}

		$deleted = 0;
		$cutoff_time = time() - ($minutes * 60);

		$files = glob($temp_dir . '*');
		foreach ($files as $file)
		{
			if (is_file($file) && filemtime($file) < $cutoff_time)
			{
				if (unlink($file))
				{
					$deleted++;
				}
			}
		}

		if ($deleted > 0)
		{
			\Log::info("Limpieza automática: {$deleted} certificados temporales eliminados");
		}

		return $deleted;
	}
}
