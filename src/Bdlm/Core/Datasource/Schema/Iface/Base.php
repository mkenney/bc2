<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Datasource\Schema\Iface;

use \Bdlm\Core\Datasource;

/**
 * Base config interface
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
interface Base extends \Bdlm\Core\Object\Iface\Base {

    /**
     * Set the Datasource and initialize
     *
     * @param  Datasource\DatasourceAbstract $datasource Object representing the Datasource
     * @param  string                        $name       Optional, the name of this Schema in the Datasource
     * @return SchemaAbstract
     */
    public function __construct(Datasource\DatasourceAbstract $datasource, $name = '');

    /**
     * Return an array of information about this schema
     *
     * If an individual field is specified, only return information for that field
     *
     * @param  string $field Optional, the name of a field
     * @return array  Array describing the field(s) in this schema:
     *                [
     *                  "field name" => [
     *                    "metadata property 1" => "value or description"
     *                    , "metadata property 2" => "value or description"
     *                    ...
     *                  ]
     *                ]
     */
    public function describe($field = null);

    /**
     * Load schema data for a resource in the Datasource identified by
     * $this->getName(). Schema should be stored using \Core\Object\Iface\Base
     * methods, generally as an array of:
     *     [
     *       "field name" => [
     *         "metadata property 1" => "value or description"
     *         , "metadata property 2" => "value or description"
     *         ...
     *       ]
     *     ]
     *
     * @return Schema\Iface\Base
     */
    public function load();
}
