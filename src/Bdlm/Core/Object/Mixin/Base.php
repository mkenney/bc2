<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Object\Mixin;
use \Bdlm\Core;

/**
 * Implementations for \Bdlm\Core\Object\Iface\Base
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.1.0
 */
trait Base {

	/**
	 * Local data storage
	 * @var array $_data
	 */
	protected $_data = [];

	/**
	 * The read vs read/write mode.
	 * If true, set/add/save methods should fail
	 */
	protected $_is_static = false;

	/**
	 * Optional, max length/value for this data
	 * @var int $_max
	 */
	protected $_max = null;

	/**
	 * Optional, min length/value for this data
	 * @var int $_min
	 */
	protected $_min = null;

	/**
	 * The object mode.  May be one of:
	 *  - 'list'
	 *  - 'fixed'
	 *  - 'singleton'
	 * Singleton mode should act as an array with one and only one element.
	 * List mode should act as an array with fixed keys.
	 * @var string $_mode
	 */
	protected $_mode = 'list';

	/**
	 * Optional, name of this data object
	 * @var string
	 */
	protected $_name = null;

	/**
	 * Optional, data type, used by validation functions
	 * @var string $_type
	 */
	protected $_type = null;

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
	public function addTo($var, $val) {

		if (method_exists($this, 'validateData')) {
			// throws \DomainException if data is not of type $this->getType()
			$this->validateData($val);
		}

		if ($this->isStatic()) {
			throw new \DomainException("Static objects cannot be modified.");
		}

		$var = (string) $var;

		if (
			'singleton' === $this->getMode()
			&& !$this->has($var)
		) {
			$this->reset();
		} elseif (
			'fixed' === $this->getMode()
			&& !$this->has($var)
		) {
			throw new \DomainException("This is a fixed list and the specified key '{$var}' does not exist.");
		}

		// 'add' this value to the list identified by $key
		if ($this->has($var)) {
			if (!is_array($this->_data[$var])) {
				$this->_data[$var] = [$this->_data[$var]];
			}
		} else {
			$this->_data[$var] = [];
		}
		$this->_data[$var][] = $val;

		return $this;
	}

	/**
	 * Because ArrayAccess doesn't support array_keys()
	 * @return array
	 */
	public function arrayKeys() {
		return array_keys($this->getData());
	}

	/**
	 * Delete a locally stored value by name
	 *
	 * @param string $var The variable name
	 * @return Core\Object\Iface\Base $this
	 * @throws \DomainException If mode is static
	 * @throws \DomainException If mode is fixed and $var is not a valid key
	 */
	public function delete($var) {
		if ($this->isStatic()) {
			throw new \DomainException("Static objects cannot be modified.");
		}

		$var = (string) $var;
		if (!$this->has($var)) {
			throw new \DomainException("The specified key '{$var}' does not exist");
		}

		unset($this->_data[$var]);
		return $this;
	}

	/**
	 * Get a locally stored value by name
	 *
	 * @param string $var The variable name
	 * @return mixed|null The current value, else null
	 */
	public function get($var) {
		$ret_val = null;
		$var = (string) $var;
		if ($this->has($var)) {
			$ret_val = $this->_data[$var];
		}
		return $ret_val;
	}

	/**
	 * Get all defined constants
	 *
	 * @return array
	 */
	final public function getConstants() {
		$reflection = new \ReflectionClass(get_called_class());
		return $reflection->getConstants();
	}

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
	public function getData() {
		return $this->_data;
	}

	/**
	 * Get the max boundary property
	 *
	 * Throw an exception if "max" has no meaning in your class.
	 *
	 * @return int|null Current max value else null
	 */
	public function getMax() {
		return $this->_max;
	}

	/**
	 * Get the min boundary property
	 *
	 * Throw an exception if "min" has no meaning in your class.
	 *
	 * @return int|null Current min value else null
	 */
	public function getMin() {
		return $this->_min;
	}

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
	 * @return string|null A valid mode else null
	 */
	public function getMode() {
		$ret_val = null;
		switch ($this->_mode) {
			case 'list':
			case 'fixed':
			case 'singleton':
				$ret_val = $this->_mode;
			break;
		}
		return $ret_val;
	}

