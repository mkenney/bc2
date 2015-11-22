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
 * Base model class
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
abstract class Model implements
    Model\Iface\Base
    , Core\Datasource\Object\Iface\Base
{
    /**
     * Controller boilerplate
     */
    use Model\Mixin\Base;
    /**
     * Controller boilerplate
     */
    use Controller\Mixin\Boilerplate;
    /**
     * Datasource boilerplate
     */
    use Datasource\Mixin\Boilerplate;
    /**
     * Store the Datasource instance
     * @param Datasource\Iface\Base|null $datasource
     */
    public function __construct(Datasource\Iface\Base $datasource = null) {
        if (!is_null($datasource)) {$this->setDatasource($datasource);}
        return $this;
    }
}
