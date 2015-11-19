<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Object\Mixin;

/**
 * Implementations for \Bdlm\Core\Object\Iface\Magic
 *
 * For use with classes that implement \Bdlm\Core\Object\Iface\Magic or similar
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.1.0
 */
trait Magic {

	/**
	 * @return mixed
	 */
	final public function __get($var) {
		return $this->get($var);
	}

	/**
	 * @return bool
	 */
	final public function __isset($var) {
		return $this->has($var);
	}

	/**
	 * @return void
	 */
	final public function __set($var, $val) {
		$this->set($var, $val);
	}

	/**
	 * Alias the API implementation if available
	 * @return string
	 */
	final public function __toString() {
		$ret_val = '';
		if (method_exists($this, 'toString')) {
			$ret_val = $this->toString();
		}
		return $ret_val;
	}

	/**
	 * @return void
	 */
	final public function __unset($var) {
		$this->delete($var);
	}
}
