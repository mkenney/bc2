<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Datasource\Schema;

/**
 * PDO-based schema information
 *
 * Describes a single table in a database
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
class Pdo extends SchemaAbstract {

    /**
     * Load schema data for a resource in the Datasource identified by
     * $this->getName()
     *
     * @return Schema\Iface\Base
     */
    public function load() {
        $query = $this->getDatasource()->prepareQuery(
<<<SQL
    DESCRIBE `{$this->getName()}`
SQL
        );
        $query->execute();

        while ($data = $query->next()) {
            $data = array_change_key_case($data->getData(), CASE_LOWER);
            $this->_coreSet($data['field'], $data);
        }
    }

}
