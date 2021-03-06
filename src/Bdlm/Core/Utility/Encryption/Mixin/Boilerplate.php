<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Utility\Encryption\Mixin;

use Bdlm\Core\Utility;

/**
 * Getter/setter boilerplate for Model instances
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
trait Boilerplate {

	/**
	 * @var Utility\Encryption\Iface\Base
	 */
	protected $_encryption = null;

	/**
	 * Access the local encryption instance
	 *
	 * @return Utility\Encryption\Iface\Base|false The current instance, else false
	 */
	public function getEncryption() {
		$ret_val = false;
		if ($this->_encryption instanceof Utility\Encryption\Iface\Base) {
			$ret_val = $this->_encryption;
		}
		return $ret_val;
	}

	/**
	 * Set a local encryption instance
	 *
	 * @return Object\Iface\Base The current instance
	 */
	public function setEncryption(Utility\Encryption\Iface\Base $encryption) {
		$this->_encryption = $encryption;
		return $this;
	}
}
