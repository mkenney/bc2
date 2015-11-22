<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Datasource\Mixin;

use \Bdlm\Core;

/**
 * Implementations for \Bdlm\Core\Datasource\Iface\Base
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.1.0
 */
trait Base {

    /**
     * @param int    $type A supported datasource TYPE_* constant
     * @param string $dsn  A Data Source Name (DSN) string, a PDO style format is
     *                     recommended for all data sources:
     *                         '[driver]:host=[host or ip];dbname=[database name]'
     * @return Datasource
     */
    public function __construct($type, $dsn = null, $username = null, $password = null) {
        $this->setType($type);
        if (!is_null($dsn))      {$this->setDsn($dsn);}
        if (!is_null($username)) {$this->setUsername($username);}
        if (!is_null($password)) {$this->setPassword($password);}
    }

    /**
     * Connect to the datasource
     *
     * @return Datasource $this
     */
    public abstract function connect();

    /**
     * Prepare a query object
     *
     * @param  string $query  The query to execute
     * @param  array  $data   Bind data
     * @return Datasource\Pdo
     */
    public abstract function prepareQuery($query);

////////////////////////////////////////////////////////////////////////
// Boilerplate
////////////////////////////////////////////////////////////////////////

    /**
     * Get the current connection, else false
     *
     * @return mixed The current connection resource or instance, else false
     */
    final public function getConnection() {
        return ($this->hasConnection() ? $this->get('connection') : false);
    }
    /**
     * Check to see if a connection exists
     *
     * @return bool
     */
    final public function hasConnection() {
        return $this->has('connection');
    }
    /**
     * Set the current connection
     *
     * @param mixed $connection The connection resource or instance to use
     * @return Datasource $this
     */
    final public function setConnection($connection) {
        return $this->set('connection', $connection);
    }
    /**
     * Get the current DSN string
     *
     * @return string|false The current DSN string, else false
     */
    final public function getDsn() {
        return ($this->has('dsn') ? (string) $this->get('dsn') : false);
    }
    /**
     * Return the driver portion of the DSN string
     * @return string
     */
    final public function getDsnDriver() {
        return explode(':', $this->getDsn)[0];
    }
    /**
     * Get a value from the DSN string
     * @param  string $key  The name of the value to return
     * @return false|string The named value, or false if it doesn't exist
     */
    final public function getDsnValue($key) {
        $ret_val = false;
        $dsn_values = explode(';', explode(':', $this->getDsn)[1]);
        foreach ($dsn_values as $value) {
            $value = explode('=');
            if ($key === $value[0]) {
                $ret_val = $value[1];
            }
        }
        return $ret_val;
    }
    /**
     * Set the DSN string
     *
     * @param string $dsn The Data Source Name or similar connection string
     * @return Datasource $this
     */
    final public function setDsn($dsn) {
        return $this->set('dsn', (string) $dsn);
    }
    /**
     * Get the current password string
     *
     * @return string|false The current password string, else false
     */
    final public function getPassword() {
        return ($this->has('password') ? (string) $this->get('password') : false);
    }
    /**
     * Set the password string
     *
     * @param string $password The Data Source Name or similar connection string
     * @return Datasource $this
     */
    final public function setPassword($password) {
        return $this->set('password', (string) $password);
    }
    /**
     * Get the current datasource type value
     *
     * @return int|false The current datasource type value, else false
     */
    final public function getType($type = null) {
        return ($this->has('type') ? (int) $this->get('type') : false);
    }
    /**
     * Get the current datasource type's name
     * @return string|false The current datasource type's name value, else false
     */
    final public function getTypeString($type = null) {
        $ret_val = false;
        switch ($this->getType()) {
            case \Bdlm\Core\Datasource\Iface\Base::TYPE_PDO:   $ret_val = \Bdlm\Core\Datasource\Iface\Base::TYPE_PDO_NAME;   break;
            case \Bdlm\Core\Datasource\Iface\Base::TYPE_OCI:   $ret_val = \Bdlm\Core\Datasource\Iface\Base::TYPE_OCI_NAME;   break;
            case \Bdlm\Core\Datasource\Iface\Base::TYPE_ES:    $ret_val = \Bdlm\Core\Datasource\Iface\Base::TYPE_ES_NAME;    break;
            case \Bdlm\Core\Datasource\Iface\Base::TYPE_HBASE: $ret_val = \Bdlm\Core\Datasource\Iface\Base::TYPE_HBASE_NAME; break;
        }
        return $ret_val;
    }
    /**
     * Set the datasource type value
     *
     * @param string $password The datasource type value, must be one of the TYPE_* constants
     * @return Datasource $this
     */
    final public function setType($type) {
        if (!in_array($type, $this->getConstants())) {throw new \RuntimeException("Invalid type '{$type}'");}
        return $this->set('type', (int) $type);
    }
    /**
     * Get the current username string
     *
     * @return int|false The current username string, else false
     */
    final public function getUsername() {
        return ($this->has('username') ? $this->get('username') : false);
    }
    /**
     * Set the username string
     *
     * @param string $password The username string value
     * @return Datasource $this
     */
    final public function setUsername($username) {
        return $this->set('username', (string) $username);
    }

    /**
     * Quote a value for use in a query
     * Should trigger a database connection using the current DSN if one doesn't
     * exist yet.
     * @param  mixed $value
     * @param  bool  $autoquote If false, strip any quotes added by your Datasource's
     *                          quote algorithm
     * @return string
     */
    public abstract function quote($value, $autoquote = true);
}
