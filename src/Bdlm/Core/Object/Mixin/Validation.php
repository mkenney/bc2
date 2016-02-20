<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Object\Mixin;
use \Bdlm\Core\Util;

/**
 * Implementations for \Bdlm\Core\Object\Iface\Validation
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.1.0
 */
trait Validation {

	/**
	 * Find out if $max is valid
	 *
	 * @param int|float $max The max value to check
	 * @return bool
	 */
	public function isValidMax($max) {
		$ret_val = true;
		if (
			!Util::isNum($max)
			|| (
				!is_null($this->getMin())
				&& (float) $max < $this->getMin()
			)
		) {
			$ret_val = false;
		}
		return $ret_val;
	}

	/**
	 * Find out if $min is valid
	 * @param int|float $min The min value to check
	 * @return bool
	 */
	public function isValidMin($min) {
		$ret_val = true;
		if (
			!Util::isNum($min)
			|| (
				!is_null($this->getMax())
				&& (float) $min > $this->getMax()
			)
		) {
			$ret_val = false;
		}
		return $ret_val;
	}

	/**
	 * Find out if $mode is valid
	 * @param string $mode The mode value to check
	 * @return bool
	 */
	public function isValidMode($mode) {
		$ret_val = false;
		switch (trim($mode)) {
			case 'list':      // Arbitrary list of data
			case 'fixed':     // List of data with defined locations (keys)
			case 'singleton': // Single value
				$ret_val = true;
			break;

			default:
				$ret_val = false;
			break;
		}
		return $ret_val;
	}

	/**
	 * Find out if $name is valid
	 * @param string $name The name to check
	 * @return bool
	 */
	public function isValidName($name) {
		return ('' !== (string) $name);
	}

	/**
	 * Find out if $type is valid, in this case $type must be a valid class
	 * Not final so this class can more easily be reapplied while maintaining the API
	 * @param string $type The type name to check
	 * @return bool
	 * @throws \InvalidArgumentException If $type can't be a string or is empty
	 */
	public function isValidType($type) {
		$type = (string) $type;
		$ret_val = false;
		switch ($type) {
			case 'array':
			case 'bool':
			case 'boolean':
			case 'date':
			case 'double':
			case 'file':
			case 'float':
			case 'int':
			case 'integer':
			case 'long':
			case 'mbstring':
			case 'mixed':
			case 'object':
			case 'real':
			case 'resource':
			case 'scalar':
			case 'string':
				$ret_val = true;
			break;

			//
			// Assume it's a class name
			//
			default:
				try {
					$ret_val = class_exists($type, true);
				} catch (\Exception $e) {
					if (
						0 !== $e->getCode()
						|| false === strpos(strtolower($e->getMessage()), 'invalid class name')
					) {
						throw new \InvalidArgumentException("{$e->getCode()}: {$e->getMessage()}", self::INVALID_TYPE_DEFINITION);
					}
				}
			break;
		}
		return $ret_val;
	}

