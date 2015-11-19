<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\View\Html\Mixin;

use \Bdlm\Core;

/**
 * Getter/setter boilerplate for Html instances
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
trait Boilerplate {

    /**
     * @var Core\View\Html\Iface\Base
     */
    protected $_html = null;

    /**
     * Get the current html instance
     *
     * @return Core\View\Html\Iface\Base|false The current html instance, else false
     */
    public function getHtml() {
        $ret_val = false;
        if ($this->_html instanceof Core\View\Html\Iface\Base) {
            $ret_val = $this->_html;
        }
        return $ret_val;
    }

    /**
     * Set a html instance for this object to reference
     *
     * @param  Core\View\Html\Iface\Base   $html A html instance to reference
     * @return Core\Object\Iface\Base The current instance
     */
    public function setHtml(Core\View\Html\Iface\Base $html) {
        $this->_html = $html;
        return $this;
    }
}
