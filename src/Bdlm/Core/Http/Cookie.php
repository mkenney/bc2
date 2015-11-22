<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Http;
use Bdlm\Core\Object;

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
class Cookie implements
    Object\Iface\Base
    , Cookie\Iface\Base
    , Core\Object\Iface\Magic
    , \ArrayAccess
    , \Countable
    , \Iterator
    , \Serializable
{

    /**
     * Object\Iface\Base implementation
     */
    use Object\Mixin\Base {
        Object\Mixin\Base::delete as protected _coreDelete;
        Object\Mixin\Base::reset  as protected _coreReset;
        Object\Mixin\Base::set    as protected _coreSet;
    }

    /**
     * Http\Cookie\Iface\Base implementation
     */
    use Cookie\Mixin\Base {
        Cookie\Mixin\Base::getName insteadof Object\Mixin\Base;
        Cookie\Mixin\Base::setName insteadof Object\Mixin\Base;
    }

    /**
     * ArrayAccess
     */
    use Core\Object\Mixin\ArrayAccess;

    /**
     * Countable
     */
    use Core\Object\Mixin\Countable;

    /**
     * Iterator
     */
    use Core\Object\Mixin\Iterator;

    /**
     * Magic
     */
    use Core\Object\Mixin\Magic;

    /**
     * Serializable
     */
    use Core\Object\Mixin\Serializable;

    /**
     * Setup all the defaults and initialize
     *
     * @param  string $name      The name of the cookie
     * @param  int    $timeout   Cookie lifetime in seconds
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
        $this->setTimeout(time() + (int) $timeout);
        $this->setPath(trim($path));
        $this->setDomain(trim($domain));
        $this->setSecure((bool) $secure);
        $this->setHttpOnly((bool) $http_only);

        if ('' === $this->getName()) {
            $this->setData($_COOKIE);
        } elseif (isset($_COOKIE[$this->getName()])) {
            $this->setData($_COOKIE[$this->getName()]);
        }
    }

    /**
     * Delete a cookie value
     * Overloads \Bdlm\Core\Object::delete
     * @param string $var The name of the value to be deleted
     * @return Cookie\Iface\Base
     * @throws \RuntimeException
     */
    public function delete($var) {

        // Try to delete the specified cookie value
        if (
            !setcookie(
                $this->getName()."[$var]"
                , false
                , time() - 86400
                , $this->getPath()
                , $this->getDomain()
                , $this->getSecure()
            )
        ) {
            throw new \RuntimeException("Could not delete cookie ($this->getName[$var])");
        }

        // Update local value
        return $this->_coreDelete($var);
    }

    /**
     * Delete all cookies accessable from this cookie object
     * Overloads \Bdlm\Core\Object::reset
     * @return Cookie\Iface\Base
     */
    final public function reset() {

        // Loop through cookie values deleting each one
        // Don't just loop through $this or the each() call gets offset
        // as you delete keys and you can potentially skip some.
        foreach ($this->getData() as $var) {
            $this->delete($var);
        }
        return $this->_coreReset();
    }

    /**
     * Wrap the \Bdlm\Core\Object setter to accommodate the timeout override argument
     * Overloads \Bdlm\Core\Object::set
     * @param  string $var            The key name
     * @param  string $val            The value
     * @param  int    $timeout        The cookie timeout in seconds
     * @return bool|Cookie\Iface\Base False on failure else $this
     */
    public function set($var, $val, $timeout = null) {
        $var = (string) $var;
        $val = (string) $val;

        // Init return value
        $ret_val = false;

        // Set the cookie timeout.  If the timeout value is 0, use a session cookie.
        if (!is_null($timeout)) {
            $timeout = (int) $timeout;
        } else {
            $timeout = $this->getTimeout();
        }

        // Set the cookie value
//debug(<<<txt
//setcookie(
//    {$this->getName()}[{$var}]
//    , {$val}
//    , {$timeout}
//    , {$this->getPath()}
//    , {$this->getDomain()}
//    , {$this->getSecure()}
//);
//txt
//);
        if (
            setcookie(
                "{$this->getName()}[{$var}]"
                , $val
                , $timeout
                , $this->getPath()
                , $this->getDomain()
                , $this->getSecure()
                , $this->getHttpOnly()
            )
        ) {

            // Update local values
            $this->_coreSet($var, $val);
            $ret_val = $this;
        }
        return $ret_val;
    }
}
