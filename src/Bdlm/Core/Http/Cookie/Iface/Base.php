<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Http\Cookie\Iface;

use \Bdlm\Core;

/**
 * Cookie object interface
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.1.1
 */
interface Base extends Core\Object\Iface\Base {

	/**
	 * Get the cookie domain
	 * @return string
	 */
	public function getDomain();

	/**
	 * Set the cookie domain
	 * @param  string $domain
	 * @return Bdlm\Core\Http\Cookie\Iface
	 */
	public function setDomain($domain);

	/**
	 * Get the cookie HTTPOnly flag
	 * @return bool
	 */
	public function getHttpOnly();

	/**
	 * Set the cookie HTTPOnly flag
	 * @param bool $httponly
	 * @return Cookie\Iface\Base
	 */
	public function setHttpOnly($httponly);

	/**
	 * Get the cookie name property
	 * @return string
	 */
	public function getName();

	/**
	 * Set the cookie name property
	 * @param string $name
	 * @return Cookie\Iface\Base
	 * @throws \InvalidArgumentException
	 */
	public function setName($name);

	/**
	 * Get the cookie path value
	 * @return string
	 */
	public function getPath();

	/**
	 * Set the cookie path value
	 * @param  string $path
	 * @return Bdlm\Core\Http\Cookie\Iface
	 */
	public function setPath($path);

	/**
	 * Get the cookie secure flag
	 * @return bool
	 */
	public function getSecure();

	/**
	 * Set the cookie secure flag
	 * @param  bool $secure
	 * @return Bdlm\Core\Http\Cookie\Iface
	 */
	public function setSecure($secure);

	/**
	 * Get the cookie timeout value
	 * @return  int The timeout time stamp in seconds
	 */
	public function getTimeout();

	/**
	 * Set the cookie timeout value
	 * A value of 0 creates a session cookie
	 * @param  int $timeout                A future timestamp specifying when
	 *                                     this cookie should expire. A timestamp
	 *                                     in the past will expire this cookie
	 *                                     immediately
	 * @return Bdlm\Core\Http\Cookie\Iface
	 */
	public function setTimeout($timeout);

	/**
	 * Set the future timeout timestamp by specifying a number of seconds relative
	 * to now. Convience method.
	 * @param  int $seconds                The number of seconds this cookie should
	 *                                     exist for
	 * @return Bdlm\Core\Http\Cookie\Iface
	 */
	public function timeoutIn($seconds);

}