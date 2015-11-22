<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Model\Mixin;

use \Bdlm\Core;

/**
 * Getter/setter boilerplate for Model instances
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
trait Boilerplate {

    /**
     * @var Core\Model\Iface\Base
     */
    protected $_model = null;

    /**
     * Get the current model instance
     *
     * @return Core\Model\Iface\Base|false The current model instance, else false
     */
    public function getModel() {
        $ret_val = false;
        if ($this->_model instanceof Core\Model\Iface\Base) {
            $ret_val = $this->_model;
        }
        return $ret_val;
    }

    /**
     * Set a model instance for this object to reference
     *
     * @param  Core\Model\Iface\Base  $model A model instance to reference
     * @return Core\Object\Iface\Base The current instance
     */
    public function setModel(Core\Model\Iface\Base $model) {
        $this->_model = $model;
        return $this;
    }
}
