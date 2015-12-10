<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Datasource\Object\Iface;
use \Bdlm\Core\Datasource;

/**
 * Base interface
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
interface Base extends \Bdlm\Core\Object\Iface\Base {

    /**
     * Add a schema instance to the stack
     *
     * @param  Datasource\Schema\Iface\Base $schema
     * @return Datasource\Object\Iface\Base
     */
    public function addSchema(Datasource\Schema\Iface\Base $schema);

    /**
     * Get the name of the primary schema for this object
     * @return string
     */
    public function getPrimarySchemaName();

    /**
     * Get a schema instance by name
     * Return false if a corresponding instance doesn't exist
     * @param  string $schema_name
     * @return false|Datasource\Schema\Iface\Base
     */
    public function getSchema($schema_name);

    /**
     * Initialize Record instances for all records related to the primary record in
     * the primary schema
     *
     * @param  boolean $force [description]
     * @return [type]         [description]
     */
    public function load($force = false);

    /**
     * Save this object's data to appropriate data storage
     *
     * @param  boolean                 $as_new If true, save this object's data as a new record
     * @return Core\Model\Iface\Base   $this
     */
    public function save($force = false);

    /**
     * Set the name of the primary schema for this object
     * @param string $schema_name
     */
    public function setPrimarySchemaName($schema_name);
}
