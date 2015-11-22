<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Datasource\Schema;
use Bdlm\Core\Datasource;
use Bdlm\Core;

/**
 * Abstract interface for representing a record definition in a Datasource
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 2.0.0
 */
abstract class SchemaAbstract implements
    Core\Datasource\Schema\Iface\Base
    , \ArrayAccess
    , \Countable
    , \Iterator
    , \Serializable
{
    /**
     * Object base
     */
    use Core\Object\Mixin\Base {
        Core\Object\Mixin\Base::get     as protected _coreGet;
        Core\Object\Mixin\Base::getData as protected _coreGetData;
        Core\Object\Mixin\Base::has     as protected _coreHas;
        Core\Object\Mixin\Base::set     as protected _coreSet;
        Core\Object\Mixin\Base::setData as protected _coreSetData;
    }

    /**
     * ArrayAccess
     */
    use Core\Object\Mixin\ArrayAccess;

    /**
     * Countable
     */
    use Core\Object\Mixin\Countable;

    /**
     * Iterator
     */
    use Core\Object\Mixin\Iterator;

    /**
     * Magic
     */
    use Core\Object\Mixin\Magic;

    /**
     * Serializable
     */
    use Core\Object\Mixin\Serializable;

    /**
     * Datasource boilerplate
     */
    use Core\Datasource\Mixin\Boilerplate;

    /**
     * Set the Datasource and initialize
     *
     * @param  Datasource\DatasourceAbstract $datasource Object representing the Datasource
     * @param  string                        $name       Optional, the name of this Schema in the Datasource
     * @return Schema\Iface\Base
     */
    final public function __construct(Datasource\DatasourceAbstract $datasource, $name = null) {
        $this->setDatasource($datasource);
        if (!is_null($name)) {
            $this->setName($name);
        }
        return $this;
    }

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
    public function describe($field = null) {
        if (!$this->length()) {$this->load();}

        if (!is_null($field)) {
            $ret_val = $this->get($field);
        } else {
            $ret_val = $this->getData();
        }
        return $ret_val;
    }

    /**
     * Overload get() to load schema on-demand
     */
    public function get($field) {
        if (!$this->length()) {$this->load();}
        return $this->_coreGet($field);
    }

    /**
     * Overload getData() to load schema on-demand
     */
    public function getData() {
        if (!$this->length()) {$this->load();}
        return $this->_coreGetData();
    }

    /**
     * Overload has() to load schema on-demand
     */
    public function has($field) {
        if (!$this->length()) {$this->load();}
        return $this->_coreHas($field);
    }

    /**
     * Don't allow schemas to be modified by default
     * Core\Object::set is mapped to _coreSet
     *
     * @throws \BadMethodCallException A schema cannot be modified
     */
    public function set($var, $val) {
        throw new \BadMethodCallException("A schema cannot be modified");
    }

    /**
     * Don't allow schemas to be modified by default
     * Core\Object::setData is mapped to _coreSetData
     *
     * @throws \BadMethodCallException A schema cannot be modified
     */
    public function setData($data) {
        throw new \BadMethodCallException("A schema cannot be modified");
    }

    /**
     * Sanitize the table name before storing it
     * @param string $name
     */
    public function setName($name) {
        $name = trim(str_replace('`', '', $this->getDatasource()->quote($name, false)));
        if (!$name) {throw new \RuntimeException("Invalid table name '{$name}'");}
        $this->_name = $name;
        return $this;
    }
}