	/**
	 * Get the object name property
	 *
	 * Throw an exception if "name" has no meaning in your class.
	 *
	 * @return string|null
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * Get the "type" property
	 *
	 * @return string|null The current type value, else null
	 */
	public function getType() {
		$ret_val = null;
		if (!is_null($this->_type)) {
			$ret_val = (string) $this->_type;
		}
		return $ret_val;
	}

	/**
	 * Check to see if a value has been set
	 *
	 * @param string $var The variable name
	 * @return bool True if set, else false
	 */
	public function has($var) {
		return array_key_exists((string) $var, $this->_data);
	}

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
	public function isEmpty($var = null) {
		$ret_val = false;
		if (is_null($var)) {
			$ret_val = (0 === count($this));
		} else {
			$ret_val = (
				!$this->has($var)
				|| is_null($this->get($var))
				|| '' === $this->get($var)
			);
		}
		return $ret_val;
	}

	/**
	 * Set/get read-only flag for this object
	 *
	 * If true (static) this object becomes read-only
	 *
	 * @param bool $static
	 * @return bool
	 */
	public function isStatic($is_static = null) {
		if (!is_null($is_static)) {
			$this->_is_static = (bool) $is_static;
		}
		return $this->_is_static;
	}

	/**
	 * Return the number of records in the current object
	 *
	 * @return int
	 */
	public function length() {
		return count($this->_data);
	}

	/**
	 * Delete all locally stored values
	 *
	 * @return Core\Object\Iface\Base $this
	 * @throws \DomainException If mode is static
	 */
	public function reset() {
		if ($this->isStatic()) {
			throw new \DomainException("Static objects cannot be modified.");
		}
		$this->_data = [];
		return $this;
	}

	/**
	 * Store a named value locally
	 *
	 * @param string $var The name of the value
	 * @param mixed $val         The value to store
	 * @return Core\Object\Iface\Base $this
	 * @throws \DomainException  If mode is static
	 * @throws \DomainException  If mode is fixed and $var is not a valid key
	 */
	public function set($var, $val) {

		if (method_exists($this, 'validateData')) {
			// throws \DomainException if data is not of type $this->getType()
			$this->validateData($val);
		}

		if ($this->isStatic()) {
			throw new \DomainException("Static objects cannot be modified.");
		}

		//
		// Check the current mode, act accordingly.
		//
		if ('singleton' === $this->getMode()) {
			$this->reset();

		} elseif (
			'fixed' === $this->getMode()
			&& !$this->has($var)
		) {
			throw new \DomainException("This is a fixed list and the specified key '{$var}' does not exist.");
		}

		$var = (string) $var;

		$this->_data[$var] = $val;

		return $this;
	}

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
	public function setData($data) {
		if ($this->isStatic()) {
			throw new \DomainException("Static objects cannot be modified.");
		}

		if (!is_array($data)) {
			$data = (array) $data;
		}

		if (
			'singleton' === $this->getMode()
			&& count($data) > 1
		) {
			throw new \DomainException('Too much data for \'singleton\' mode ('.count($data).' elements given)');

		} elseif (
			'fixed' === $this->getMode()
			&& count(array_diff(array_keys($this->getData(), array_keys($data)))) > 0
		) {
			foreach ($this->getData() as $key => $value) {
				if (!array_key_exists($key, $data)) {
					throw new \DomainException("This is a fixed list and an existing key ('{$var}') is not present in the new list.");
				}
			}
			foreach ($data as $key => $value) {
				if (!array_key_exists($key, $this->getData())) {
					throw new \DomainException("This is a fixed list and a specified key ('{$var}') does not exist.");
				}
			}

		}
		if (method_exists($this, 'validateData')) {
			foreach ($data as $val) {
				// throws \DomainException if data is not of type $this->getType()
				$this->validateData($val);
			}
		}
		$this->_data = $data;

		return $this;
	}

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
	public function setMax($max) {
		if (!is_null($max)) {
			if (method_exists($this, 'isValidMax') && !$this->isValidMax($max)) {
				throw new \InvalidArgumentException("Invalid max value '$max'. Must be numeric and greater than the current min value.");
			}
			$this->_max = (float) $max;
		}
		return $this;
	}

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
	public function setMin($min) {
		if (!is_null($min)) {
			if (method_exists($this, 'isValidMin') && !$this->isValidMin($min)) {
				throw new \InvalidArgumentException("Invalid min value '$min'. Must be numeric and less than the current max value.");
			}
			$this->_min = (float) $min;
		}
		return $this->_min;
	}

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
	public function setMode($mode) {
		if (method_exists($this, 'isValidMode') && !$this->isValidMode($mode)) {
			throw new \InvalidArgumentException("Invalid mode '{$mode}'. Valid values are 'list', 'fixed' and 'singleton'");
		}
		$this->_mode = $mode;
		return $this;
	}

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
	public function setName($name) {
		if (method_exists($this, 'isValidName') && !$this->isValidName($name)) {
			throw new \InvalidArgumentException("Invalid name '$name'");
		}
		$this->_name = (string) $name;
		return $this;
	}

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
	public function setType($type) {
		if (!is_null($this->_type)) {
			throw new \RuntimeException("This object's type property has already been set");

		} elseif ('' === trim($type)) {
			throw new \DomainException("'type' must be a string and must not be empty");

		} elseif (method_exists($this, 'isValidType') && !$this->isValidType($type)) {
			throw new \DomainException("Invalid type '$type'");
		}

		$this->_type = $type;
		if (method_exists($this, 'validateData')) {
			foreach ($this->getData() as $val) {
				// throws \DomainException if data is not of type $this->getType()
				$this->validateData($val);
			}
		}
		return $this;
	}