	/**
	 * Validate data aginst this objects's _type, _max and _min values.
	 * @param mixed $data
	 * @return \Bdlm\Core\Object $this
	 * @throws \DomainException If validation fails for any reason
	 * @todo This has full unit-test coverage but it still needs a lot of testing
	 */
	public function validateData($data) {
		$type = $this->getType();
		$throw_validtion_exception = function($type, $data, $code) {
			throw new \DomainException('Invalid data type \''.gettype($data).'\', expecting \''.$type.'\'.', $code);
		};
		switch ($type) {
			case 'array':
				if (!is_array($data)) {$throw_validtion_exception($type, $data, self::INVALID_TYPE_ARRAY);}
				$size = count($data);
				if (
					(!is_null($this->getMax()) && $size > (int) $this->getMax())
					|| (!is_null($this->getMin()) && $size < (int) $this->getMin())
				) {
					throw new \OutOfBoundsException("Data ($size array elements) out of range ({$this->getMin()} to {$this->getMax()} array elements)", self::INVALID_DATA_SIZE);
				}
			break;

			case 'bool':
			case 'boolean':
				if (!is_bool($data)) {$throw_validtion_exception($type, $data, self::INVALID_TYPE_BOOLEAN);}
			break;

			case 'date':
				if (Util::isNum($data)) {
					$size = (float) $data;
					if (0 > $size) {
						$throw_validtion_exception($type, $size, self::INVALID_TYPE_DATE);
					}
				} elseif (is_string($data)) {
					$size = strtotime($data);
					if (false === $size) {
						$throw_validtion_exception($type, $size, self::INVALID_TYPE_DATE);
					}
				} elseif ($data instanceof \DateTime) {
					$size = $data->getTimestamp();
					if (false === $size) {
						$throw_validtion_exception($type, $size, self::INVALID_TYPE_DATE);
					}
				} else {
					$throw_validtion_exception($type, "{$data}", self::INVALID_TYPE_DATE);
				}
				if (
					(!is_null($this->getMax()) && $size > (int) $this->getMax())
					|| (!is_null($this->getMin()) && $size < (int) $this->getMin())
				) {
					throw new \OutOfBoundsException("Data ($size) out of range ({$this->getMin()} to {$this->getMax()} epoch seconds)", self::INVALID_DATA_SIZE);
				}
			break;

			case 'double':
			case 'float':
			case 'real':
				if (!Util::isNum($data)) {
					$throw_validtion_exception($type, $data, self::INVALID_TYPE_DOUBLE);
				}
				$size = (float) $data;
				if (
					(!is_null($this->getMax()) && $size > (float) $this->getMax())
					|| (!is_null($this->getMin()) && $size < (float) $this->getMin())
				) {
					throw new \OutOfBoundsException("Data ({$size}) out of range ({$this->getMin()} to {$this->getMax()})", self::INVALID_DATA_SIZE);
				}
			break;

			case 'file':
				if (!is_string($data)) {
					$throw_validtion_exception($type, $data, self::INVALID_TYPE_FILE);
				}
				if (!is_file($data)) {
					$throw_validtion_exception($type, $data, self::INVALID_TYPE_FILE);
				}
				$size = filesize($data);
				if (
					(!is_null($this->getMax()) && $size > (int) $this->getMax())
					|| (!is_null($this->getMin()) && $size < (int) $this->getMin())
				) {
					$size = number_format($size, 0);
					throw new \OutOfBoundsException("Data ({$data} is {$size} bytes) out of range ({$this->getMin()} to {$this->getMax()})", self::INVALID_DATA_SIZE);
				}
			break;

			case 'int':
			case 'integer':
			case 'long':
				if (!Util::isNum($data, true)) {
					$throw_validtion_exception($type, $data, self::INVALID_TYPE_INTEGER);
				}
				$size = (int) $data;
				if (
					(!is_null($this->getMax()) && $size > (int) $this->getMax())
					|| (!is_null($this->getMin()) && $size < (int) $this->getMin())
				) {
					throw new \OutOfBoundsException("Data ({$size}) out of range ({$this->getMin()} to {$this->getMax()})", self::INVALID_DATA_SIZE);
				}
			break;

			case 'mbstring':
				//throw new \DomainException("Multi-byte string validation still needs to be added... :(", self::INVALID_TYPE_MBSTRING);
			break;

			case 'mixed':
			case null:
				// 'mixed' type data is not validated, exists for flexibility.
			break;

			case 'object':
				if (!is_object($data)) {
					$throw_validtion_exception($type, $data, self::INVALID_TYPE_OBJECT);
				}
			break;

			case 'resource':
				if (!is_resource($data)) {
					$throw_validtion_exception($type, $data, self::INVALID_TYPE_RESOURCE);
				}
			break;

			case 'scalar':
				if (!is_scalar($data)) {
					$throw_validtion_exception($type, $data, self::INVALID_TYPE_SCALAR);
				}
				if (is_bool($data)) {
					$size = null;
				} elseif (is_integer($data)) {
					$size = $data;
					$max = (int) $this->getMax();
					$min = (int) $this->getMin();
				} elseif (is_float($data)) {
					$size = $data;
					$max = (float) $this->getMax();
					$min = (float) $this->getMin();
				} elseif (is_string($data)) {
					$size = strlen($data);
					$max = (int) $this->getMax();
					$min = (int) $this->getMin();
				}
				if (
					(!is_null($this->getMax()) && $size > $max)
					|| (!is_null($this->getMin()) && $size < $min)
				) {
					throw new \OutOfBoundsException("Data ({$size}) out of range ({$this->getMin()} to {$this->getMax()}), data type: ".gettype($data), self::INVALID_DATA_SIZE);
				}
			break;

			case 'string':
				if (!is_string($data)) {
					$throw_validtion_exception($type, $data, self::INVALID_TYPE_STRING);
				}
				$size = strlen($data);
				if (
					(!is_null($this->getMin()) && $size < $this->getMin())
					|| (!is_null($this->getMax()) && $size > $this->getMax())
				) {
					throw new \OutOfBoundsException("Data ({$size} characters) out of range ({$this->getMin()} to {$this->getMax()})", self::INVALID_DATA_SIZE);
				}
			break;

			default:

				// Assume $this->getType() is a class name.
				// @todo Test that inheritance is correctly accounted for
				if (is_object($data)) {
					if (!$data instanceof $type) {
						$throw_validtion_exception($type, $data, self::INVALID_TYPE_CLASS);
					}

				// Bad type got in here somehow, revisit isValidType();
				} else {
					$throw_validtion_exception($type, $data, self::INVALID_TYPE_UNKNOWN);
				}
			break;
		}

		return $this;
	}
}
