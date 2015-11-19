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
class Pdo extends \Bdlm\Core\Datasource\Datasource {

	/**
	 * Default values
	 * @var array
	 */
	protected $_data = [
		'type'   => Datasource::TYPE_PDO,
		'is_oci' => false,
	];

	/**
	 * Always commit on close
	 *
	 * @return void
	 */
	final public function __destruct() {
		if ($this->getConnection() instanceof \PDO) {
			$this->getConnection()->commit();
		}
	}

	/**
	 * Connect to the database
	 *
	 * @return Datasource $this
	 */
	public function connect() {
		$this->setConnection(new \PDO($this->getDsn(), $this->getUsername(), $this->getPassword()));
		$driver = explode(':', $this->getDsn())[0];
		$this->setDriver($driver);
		if ('oci' === $driver) {
			$this->isOci(true);
			// Create OCI workarounds as necessary using oci_* functions because
			// PDO_OCI isn't stable
		}

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
	 * Flag for Oracle, PDO_OCI isn't stable so if this is true, work around it
	 *
	 * @param  boolean|null $is_oci If null, return the current value, else set
	 *                              a new value
	 * @return boolean
	 */
	public function isOci($is_oci = null) {
		if (!is_null($is_oci)) {$this->set('is_oci', (bool) $is_oci);}
		return $this->get('is_oci');
	}

	/**
	 * Get the driver name from the DSN string
	 * @return [type] [description]
	 */
	public function getDriver() {
		return $this->get('pdo_driver');
	}

	/**
	 * Get the driver name from the DSN string
	 * @param string $driver The driver name (mysql, oci, etc.)
	 */
	public function setDriver($driver) {
		return $this->set('pdo_driver', (string) $driver);
	}
}
