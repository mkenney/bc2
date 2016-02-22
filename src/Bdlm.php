<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

/**
 * Provide a global namespace for
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
class Bdlm {

	/**
	 * Config file manager
	 * @var \Bdlm\Core\Util\Config\Iface\Base
	 */
	protected static $config = null;

	/**
	 * Generic data storage
	 * @var \Bdlm\Core\Object\Iface\Base
	 */
	public static $data = null;

	/**
	 * Primary application datasource
	 * @var \Bdlm\Core\Datasource\Iface\Base
	 */
	public static $ds = null;

	/**
	 * Initialize objects that don't require configuration
	 * @return void
	 */
	public static function init() {
		if (!self::$config instanceof \Bdlm\Core\Util\Config\Iface\Base) {
			self::$config = new \Bdlm\Core\Util\Config();
		}
		if (!self::$data instanceof \Bdlm\Core\Object\Iface\Base) {
			self::$data = new \Bdlm\Core\Object();
		}
	}

	/**
	 * Get / set a configuration object instance
	 * @param  \Bdlm\Core\Util\Config\Iface\Base $config A config object instance
	 * @return \Bdlm\Core\Util\Config\Iface\Base
	 */
	public static function config($config = null) {
		if ($config instanceof \Bdlm\Core\Util\Config\Iface\Base) {
			self::$config = $config;
		}
		if (is_null(self::$config)) {
			self::$config = new \Bdlm\Core\Object();
		}
		return self::$config;
	}

	/**
	 * Storage for the default Datasource interface
	 * @todo   support multiple Datasource instances
	 * @param  \Bdlm\Core\Datasource\Iface\Base $ds A datasource instance
	 * @return \Bdlm\Core\Datasource\Iface\Base
	 */
	public static function ds(\Bdlm\Core\Datasource\Iface\Base $ds = null) {
		if (!is_null($ds)) {
			self::$ds = $ds;
		}
		if (is_null(self::$ds)) {
			throw new \RuntimeException('A datasource has not been set');
		}
		return self::$ds;
	}

	/**
	 * Get a locally stored value by name
	 * @param  string $var The variable name
	 * @return mixed
	 */
	public static function get($var) {
		return self::$data->get($var);
	}

	/**
	 * Delete a locally stored value by name
	 * @param  string $var The variable name
	 * @return mixed
	 */
	public static function delete($var) {
		return self::$data->delete($var);
	}

	/**
	 * See if a locally stored value exists
	 * @param  string $var The variable name
	 * @return mixed
	 */
	public static function has($var) {
		return self::$data->has($var);
	}

	/**
	 * Store a named value locally
	 * @param  string    $var   The name of the value
	 * @param  mixed     $val   The value to store
	 * @return Core\Object\Iface\Base $this
	 * @throws \DomainException If mode is static
	 * @throws \DomainException If mode is fixed and $var is not a valid key
	 */
	public static function set($var, $val) {
		return self::$data->set($var, $val);
	}
}
