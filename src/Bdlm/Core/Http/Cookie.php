<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Http;

use Bdlm\Core;

/**
 * Cookie management object
 *
 * This is nice because if you do all your cookie interaction through here then
 * your cookie data is available immediately without a page refresh.
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 1.0.0
 */
class Cookie extends Core\Object implements Cookie\Iface\Base {

	/**
	 * The cookie path
	 * @var string
	 */
	public $_path = '/';

	/**
	 * The cookie timeout in seconds, default to 1 day
	 * @var int
	 */
	public $_timeout = 86400;

	/**
	 * The cookie domain
	 * @var string
	 */
	public $_domain = '';

	/**
	 * Require SSL encryption
	 * @var bool
	 */
	public $_secure = false;

	/**
	 * HTTPOnly flag
	 * @var bool
	 */
		public $_httponly = true;

	/**
	 * Setup all the defaults and initialize
	 *
	 * @param  string $name      The name of the cookie
	 * @param  int    $timeout   Cookie expire date, Unix timestamp in number of seconds
	 * @param  string $path      The server path the cookie will be available on
	 * @param  string $domain    The domain that the cookie is available to
	 * @param  bool   $secure    If true, cookie data will only be passed on over a secure HTTPS connection
	 * @param  bool   $http_only If true, cookie data won't be accessible by JavaScript
	 * @return Cookie\Iface\Base
	 */
	public function __construct(
		$name = '',
		$timeout = 86400,
		$path = '/',
		$domain = null,
		$secure = false,
		$http_only = true
	) {

		//
		// Initialize/store cookie variables
		// @todo input validation on names and values
		//
		$this->setName(trim($name));
		$this->timeout((int) $timeout);
		$this->path(trim($path));
		$this->domain(trim($domain));
		$this->secure((bool) $secure);
		$this->httponly((bool) $http_only);
		parent::__construct(
			'' === $this->getName()
				? $_COOKIE
				: (
					isset($_COOKIE[$this->getName()])
						? $_COOKIE[$this->getName()] // Something isn't right about this.  Can you see it?  I can.  It smells funny.
						: null
				)
		);
	}

	/**
	 * Delete a cookie value
	 * @param string $var The name of the value to be deleted
	 * @return Cookie\Iface\Base
	 * @throws \RuntimeException
	 */
	public function delete($var) {

		// Try to delete the specified cookie value
		if (
			!setcookie(
				$this->name()."[$var]"
				, false
				, time() - 86400
				, $this->path()
				, $this->domain()
				, $this->secure()
			)
		) {
			throw new \RuntimeException("Could not delete cookie ($this->name[$var])");
		}

		// Update local value
		return parent::delete($var);
	}

	/**
	 * Get/set the cookie domain
	 * @param string $domain
	 * @return string
	 */
	final public function domain($domain = null) {
		if (!is_null($domain)) {
			$this->_domain = trim($domain);
		}
		return $this->_domain;
	}

	/**
	 * HTTPOnly get/set wrapper
	 * @param bool $httponly
	 * @return bool
	 */
	final public function httponly($httponly = null) {
		if (is_null($httponly)) {
			$this->_httponly = (bool) $httponly;
		}
		return $this->_httponly;
	}

	/**
	 * Get/set the cookie path
	 * @param string $path
	 * @return string
	 */
	final public function path($path = null) {
		if (!is_null($path)) {
			$this->_path = trim($path);
		}
		return $this->_path;
	}

	/**
	 * Delete all cookies accessable from this cookie object
	 * @return Cookie\Iface\Base
	 */
	final public function reset() {

		// Loop through cookie values deleting each one
		// Don't just loop through $this or the each() call gets offset
		// as you delete keys and you can potentially skip some.
		foreach ($this->getData() as $var) {
			$this->delete($var);
		}
		return parent::reset();
	}

	/**
	 * Secure get/set wrapper
	 * @param bool $secure
	 * @return bool
	 */
	final public function secure($secure = null) {
		if (is_null($secure)) {
			$this->_secure = (bool) $secure;
		}
		return $this->_secure;
	}

	/**
	 * Wrap the \Bdlm\Core\Object setter to accommodate the timeout override argument
	 *
	 * @param  string $var     The key name
	 * @param  string $val     The value
	 * @param  int    $timeout The cookie timeout in seconds
	 * @return bool
	 */
	public function set($var, $val, $timeout = null) {

		// Init return value
		$ret_val = false;

		// Set the cookie timeout.  If the timeout value is 0, use a session cookie.
		if (!is_null($timeout)) {
			$timeout = ((int) $timeout > 0
				? time() + (int) $timeout
				: 0
			);
		} else {
			$timeout = ((int) $this->timeout() > 0
				? (int) $this->timeout()
				: 0
			);
		}

		// Set the cookie value
		//echo 'setcookie('.$this->name.'['.$var.']'.', '.$val.', '.$timeout.', '.$this->path().', '.$this->domain().', '.$this->secure().");\n";
		if (
			setcookie(
				"{$this->name()}[{$var}]"
				, $val
				, $timeout
				, $this->path()
				, $this->domain()
				, $this->secure()
				, $this->httponly()
			)
		) {

			// Update local values
			$ret_val = parent::set($var, $val);
		}

		return $ret_val;
	}

	/**
	 * Get/set the default cookie timeout
	 * @param int $timeout
	 * @return int
	 */
	final public function timeout($timeout = null) {
		if (!is_null($timeout)) {

			$timeout = (int) $timeout;

			// Create the timeout timestamp, (a value of 0 creates a session cookie)
			if ($timeout > 0) {
				$timeout += time();
			}

			$this->_timeout = $timeout;
		}
		return (int) $this->_timeout;
	}
}
