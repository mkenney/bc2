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
interface Magic {

    /**
     * @return mixed
     */
    public function __get($var);

    /**
     * @return bool
     */
    public function __isset($var);

    /**
     * @return void
     */
    public function __set($var, $val);

    /**
     * @return string
     */
    public function __toString();

    /**
     * @return void
     */
    public function __unset($var);
}
