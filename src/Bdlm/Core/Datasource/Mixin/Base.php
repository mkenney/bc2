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
	 * Final for now...
	 *
	 * @param int    $type A supported datasource TYPE_* constant
	 * @param string $dsn  A Data Source Name (DSN) string (generally a PDO compatible
	 *                     DSN)
	 * @return Datasource
	 * @see http://php.net/manual/en/pdo.drivers.php
	 */
	final public function __construct($type, $dsn = null, $username = null, $password = null) {
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
		return ($this->has('connection') ? $this->get('connection') : false);
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
}
