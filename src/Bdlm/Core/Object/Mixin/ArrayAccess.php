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
 * Implementations for \ArrayAccess
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 1.8.0
 */
trait ArrayAccess {

    /**
     * ArrayAccess implementation of offsetExists()
     * @param mixed $var
     * @return bool
     */
    final public function offsetExists($var) {
        return $this->has($var);
    }

    /**
     * ArrayAccess implementation of offsetGet()
     * @param mixed $var
     * @return mixed
     */
    final public function offsetGet($var) {
        return $this->get($var);
    }

    /**
     * ArrayAccess implementation of offset()
     * @param mixed $var
     * @param mixed $val
     * @return void
     */
    final public function offsetSet($var, $val) {
        $this->set($var, $val);
    }

    /**
     * ArrayAccess implementation of offsetUnset()
     * @param mixed $var
     * @return void
     */
    final public function offsetUnset($var) {
        $this->delete($var);
    }
}
