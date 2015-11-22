<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Datasource\Schema\Mixin;

use \Bdlm\Core;

/**
 * Getter/setter boilerplate for datasource instances
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
trait Boilerplate {

    /**
     * @var Core\Datasource\Datasource
     */
    protected $_schema = null;

    /**
     * Get the current datasource instance
     *
     * @return Core\Datasource\Datasource\Iface\Base|false The current datasource instance, else false
     */
    public function getSchema() {
        return $this->_schema;
    }

    /**
     * Set a schema instance for this object to reference
     *
     * @param Core\Datasource\Schema $schema A schema instance to reference
     * @return Core\Datasource\Schema\Iface\Base            The current instance
     */
    public function setSchema(Core\Datasource\Schema\Iface\Base $schema) {
        $this->_schema = $schema;
        return $this;
    }
}
