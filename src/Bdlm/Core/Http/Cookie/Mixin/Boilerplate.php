<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Http\Cookie\Mixin;

use \Bdlm\Core\Http;

/**
 * Getter/setter boilerplate for Cookie instances
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
trait Boilerplate {

	/**
	 * @var Cookie\Iface\Base
	 */
	protected $_cookie = null;

	/**
	 * Get the current coolie instance
	 *
	 * @return Cookie\Ifase\Base The current cookie instance
	 */
	public function getCookie() {
		$ret_val = false;
		if ($this->_cookie instanceof Http\Cookie\Iface\Base) {
			$ret_val = $this->_cookie;
		}
		return $ret_val;
	}

	/**
	 * Set a cookie instance for this object to reference
	 *
	 * @param  Cookie\Iface\Base $cookie A cookie instance to reference
	 * @return Object\Iface\Base The current instance
	 */
	public function setCookie(Http\Cookie\Iface\Base $cookie) {
		$this->_cookie = $cookie;
		return $this;
	}
}
