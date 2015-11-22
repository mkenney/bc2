<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Datasource;

use \Bdlm\Core;

/**
 * Datasource abstraction
 *
 * Manages datasource connections and initializes queries
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
abstract class DatasourceAbstract implements
    Iface\Base
    , Core\Object\Iface\Base
    , Core\Object\Iface\Magic
    , \Iterator
    , \ArrayAccess
    , \Countable
    , \Serializable
{

    /**
     * \Serializable implementation
     */
    use Core\Datasource\Mixin\Base;

    /**
     * Object\Iface\Core implementation
     */
    use Core\Object\Mixin\Base {
        Core\Datasource\Mixin\Base::getType insteadof Core\Object\Mixin\Base;
        Core\Datasource\Mixin\Base::setType insteadof Core\Object\Mixin\Base;
    }

    /**
     * Object\Iface\Magic implementation
     */
    use Core\Object\Mixin\Magic;

    /**
     * \ArrayAccess implementation
     */
    use Core\Object\Mixin\ArrayAccess;

    /**
     * \Countable implementation
     */
    use Core\Object\Mixin\Countable;

    /**
     * \Iterator implementation
     */
    use Core\Object\Mixin\Iterator;

    /**
     * \Serializable implementation
     */
    use Core\Object\Mixin\Serializable;
}
