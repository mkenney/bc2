<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\View\Mixin;

use \Bdlm\Core;

/**
 * Getter/setter boilerplate for View instances
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
trait Boilerplate {

	/**
	 * @var Core\View\Iface\Base
	 */
	protected $_view = null;

	/**
	 * Get the current view instance
	 *
	 * @return Core\View\Iface\Base|false The current view instance, else false
	 */
	public function getView() {
		$ret_val = false;
		if ($this->_view instanceof Core\View\Iface\Base) {
			$ret_val = $this->_view;
		}
		return $ret_val;
	}

	/**
	 * Set a view instance for this object to reference
	 *
	 * @param  Core\View\Iface\Base   $view A view instance to reference
	 * @return Core\Object\Iface\Base The current instance
	 */
	public function setView(Core\View\Iface\Base $view) {
		$this->_view = $view;
		return $this;
	}
}