	/**
	 * Recursively convert any Object\Iface\Base instances in the internal
	 * data storage array to an array and return the result
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function toArray($array = null) {
		$ret_val = [];
		if ($array instanceof Object\Iface\Base) {
			$array = $array->toArray();
		} elseif (!is_array($array)) {
			$array = $this->getData();
		}
		foreach ($array as $k => $v) {
			if (is_array($v)) {
				$ret_val[$k] = $this->toArray($v);
			} elseif ($v instanceof Core\Object\Iface\Base) {
				$ret_val[$k] = $v->toArray();
			} else {
				$ret_val[$k] = $v;
			}
		}
		return $ret_val;
	}

	/**
	 * Recursively convert the internal data storage array to a JSON string
	 *
	 * @return string JSON
	 * @throws \Exception
	 */
	public function toJson($options = 0, $depth = 512) {
		return json_encode($this->toArray(), $options, $depth);
	}

	/**
	 * Recursively convert stored arrays to Object instances
	 *
	 * @return string Object\Iface\Base
	 */
	public function toObject($data = null) {
		$root = false;
		if (is_null($data)) {
			$root = true;
			$data = $this->getData();
		}
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$value = $this->toObject($value);
				$data[$key] = new Core\Object($value);
			}
		}
		if ($root) {
			$this->setData($data);
			$data = $this;
		}
		return $data;
	}

	/**
	 * Convert the data array to a text representation, default JSON
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function toString() {
		return $this->toJson(JSON_PRETTY_PRINT);
	}

	/**
	 * Recursively convert the internal data storage array to an XML compatible
	 * string
	 *
	 * @return string XML
	 * @throws \Exception
	 */
	public function toXml($array = null) {
		$xml = '';
		if ($array instanceof Object\Iface\Base) {
			$array = $array->toArray();
		} elseif (is_object($array)) {
			$array = [get_class($array)];
		} elseif (!is_array($array)) {
			$array = $this->toArray();
		}
		foreach ($array as $k => $v) {
			$xml .= "<$k>";
			if (
				is_array($v)
				|| $v instanceof Object\Iface\Base
			) {
				$xml .= $this->toXml($v);
			} else {
				$xml .= $v;
			}
			$xml .= "</$k>";
		}
		return $xml;
	}
}
