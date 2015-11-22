<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Object\Iface;

/**
 * Object class interface
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.1.0
 */
interface Validation {

    /**
     * Array type error code
     */
    const INVALID_TYPE_ARRAY     = 1;
    /**
     * Boolean type error code
     */
    const INVALID_TYPE_BOOLEAN   = 2;
    /**
     * Date type error code
     */
    const INVALID_TYPE_DATE      = 3;
    /**
     * Double / Float type error code
     */
    const INVALID_TYPE_DOUBLE    = 4;
    /**
     * File type error code
     */
    const INVALID_TYPE_FILE      = 5;
    /**
     * Int / Integer / Long / Real type error code
     */
    const INVALID_TYPE_INTEGER   = 7;
    /**
     * Multi-byte string type error code
     */
    const INVALID_TYPE_MBSTRING  = 9;
    /**
     * Object type error code
     */
    const INVALID_TYPE_OBJECT    = 10;
    /**
     * Resource type error code
     */
    const INVALID_TYPE_RESOURCE  = 12;
    /**
     * Scalar type error code
     */
    const INVALID_TYPE_SCALAR    = 13;
    /**
     * String type error code
     */
    const INVALID_TYPE_STRING    = 14;
    /**
     * Class type error code
     */
    const INVALID_TYPE_CLASS     = 15;
    /**
     * Unknown type error code
     */
    const INVALID_TYPE_UNKNOWN   = 16;
    /**
     * Invalid type definition specified
     */
    const INVALID_TYPE_DEFINITION   = 17;
    /**
     * Bounded data (max() / min()) error code
     */
    const INVALID_DATA_SIZE      = 18;

    /**
     * Find out if $max is valid
     *
     * @param int|float $max The max value to check
     * @return bool
     */
    public function isValidMax($max);

    /**
     * Find out if $min is valid
     * @param int|float $min The min value to check
     * @return bool
     */
    public function isValidMin($min);

    /**
     * Find out if $mode is valid
     * @param string $mode The mode value to check
     * @return bool
     */
    public function isValidMode($mode);

    /**
     * Find out if $name is valid
     * @param string $name The name to check
     * @return bool
     */
    public function isValidName($name);

    /**
     * Find out if $type is valid, in this case $type must be a valid class
     * Not final so this class can more easily be reapplied while maintaining the API
     * @param string $type The type name to check
     * @return bool
     * @throws \InvalidArgumentException If $type can't be a string or is empty
     */
    public function isValidType($type);

    /**
     * Validate data aginst this objects's _type, _max and _min values.
     * @param mixed $data
     * @return ObjectAbstract $this
     * @throws \DomainException If validation fails for any reason
     * @todo This has full unit-test coverage but it still needs a lot of testing
     */
    public function validateData($data);
}
