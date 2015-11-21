<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Util\Encryption\Mixin;

use Bdlm\Core\Util;

/**
 * Getter/setter boilerplate for Model instances
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
trait Boilerplate {

	/**
	 * @var Util\Encryption\Iface\Base
	 */
	protected $_encryption = null;

	/**
	 * Access the local encryption instance
	 *
	 * @return Util\Encryption\Iface\Base|false The current instance, else false
	 */
	public function getEncryption() {
		$ret_val = false;
		if ($this->_encryption instanceof Util\Encryption\Iface\Base) {
			$ret_val = $this->_encryption;
		}
		return $ret_val;
	}

	/**
	 * Set a local encryption instance
	 *
	 * @return Object\Iface\Base The current instance
	 */
	public function setEncryption(Util\Encryption\Iface\Base $encryption) {
		$this->_encryption = $encryption;
		return $this;
	}
}
