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
class Model extends Object implements Model\Iface\Base {

    /**
     *
     */
    use Controller\Mixin\Boilerplate;

    /**
     *
     */
    use Datasource\Mixin\Boilerplate;

    /**
     * [$_datasources description]
     * @var [type]
     */
    protected $_datasources = [];

    /**
     * The current fetch mode
     * @var int
     */
    protected $_fetch_mode = null;

    /**
     * The value to filter on
     * @var mixed
     */
    protected $_fetch_value = null;

    /**
     * The column name to filter on
     * @var string
     */
    protected $_fetch_column_name = null;

    /**
     * The name of a pre-defined query to use
     * This is a \Bdlm\App\{controller name}\Data\* class name
     * @var string
     */
    protected $_fetch_query_name = null;

    public function __construct(Datasource\Iface\Base $datasource = null) {
        if (!is_null($datasource)) {$this->setDatasource($datasource);}
    }

    public function load() {
        throw new \RuntimeException('Not yet implemented');
    }

    public function save($as_new = false) {
        throw new \RuntimeException('Not yet implemented');
    }
}
