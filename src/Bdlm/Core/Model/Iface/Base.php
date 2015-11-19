<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Model\Iface;

use \Bdlm\Core;

/**
 * Base model interface
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
interface Base {

	const FETCH_BY_ID = 1;
	const FETCH_BY_NAME = 2;
	const FETCH_BY_COLUMN_VALUE = 3;
	const FETCH_BY_NAMED_QUERY = 4;

	/**
	 * Delete a locally stored value by name
	 *
	 * @param  string $var The variable name
	 * @return Core\Model\Iface\Base
	 * @throws \DomainException If mode is static
	 * @throws \DomainException If mode is fixed and $var is not a valid key
	 */
	public function delete($var);

	/**
	 * Get a locally stored value by name
	 *
	 * @param string $var The variable name
	 * @return mixed
	 */
	public function get($var);

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
	 * Load data relevant to this object
	 *
	 * load() should always return the current instance or throw an exception on
	 * failure.
	 *
	 * @return Core\Model\Iface\Base $this
	 * @throws \Exception on failure
	 */
	public function load();

	/**
	 * Reset any modified values to their original values
	 *
	 * @return Core\Model\Iface\Base $this
	 * @throws \DomainException If mode is static
	 */
	public function reset();

	/**
	 * Save this object's data to appropriate data storage
	 *
	 * save() should always return the current instance or throw an exception on
	 * failure.
	 *
	 * @param  boolean                 $as_new If true, save this object's data as a new record
	 * @return Core\Model\Iface\Base $this
	 * @throws \BadMethodCallException save() has no meaning in this context
	 */
	public function save($as_new = false);

	/**
	 * Store a named value locally
	 *
	 * @param string $var The name of the value
	 * @param mixed $val        The value to store
	 * @return Core\Model\Iface\Base $this
	 * @throws \DomainException If mode is static
	 * @throws \DomainException If mode is fixed and $var is not a valid key
	 */
	public function set($var, $val);

	/**
	 * Set or replace the entire internal data storage array
	 *
	 * @param array $data
	 * @return bool
	 * @throws \DomainException If mode is static
	 * @throws \DomainException If mode is fixed and an existing key is missing from $data
	 * @throws \DomainException If mode is fixed and any of the keys in $data is not a valid key
	 * @throws \DomainException If any value in $data is not a valid type
	 */
	public function setData($data);

	/**
	 * Recursively convert any \Bdlm\Core\ObjectAbstract instances in the internal
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
	public function toJson();

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
