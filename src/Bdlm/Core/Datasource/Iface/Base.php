<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Datasource\Iface;

/**
 * Base interface
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
interface Base {

    /**
     * For PDO-compatible datasources
     */
    const TYPE_PDO = 1;
    const TYPE_PDO_NAME = 'Pdo';
    /**
     * For OCI-compatible datasources
     */
    const TYPE_OCI = 2;
    const TYPE_OCI_NAME = 'Oci';
    /**
     * For Elastic Search compatible datasources
     */
    const TYPE_ES = 3;
    const TYPE_ES_NAME = 'Es';
    /**
     * For Hadoop hBase compatible datasources
     */
    const TYPE_HBASE = 4;
    const TYPE_HBASE_NAME = 'Hbase';

    /**
     * Specified for Datasource interface uniformity
     * Datasources should generally only be used in a model definition so it's
     * helpful if they're all the same.
     *
     * @param int    $type A supported datasource TYPE_* constant
     * @param string $dsn  A Data Source Name (DSN) string, a PDO style format is
     *                     recommended for all data sources:
     *                         '[driver]:host=[host or ip];dbname=[database name]'
     * @return Datasource
     */
    public function __construct($type, $dsn = null, $username = null, $password = null);
    /**
     * Connect to the datasource
     *
     * @return Datasource $this
     */
    public function connect();
    /**
     * Prepare a query object
     *
     * @param  string $query  The query to execute
     * @param  array  $data   Bind data
     * @return Datasource\Pdo
     */
    public function prepareQuery($query);
    /**
     * Get the current connection, else false
     *
     * @return mixed The current connection resource or instance, else false
     */
    public function getConnection();
    /**
     * Set the current connection
     *
     * @param mixed $connection The connection resource or instance to use
     * @return Datasource $this
     */
    public function setConnection($connection);
    /**
     * Get the current DSN string
     *
     * @return string|false The current DSN string, else false
     */
    public function getDsn();
    /**
     * Return the driver portion of the DSN string
     * @return string
     */
    public function getDsnDriver();
    /**
     * Get a value from the DSN string
     * @param  string $key  The name of the value to return
     * @return false|string The named value, or false if it doesn't exist
     */
    public function getDsnValue($key);
    /**
     * Set the DSN string
     *
     * @param string $dsn The Data Source Name or similar connection string
     * @return Datasource $this
     */
    public function setDsn($dsn);
    /**
     * Get the current password string
     *
     * @return string|false The current password string, else false
     */
    public function getPassword();
    /**
     * Set the password string
     *
     * @param string $password The Data Source Name or similar connection string
     * @return Datasource $this
     */
    public function setPassword($password);
    /**
     * Get the current datasource type value
     *
     * @return int|false The current datasource type value, else false
     */
    public function getType($type = null);
    /**
     * Set the datasource type value
     *
     * @param string $password The datasource type value, must be one of the TYPE_* constants
     * @return Datasource $this
     */
    public function setType($type);
    /**
     * Get the current username string
     *
     * @return int|false The current username string, else false
     */
    public function getUsername();
    /**
     * Set the username string
     *
     * @param string $password The username string value
     * @return Datasource $this
     */
    public function setUsername($username);
    /**
     * Quote a value for use in a query
     * Should trigger a database connection using the current DSN if one doesn't
     * exist yet.
     * @param  mixed $value
     * @param  bool  $autoquote If false, strip any quotes added by your Datasource's
     *                          quote algorithm
     * @return string
     */
    public function quote($value, $autoquote = true);
}
