<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Http\Cookie\Mixin;

use \Bdlm\Core\Http\Cookie;

/**
 * Getter/setter boilerplate for Cookie instances
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
trait Base {

    /**
     * The cookie name
     * @var string
     */
    protected $_name = null;

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
     * Get the cookie domain
     * @return string
     */
    final public function getDomain($domain = null) {
        return $this->_domain;
    }

    /**
     * Get the cookie HTTPOnly flag
     * @return bool
     */
    final public function getHttpOnly() {
        return (bool) $this->_httponly;
    }

    /**
     * Get the object name property
     * @return string|null
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * Get the cookie path value
     * @return string
     */
    final public function getPath() {
        return (string) $this->_path;
    }

    /**
     * Get the cookie secure flag
     * @return bool
     */
    final public function getSecure() {
        return (bool) $this->_secure;
    }

    /**
     * Get the cookie timeout value
     * @return  int The timeout time stamp in seconds
     */
    final public function getTimeout() {
        return (int) $this->_timeout;
    }

    /**
     * Set the cookie domain
     * @param string $domain
     * @return Cookie\Iface\Base
     */
    final public function setDomain($domain) {
        $this->_domain = trim($domain);
        return $this;
    }

    /**
     * Set the cookie HTTPOnly flag
     * @param bool $httponly
     * @return Cookie\Iface\Base
     */
    final public function setHttpOnly($httponly) {
        $this->_httponly = (bool) $httponly;
        return $this;
    }

    /**
     * Set the object name property
     * @param string $name
     * @return Cookie\Iface\Base
     * @throws \InvalidArgumentException
     */
    public function setName($name) {
        if (method_exists($this, 'isValidName') && !$this->isValidName($name)) {
            throw new \InvalidArgumentException("'$name' is not a valid name");
        }
        $this->_name = (string) $name;
        return $this;
    }

    /**
     * Set the cookie path value
     * @param  string $path
     * @return Bdlm\Core\Http\Cookie\Iface
     */
    final public function setPath($path = null) {
        $this->_path = trim($path);
        return $this;
    }

    /**
     * Set the cookie secure flag
     * @param  bool $secure
     * @return Bdlm\Core\Http\Cookie\Iface
     */
    final public function setSecure($secure) {
        $this->_secure = (bool) $secure;
        return $this;
    }

    /**
     * Set the cookie timeout value
     * A value of 0 creates a session cookie
     * @param  int $timeout                A future timestamp specifying when
     *                                     this cookie should expire. A timestamp
     *                                     in the past will expire this cookie
     *                                     immediately
     * @return Bdlm\Core\Http\Cookie\Iface
     */
    final public function setTimeout($timeout) {
        $this->_timeout = (int) $timeout;
        return $this;
    }

    /**
     * Set the future timeout timestamp by specifying a number of seconds relative
     * to now. Convience method.
     * @param  int $seconds                The number of seconds this cookie should
     *                                     exist for
     * @return Bdlm\Core\Http\Cookie\Iface
     */
    final public function timeoutIn($seconds) {
        $this->_timeout = (int) $seconds + time();
        return $this;
    }
}
