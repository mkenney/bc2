<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Controller\Mixin;

use \Bdlm\Core;

/**
 * Getter/setter boilerplate for Controller instances
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
trait Boilerplate {

	/**
	 * @var Controller\Iface\Base
	 */
	protected $_controller = null;

	/**
	 * Get the current controller instance
	 *
	 * @return \Bdlm\Core\Controller|false The current controller instance, else false
	 */
	public function getController() {
		$ret_val = false;
		if ($this->_controller instanceof Controller\Iface\Base) {
			$ret_val = $this->_controller;
		}
		return $ret_val;
	}

	/**
	 * Set a controller instance for this object to reference
	 *
	 * @param  Controller\Iface\Base  $controller A controller instance to reference
	 * @return Core\Object\Iface\Base             The current instance
	 */
	public function setController(Controller\Iface\Base $controller) {
		$this->_controller = $controller;
		return $this;
	}
}
