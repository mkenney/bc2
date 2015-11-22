<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Controller\Iface;

use \Bdlm\Core;

/**
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.1.0
 */
interface Base {

    /**
     * Get the current config instance
     *
     * @return Util\Config\Iface\Base|false The current config instance, else false
     */
    public function getConfig();

    /**
     * Set a config instance for this object to reference
     *
     * @param Util\Config\Iface\Base     $config A config instance to reference
     * @return \Bdlm\Core\Object\Iface\Base         The current instance
     */
    public function setConfig(Core\Util\Config\Iface\Base $config);

    /**
     * Get the current model instance
     *
     * @return Model\Iface\Base|false The current model instance, else false
     */
    public function getModel();

    /**
     * Set a model instance for this object to reference
     *
     * @param  Model\Iface\Base  $model A model instance to reference
     * @return Object\Iface\Base The current instance
     */
    public function setModel(Core\Model\Iface\Base $model);

    /**
     * Get the current view instance
     *
     * @return View\Iface\Base|false The current view instance, else false
     */
    public function getView();

    /**
     * Set a view instance for this object to reference
     *
     * @param  View\Iface\Base   $view A view instance to reference
     * @return Object\Iface\Base The current instance
     */
    public function setView(Core\View\Iface\Base $view);
}