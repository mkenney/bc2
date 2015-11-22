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
 * Implementations for \Iterator
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.1.0
 */
trait Iterator {

    /**
     * Iterator implementation for current()
     * @return bool|mixed See http://php.net/current
     */
    public function current() {
        return current($this->_data);
    }

    /**
     * Iterator implementation for each()
     * @return bool|mixed See http://php.net/each
     */
    public function each() {
        return each($this->_data);
    }

    /**
     * Iterator implementation for end()
     * @return mixed See http://php.net/end
     */
    public function end() {
        return end($this->_data);
    }

    /**
     * Iterator implementation for key()
     * @return mixed See http://php.net/key
     */
    public function key() {
        return key($this->_data);
    }

    /**
     * Iterator implementation for next()
     * @return bool|mixed See http://php.net/next
     */
    public function next() {
        return next($this->_data);
    }

    /**
     * Iterator implementation for prev()
     * @return bool|mixed See http://php.net/prev
     */
    public function prev() {
        return prev($this->_data);
    }

    /**
     * Iterator implementation for rewind()
     * @return bool|mixed See http://php.net/rewind
     */
    public function rewind() {
        return reset($this->_data);
    }

    /**
     * Iterator implementation for valid()
     *
     * If an array key is false (by calling Object::set(false, 'someval')) foreach
     * loops would be endless because $this->key() returns false when past the
     * end of the array. That would still be better than the recommended method
     * from http://php.net/manual/en/language.oop5.iterations.php which will end
     * the loop if any _value_ === false.
     *
     * Because of this, the set method only allows strings as keys and this method
     * uses array_key_exists (catches false keys and null values correctly)
     *
     * @see http://php.net/manual/en/class.iterator.php
     * @return bool True if valid else false
     */
    public function valid() {
        return array_key_exists($this->key(), $this->getData());
    }
}
