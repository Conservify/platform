<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Core
 *
 * Handle plugin loading
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

abstract class Ushahidi_Core {
	/**
	 * Initializes Ushahidi and Plugins
	 */
	public static function init()
	{
		/**
		 * 1. Plugin Registration Listener
		 */
		Event::instance()->listen(
			'Ushahidi_Plugin',
			function ($event, $params)
			{
				Ushahidi::register($params);
			}
		);

		/**
		 * 2. Load the plugins
		 */
		self::load();
	}

	/**
	 * Load All Plugins Into System
	 */
	public static function load()
	{
		if (! is_dir(PLUGINPATH)) return;
var_dump(PLUGINPATH, realpath(PLUGINPATH));
		// Load Plugins
		$results = scandir(PLUGINPATH);
		foreach ($results as $result)
		{
			if ($result === '.' or $result === '..') continue;

			if (is_dir(PLUGINPATH.$result))
			{
				Kohana::modules( Kohana::modules() + array($result => PLUGINPATH.$result) );
			}
		}
		var_dump(Kohana::modules());
	}

	/**
	 * Register A Plugin
	 *
	 * @param array $params
	 */
	public static function register($params)
	{
		if (self::valid_plugin($params))
		{
			$config = Kohana::$config->load('_plugins');
			$config->set(key($params), $params[key($params)]);
		}
	}

	/**
	 * Validate Plugin Parameters
	 *
	 * @param array $params
	 * @return bool valid/invalid
	 */
	public static function valid_plugin($params)
	{
		$path = array_keys($params);
		$path = $path[0];

		if ( ! is_array($params) )
		{
			return FALSE;
		}

		// Validate Name
		if ( ! isset($params[$path]['name']) )
		{
			Kohana::$log->add(Log::ERROR, __("':plugin' does not have 'name'", array(':plugin' => $path)));
			return FALSE;
		}

		// Validate Version
		if ( ! isset($params[$path]['version']) )
		{
			Kohana::$log->add(Log::ERROR, __("':plugin' does not have 'version'", array(':plugin' => $path)));
			return FALSE;
		}

		// Validate Services
		if ( ! isset($params[$path]['services']) OR ! is_array($params[$path]['services']) )
		{
			Kohana::$log->add(Log::ERROR, __("':plugin' does not have 'services' or 'services' is not an array", array(':plugin' => $path)));
			return FALSE;
		}

		// Validate Options
		if ( ! isset($params[$path]['options']) OR ! is_array($params[$path]['options']) )
		{
			Kohana::$log->add(Log::ERROR, __("':plugin' does not have 'options' or 'options' is not an array", array(':plugin' => $path)));
			return FALSE;
		}

		// Validate Links
		if ( ! isset($params[$path]['links']) OR ! is_array($params[$path]['links']) )
		{
			Kohana::$log->add(Log::ERROR, __("':plugin' does not have 'links' or 'links' is not an array", array(':plugin' => $path)));
			return FALSE;
		}

		return TRUE;
	}
}