<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core;

/**
 * Arbitrary data object
 *
 * Provides functions for strict typing and data validation.  Can be easily
 * extended to add custom data types.  No data validation is performed unless a
 * data type has been specified.  In that case, the max and min properties are
 * taken into account during validation, allowing you to define upper and lower
 * boundaries for all data values.
 *
 * The meaning of the max and min boundaries should be semantically dependent on
 * the type of data.  For example, a max of 10 on a type of 'int' means it's
 * value must be <= 10, but for a type of 'string' it must be <= 10 characters.
 *
 * type, max and min apply to each stored value individually, meaning that every
 * item stored here must meet those requirements.
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 1.8.0
 * @todo write unit tests
 */
class Object implements
	Object\Iface\Base
	, Object\Iface\Magic
	, Object\Iface\Validation
	, \Iterator
	, \ArrayAccess
	, \Countable
	, \Serializable
{
	/**
	 * Object\Iface\Base implementation
	 */
	use Object\Mixin\Base;
	/**
	 * Object\Iface\Magic implementation
	 */
	use Object\Mixin\Magic;
	/**
	 * Object\Iface\Validation implementation
	 */
	use Object\Mixin\Validation;
	/**
	 * \ArrayAccess implementation
	 */
	use Object\Mixin\ArrayAccess;
	/**
	 * \Countable implementation
	 */
	use Object\Mixin\Countable;
	/**
	 * \Iterator implementation
	 */
	use Object\Mixin\Iterator;
	/**
	 * \Serializable implementation
	 */
	use Object\Mixin\Serializable;

	/**
	 * Initialize and populate data, if any.
	 *
	 * If data is an array, it is stored as-is, otherwise it's typed as an array first.
	 *
	 * @param mixed $data   The initial data to store in the new object
	 * @param bool  $nested If true, recursivly convert $data to nested objec instances
	 * @return Bdlm_Object
	 */
	public function __construct($data = null, $nested = false) {
		if (!is_null($data)) {
			if ($data instanceof \Bdlm\Core\Object) {
				$data = $data->getData();
			}
			if ($nested) {
				$data = $this->toObject($data);
			}

			$this->setData((array) $data);
		}
	}
}
