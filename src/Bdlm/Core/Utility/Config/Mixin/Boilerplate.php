<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Utility\Config\Mixin;

use \Bdlm\Core\Utility;

/**
 * Getter/setter boilerplate for config instances
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
trait Boilerplate {

	/**
	 * @var Utility\Config\Iface\Base
	 */
	protected $_config = null;

	/**
	 * Get the current config instance
	 *
	 * @return Utility\Config\Iface\Base|false The current config instance, else false
	 */
	public function getConfig() {
		$ret_val = false;
		if ($this->_config instanceof Utility\Config\Iface\Base) {
			$ret_val = $this->_config;
		}
		return $this->_config;
	}

	/**
	 * Set a config instance for this object to reference
	 *
	 * @param Utility\Config\Iface\Base     $config A config instance to reference
	 * @return \Bdlm\Core\Object\Iface\Base         The current instance
	 */
	public function setConfig(Utility\Config\Iface\Base $config) {
		$this->_config = $config;
		return $this;
	}
}
