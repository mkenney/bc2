<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Datasource\Object\Mixin;
use \Bdlm\Core\Datasource;

/**
 * Getter/setter boilerplate for datasource instances
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
trait Base {
    /**
     * The name of this objects primary schema
     * @var string
     */
    protected $_primary_schema = null;
    /**
     * The list of related schemas that make up this object
     * @var array A hash of schema_name => Datasource\Schema\Iface\Base instances
     */
    protected $_schemas = null;
    /**
     * Add a schema instance to the stack
     *
     * @param  Datasource\Schema\Iface\Base $schema
     * @return Datasource\Object\Iface\Base
     */
    public function addSchema(Datasource\Schema\Iface\Base $schema) {
        if (is_null($this->_schemas)) {$this->_schemas = new \Bdlm\Core\Object();}
        $this->_schemas->set($schema->getName(), $schema);
        return $this;
    }
    /**
     * Get the name of the primary schema for this object
     * @return string
     */
    public function getPrimarySchemaName() {
        return $this->_primary_schema;
    }
    /**
     * Get a schema instance by name
     * Return false if a corresponding instance doesn't exist
     * @param  string $schema_name
     * @return false|Datasource\Schema\Iface\Base
     */
    public function getSchema($schema_name) {
        $ret_val = false;
        if (isset($this->_schemas[$schema_name])) {
            $ret_val = $this->_schemas[$schema_name];
        }
        return $ret_val;
    }
    /**
     * [load description]
     * @param  boolean $force [description]
     * @return [type]         [description]
     */
    public function load($force = false) {
        if (!count($this->getId())) {throw new \RuntimeException("An Id hash must be set before data can be loaded");}

        if (!$this->isLoaded() || $force) {
            $this->isLoading(true);

            $this->reset();

            foreach ($this->_schemas as $schema_name => $schema) {
                $row_class = "\\Bdlm\\Core\\Datasource\\Record\\{$schema->getDatasource()->getTypeString()}";

                // The primary schema can only have 1 row that matches the Id
                if ($schema_name === $this->getPrimarySchemaName()) {
                    $row = new $row_class(
                        $schema
                        , $this->getPk()
                        , $this->getId()
                    );
                    $row->load();
                    $this->set($schema_name, $row);

                // Assume the rest of the schemas have a 1-to-many relationship to the
                // primary schema, and that there is an 'id' column that is the unique
                // identifier for each row. Assume the correct selector would be the
                // primary schema's name prefixed to each primary key column:
                //     If the primary key is 'id' and the primary table is 'user',
                //     then assume that the selector key in all related tables is
                //     'user_id'
                //
                } else {

                    $keys = $this->getPk();
                    foreach ($keys as $k => $v) {
                        $keys[$k] = "{$this->getPrimarySchemaName()}_{$v}";
                    }

                    $where = [];
                    foreach ($keys as $field) {
                        $where[] = "`{$field}` = :{$field}";
                    }
                    $where = (count($where) ? implode(' AND ', $where) : '');

                    $query = $this->getDatasource()->prepareQuery(
<<<SQL
    SELECT `id` FROM `{$schema->getName()}` WHERE {$where}
SQL
                    );
                    foreach ($this->getId() as $field => $value) {
                        $query->bind("{$this->getPrimarySchemaName()}_{$field}", $value);
                    }
                    $query->execute();

                    $this->set($schema_name, []);
                    while ($data = $query->next()) {
                        $row = new $row_class(
                            $schema
                            , ['id']
                            , ['id' => $data['id']]
                        );
                        $row->load();
                        $this->add($schema_name, $row);
                    }
                }
            }

            $this->isDirty(false);
            $this->isLoaded(true);
            $this->isLoading(false);
        }
        return $this;
    }
    /**
     * Save this object's data to appropriate data storage
     *
     * @param  boolean                 $as_new If true, save this object's data as a new record
     * @return Core\Model\Iface\Base   $this
     */
    public function save($force = false) {
        foreach ($this->getData() as $schema_name => $rows) {
            if ($rows instanceof \Bdlm\Core\Datasource\Record\Iface\Base) {
                $rows->save($force);
            } else {
                foreach ($rows as $row) {
                    if ($row instanceof \Bdlm\Core\Datasource\Record\Iface\Base) {
                        $row->save();
                    }
                }
            }
        }
        return $this;
    }
    /**
     * Set the name of the primary schema for this object
     * @param string $schema_name
     */
    public function setPrimarySchemaName($schema_name) {
        $schema_name = trim($schema_name);
        if ($schema_name) {
            $this->_primary_schema = $schema_name;
        }
        return $this;
    }
}
