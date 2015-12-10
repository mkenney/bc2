<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Object\Iface;

use \Bdlm\Core;

/**
 * Object class interface
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.1.0
 */
interface Base {

    /**
     * Create a list of values or add a value to an existing list.
     *
     * If the existing data for the given key '$var' is already an array created
     * using the set() method the new data will be added to that list rather than
     * the existing list being converted to a single entry in a new list.
     *
     * This could lead to unexpected behavior if you're not paying attention.
     *
     * @param  string            $var The name of the value
     * @param  mixed             $val The value to store
     * @return Core\Object\Iface\Base $this
     * @throws \DomainException  If mode is static
     * @throws \DomainException  If mode is fixed and $var is not a valid key
     */
    public function add($var, $val);

    /**
     * Because ArrayAccess doesn't support array_keys()
     * @return array
     */
    public function arrayKeys();

    /**
     * Delete a locally stored value by name
     *
     * @param string $var The variable name
     * @return Core\Object\Iface\Base $this
     * @throws \DomainException If mode is static
     * @throws \DomainException If mode is fixed and $var is not a valid key
     */
    public function delete($var);

    /**
     * Get a locally stored value by name
     *
     * @param string $var The variable name
     * @return mixed|null The current value, else null
     */
    public function get($var);

    /**
     * Get all defined constants
     *
     * @return array
     */
    public function getConstants();

    /**
     * Get the internal data array
     *
     * Wouldn't be necessary with all the iterator/ArrayAccess/etc. stuff but
     * some PHP functions will only accept an array, so... :(
     *
     * Could use toArray() instead but that's recursive and so could be slow.
     *
     * @return array
     */
    public function getData();

    /**
     * Get the max boundary property
     *
     * Throw an exception if "max" has no meaning in your class.
     *
     * @return int|false Current max value else false
     */
    public function getMax();

    /**
     * Get the min boundary property
     *
     * Throw an exception if "min" has no meaning in your class.
     *
     * @return int|false Current min value else false
     */
    public function getMin();

    /**
     * Get the object mode property
     *
     * The object mode may be one of:
     *  - 'list'
     *  - 'fixed'
     *  - 'singleton'
     *
     * Singleton mode should make objects act as an array with one and only one element.
     * Fixed mode should make objects act as an array with fixed keys.
     * List mode is the default behavior
     *
     * Throw an exception if "mode" has no meaning in your class.
     *
     * @return string|false A valid mode else false
     */
    public function getMode();

    /**
     * Get the object name property
     *
     * Throw an exception if "name" has no meaning in your class.
     *
     * @return string|null
     */
    public function getName();

    /**
     * Get the "type" property
     *
     * @return string|false The current type value, else false
     */
    public function getType($type = null);

    /**
     * Check to see if a value has been set
     *
     * @param string $var The variable name
     * @return bool True if set, else false
     */
    public function has($var);

    /**
     * Check to see if a value should be considered "empty"
     *
     * The empty() call is wrapped to trap false-positives for the string '0'
     * (http://php.net/empty).  If this is called with no arguments it checks to
     * see if any data has been stored yet.
     *
     * @param string $var The variable name
     * @return bool True if empty, else false
     */
    public function isEmpty($var = null);

    /**
     * Set/get read-only flag for this object
     *
     * If true (static) this object becomes read-only
     *
     * @param bool $static
     * @return bool
     */
    public function isStatic($is_static = null);

    /**
     * Return the number of records in the current object
     *
     * @return int
     */
    public function length();

    /**
     * Delete all locally stored values
     *
     * @return Core\Object\Iface\Base $this
     * @throws \DomainException If mode is static
     */
    public function reset();

    /**
     * Store a named value locally
     *
     * @param string $var The name of the value
     * @param mixed $val         The value to store
     * @return Core\Object\Iface\Base $this
     * @throws \DomainException  If mode is static
     * @throws \DomainException  If mode is fixed and $var is not a valid key
     */
    public function set($var, $val);

    /**
     * Set or replace the entire internal data storage array
     *
     * @param array $data
     * @return Core\Object\Iface\Base $this
     * @throws \DomainException If mode is static
     * @throws \DomainException If mode is fixed and an existing key is missing from $data
     * @throws \DomainException If mode is fixed and any of the keys in $data is not a valid key
     * @throws \DomainException If any value in $data is not a valid type
     */
    public function setData($data);

    /**
     * Set the max boundary property
     *
     * Return $this to chain calls
     *
     * Throw an exception if "max" has no meaning in your class.
     *
     * @param int $max
     * @return Core\Object\Iface\Base $this
     * @throws \InvalidArgumentException If $max is smaller than $min or is otherwise invalid
     */
    public function setMax($max);

    /**
     * Set the min boundary property
     *
     * Return $this to chain calls
     *
     * Throw an exception if "min" has no meaning in your class.
     *
     * @param  float $min
     * @return Core\Object\Iface\Base $this
     * @throws \InvalidArgumentException If $min is greater than $max or is otherwise invalid
     */
    public function setMin($min);

    /**
     * Set the object mode property
     *
     * The object mode may be one of:
     *  - 'list'
     *  - 'fixed'
     *  - 'singleton'
     *
     * Singleton mode should make objects act as an array with one and only one element.
     * Fixed mode should make objects act as an array with fixed keys.
     * List mode is the default behavior
     *
     * Return $this to chain calls
     *
     * Throw an exception if "mode" has no meaning in your class.
     *
     * @param string $mode
     * @return Core\Object\Iface\Base $this
     * @throws \InvalidArgumentException If $mode is not a valid value
     */
    public function setMode($mode);

    /**
     * Set the object name property
     *
     * Return $this to chain calls
     *
     * Throw an exception if "name" has no meaning in your class.
     *
     * @param string $name
     * @return Core\Object\Iface\Base $this
     * @throws \InvalidArgumentException
     */
    public function setName($name);

    /**
     * Set the "type" property
     *
     * @param  string $type
     * @return Core\Object\Iface\Base $this
     * @throws \RuntimeException If the type property has already been set
     * @throws \DomainException  If the $type value is empty or not a string
     * @throws \DomainException  If the $type value an invalid type
     * @throws \DomainException  If any data that has already been stored is not of type $type
     */
    public function setType($type);

    /**
     * Recursively convert any Object\Iface\Base instances in the internal
     * data storage array to an array and return the result
     *
     * @return array
     * @throws \Exception
     */
    public function toArray($array = null);

    /**
     * Recursively convert the internal data storage array to a JSON string
     *
     * @return string JSON
     * @throws \Exception
     */
    public function toJson($options = 0, $depth = 512);

    /**
     * Recursively convert stored arrays to Object instances
     *
     * @return string Object\Iface\Base
     */
    public function toObject($data = null);

    /**
     * Convert the data array to a text representation, default JSON
     *
     * @return string
     * @throws \Exception
     */
    public function toString();

    /**
     * Recursively convert the internal data storage array to an XML compatible
     * string
     *
     * @return string XML
     * @throws \Exception
     */
    public function toXml($array = null);
}
