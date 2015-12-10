<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Datasource;

/**
 * PDO-based datasource
 *
 * Manages database connections and transactions
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
class Pdo extends DatasourceAbstract {

    /**
     * Default values
     * @var array
     */
    protected $_data = [
        'type'   => DatasourceAbstract::TYPE_PDO,
    ];

    /**
     * Always roll-back on close
     *
     * @return void
     */
    final public function __destruct() {
        if (
            $this->getConnection() instanceof \PDO
            && $this->getConnection()->inTransaction()
        ) {
            $this->getConnection()->rollBack();
        }
    }

    /**
     * Commit changes, if any
     *
     * @return boolean
     */
    public function commit() {
        return $this->getConnection()->commit();
    }

    /**
     * Connect to the database
     *
     * @return DatasourceAbstract $this
     */
    public function connect() {
        $this->setConnection(new \PDO($this->getDsn(), $this->getUsername(), $this->getPassword()));
        $driver = explode(':', $this->getDsn())[0];
        $this->setDriver($driver);

        $this->getConnection()->beginTransaction();
        return $this;
    }

    /**
     * Prepare a PDO-based query object
     *
     * @param  string $query  The query to execute
     * @param  array  $data   Bind data
     * @return Datasource\Pdo
     */
    public function prepareQuery($query, $data = []) {
        if (!$this->getConnection()) {$this->connect();}
        return new Query\Pdo($this, $query, $data);
    }

    /**
     * Get the driver name from the DSN string
     * @return [type] [description]
     */
    public function getDriver() {
        return $this->get('pdo_driver');
    }

    /**
     * Set the driver name in the DSN string
     * @param string $driver The driver name (mysql, etc.)
     */
    public function setDriver($driver) {
        return $this->set('pdo_driver', (string) $driver);
    }

    /**
     * Quote a value for use in a SQL query
     * Will trigger a database connection using the current DSN if one doesn't
     * exist yet.
     * @param  mixed $value
     * @param  bool  $autoquote If false, strip any quotes added by your Datasource's
     *                          quote algorithm
     * @return string
     */
    final public function quote($value, $autoquote = true) {
        if (!$this->hasConnection()) {
            $this->connect();
        }

        $ret_val = $this->getConnection()->quote($value);
        if (true !== $autoquote) {$ret_val = trim($ret_val, '\'');}

        return $ret_val;
    }
}
