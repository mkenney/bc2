<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Util\Config\Mixin;

use \Bdlm\Core\Util;

/**
 * Getter/setter boilerplate for config instances
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
trait Boilerplate {

    /**
     * @var Util\Config\Iface\Base
     */
    protected $_config = null;

    /**
     * Get the current config instance
     *
     * @return Util\Config\Iface\Base|false The current config instance, else false
     */
    public function getConfig() {
        $ret_val = false;
        if ($this->_config instanceof Util\Config\Iface\Base) {
            $ret_val = $this->_config;
        }
        return $this->_config;
    }

    /**
     * Set a config instance for this object to reference
     *
     * @param Util\Config\Iface\Base     $config A config instance to reference
     * @return \Bdlm\Core\Object\Iface\Base         The current instance
     */
    public function setConfig(Util\Config\Iface\Base $config) {
        $this->_config = $config;
        return $this;
    }
}
