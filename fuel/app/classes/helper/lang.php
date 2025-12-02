<?php
/**
 * Helper de Idioma
 * 
 * Funciones auxiliares para manejar traducciones
 */

/**
 * Obtener traducción
 * Alias corto para Lang::get()
 * 
 * @param string $key Clave de traducción (ej: 'common.actions.save')
 * @param array $params Parámetros para reemplazar
 * @param string $default Valor por defecto si no existe
 * @return string
 */
if ( ! function_exists('__'))
{
	function __($key, $params = array(), $default = null)
	{
		return \Lang::get($key, $params, $default);
	}
}

/**
 * Obtener traducción y hacer echo
 * 
 * @param string $key Clave de traducción
 * @param array $params Parámetros
 */
if ( ! function_exists('_e'))
{
	function _e($key, $params = array())
	{
		echo __($key, $params);
	}
}

/**
 * Obtener idioma actual
 * 
 * @return string
 */
if ( ! function_exists('get_current_language'))
{
	function get_current_language()
	{
		return \Config::get('language', 'es');
	}
}

/**
 * Cambiar idioma
 * 
 * @param string $lang Código de idioma (es, en)
 * @return bool
 */
if ( ! function_exists('set_language'))
{
	function set_language($lang)
	{
		if (in_array($lang, array('es', 'en')))
		{
			\Config::set('language', $lang);
			\Session::set('language', $lang);
			return true;
		}
		return false;
	}
}

/**
 * Formatear fecha según idioma
 * 
 * @param string|int $date Fecha
 * @param string $format Formato (short, long, time, datetime)
 * @return string
 */
if ( ! function_exists('format_date'))
{
	function format_date($date, $format = 'short')
	{
		$timestamp = is_numeric($date) ? $date : strtotime($date);
		$date_format = \Lang::get('common.date_format.' . $format, array(), 'd/m/Y');
		return date($date_format, $timestamp);
	}
}

/**
 * Pluralizar texto según idioma
 * 
 * @param int $count Cantidad
 * @param string $singular Texto singular
 * @param string $plural Texto plural (opcional)
 * @return string
 */
if ( ! function_exists('pluralize'))
{
	function pluralize($count, $singular, $plural = null)
	{
		if ($count == 1)
		{
			return $count . ' ' . $singular;
		}
		
		if ($plural === null)
		{
			// Reglas simples de pluralización en español
			if (get_current_language() === 'es')
			{
				if (substr($singular, -1) === 's')
				{
					$plural = $singular;
				}
				elseif (in_array(substr($singular, -1), array('a', 'e', 'i', 'o', 'u')))
				{
					$plural = $singular . 's';
				}
				else
				{
					$plural = $singular . 'es';
				}
			}
			else
			{
				// Inglés: agregar 's'
				$plural = $singular . 's';
			}
		}
		
		return $count . ' ' . $plural;
	}
}
