<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Utility;

use \Bdlm\Core;

/**
 * Base config class
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
class Config extends Core\Object implements Config\Iface\Base {

	/**
	 * Path to a config file
	 * @var string
	 */
	protected $_conf_files = [];

	/**
	 * Initialize this config
	 *
	 * @param string $conf_files A path to a config file
	 */
	public function __construct($conf_files = null) {
		if (!is_null($conf_files)) {
			foreach((array) $conf_files as $conf_file) {
				$this->addFile($conf_file);
			}
		}
	}

	/**
	 * [addFile description]
	 * @param  string $conf_file Path to a config file in ini format
	 * @throws \OverflowException If the specified file has already been loaded
	 */
	public function addFile($conf_file) {
		if (!array_key_exists($conf_file, $this->_conf_files)) {
			$this->_conf_files[$conf_file] = false;
		}
		if ($this->_conf_files[$conf_file]) {
			throw new \OverflowException("'{$conf_file}' has already been loaded");
		} else {
			$this->load($conf_file);
			$this->_conf_files[$conf_file] = true;
		}
		return $this;
	}

	/**
	 * Load the specified config file
	 *
	 * load() should always return the current instance or throw an exception on
	 * failure.
	 *
	 * @return Config\Iface\Base
	 */
	public function load($conf_file) {
		if (!is_file($conf_file)) {
			throw new \InvalidArgumentException("The specified file '{$conf_file}' could not be found");
		}
		$this->_conf_files[$conf_file] = true;
		return $this->setData(array_merge($this->getData(), $this->_multiDimentionalIni(parse_ini_file($conf_file, true))))->toObject();
	}

	/**
	 * [getPath description]
	 * @return [type] [description]
	 */
	public function getPath() {
		return $this->_path;
	}

	/**
	 * Set the path to a config file
	 * @param [type] $path [description]
	 */
	public function setPath($path) {
		$this->_path = (string) $path;
		return $this;
	}

	/**
	 * Advanced INI parsing
	 *
	 * parse_ini_file() returns a 1-dimentional array.  Parse the keys out into
	 * child arrays, splitting on '.'
	 *
	 * @param  array $array The 1-dimentional array to parse
	 * @return array        The resulting multi-dimentional array
	 */
	protected function _multiDimentionalIni($array) {

		foreach ($array as $key => $value) {

			if (is_array($value)) {
				$value = $this->_multiDimentionalIni($value);
				$array[$key] = $value;
			}

			if (false !== strpos($key, '.')) {
				$parts = explode('.', $key);

				// Set the root key separately
				if (!isset($array[$parts[0]])) {
					$array[$parts[0]] = [];
				}

				// Walk the parts and create child arrays
				$node =& $array[$parts[0]];
				if (!is_array($node)) {throw new \Exception("Syntax error in {$this->getPath()}: '$node' is not a valid key");}
				for ($a = 1; $a < count($parts); $a++) {

					// Create the key and child array, then step down into the
					// next dimension
					$part = $parts[$a];
					if (!isset($node[$part])) {$node[$part] = [];}
					$node = &$node[$part];
				}

				// Migrate the value to it's new postion, delete the old key
				$node = $value;
				unset($array[$key]);
			}
		}

		return $array;
	}
}
