<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Datasource\Object;
use Bdlm\Core;

/**
 * Abstract interface for representing a collection of related Datasource Records
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 2.0.0
 */
abstract class ObjectAbstract implements
    Core\Datasource\Object\Iface\Base
    , Core\Model\Iface\Base
    , Core\Object\Iface\Magic
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
     * Data object base
     */
    use Core\Datasource\Object\Mixin\Base;

    /**
     * Object data model
     */
    use Core\Model\Mixin\Base;

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
     * @param  Datasource\DatasourceAbstract $datasource Object representing the
     *                                                   Datasource
     * @param  string                        $name       Optional, the name of
     *                                                   this Schema in the Datasource
     * @return Core\Datasource\Object\Iface\Base
     */
    public function __construct(
        Core\Datasource\DatasourceAbstract $datasource
        , $primary_schema_name
        , array $primary_key_fields
        , array $schemas
        , array $primary_id = null
    ) {
        $this->setDatasource($datasource);
        $this->setPrimarySchemaName($primary_schema_name);
        $this->setPk($primary_key_fields);
        foreach ($schemas as $schema) {
            $this->addSchema($schema);
        }
        if (!is_null($primary_id)) {
            $this->setId($primary_id);
        }
        return $this;
    }
}
