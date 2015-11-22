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
 * Base controller class
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
class Controller extends Object implements Controller\Iface\Base {

    /**
     * Model boilerplate to implement Controller\Iface\Base
     * @trait
     */
    use Model\Mixin\Boilerplate;

    /**
     * View boilerplate to implement Controller\Iface\Base
     * @trait
     */
    use View\Mixin\Boilerplate;

    /**
     * Config boilerplate to implement Controller\Iface\Base
     * @trait
     */
    use Util\Config\Mixin\Boilerplate;

    /**
     * Initialize this controller
     *
     * @param \Bdlm\Core\Model $model The Model this controller should use
     * @param \Bdlm\Core\View  $view  The View this controller should use
     */
    public function __construct(Model\Iface\Base $model, View\Iface\Base $view, Util\Config\Iface\Base $config = null) {
        $this->setModel($model);
        $this->setView($view);
        if (!is_null($config)) {
            $this->setConfig($config);
        }
    }

}
