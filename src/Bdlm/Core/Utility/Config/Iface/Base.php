<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Utility\Config\Iface;

use \Bdlm\Core\Utility;

/**
 * Base config interface
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
interface Base {

	/**
	 * Get a config value by name
	 *
	 * @param string $var The variable name
	 * @return mixed
	 */
	public function get($var);

	/**
	 * Load data relevant to this object
	 *
	 * load() should always return the current instance or throw an exception on
	 * failure.
	 *
	 * @return \Bdlm\Core\Object
	 * @throws \BadMethodCallException load() has no meaning in this context
	 */
	public function load($file);

	public function getPath();

	public function setPath($path);

	/**
	 * Store a config value locally
	 *
	 * @param string $var The name of the value
	 * @param mixed $val        The value to store
	 * @return \Bdlm\Core\Object     $this
	 */
	public function set($var, $val);

}
