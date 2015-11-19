<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Datasource\Query;

/**
 * Datasource query abstraction
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
abstract class QueryAbstract extends \Bdlm\Core\Object {


	const EXCEPTION_NO_DATA_SOURCE = 1;

	protected $_datasource = null;
	protected $_query = '';
	protected $_result = false;

	/**
	 * @param \Bdlm\Core\Datasource\Iface\Base $datasource A datasource to query against
	 * @param string                              $query      A query string to execute
	 * @param array                               $data       Any data to bind to the query
	 */
	final public function __construct(\Bdlm\Core\Datasource\Iface\Base $datasource, $query = '', $data = []) {
		$this->setDatasource($datasource);
		$this->setQuery($query);
		$this->setData($data);
	}

	/**
	 * Bind values to query variables
	 *
	 * This should use late binding, so variables should just be stored locally
	 * until this query is executed
	 *
	 * @param  string|array|\Bdlm\Core\ObjectAbstract $var If an array or \Bdlm\Core\ObjectAbstract,
	 *                                                key->value pairs are bound and $val is ignored,
	 *                                                else $val is bound to a variable called $var
	 *                                                in the statement
	 * @param  $val                         The value to bind in the statement
	 * @return QueryAbstract                Alwasy return the current instance
	 */
	final public function bind($var, $val = '') {
		if (is_array($var) || $var instanceof \Bdlm\Core\ObjectAbstract) {
			foreach ($var as $k => $v) {
				$this->set($k, $v);
			}
		} else {
			$this->set($var, $val);
		}
		return $this;
	}

	/**
	 * Commit changes, if any
	 *
	 * @return boolean
	 */
	public abstract function commit();

	/**
	 * Execute the current statement
	 * @param int $mode
	 * @return boolean
	 * @todo Implement remaining execute() logic (cursors...)
	 */
	public abstract function execute($mode = OCI_NO_AUTO_COMMIT);

	/**
	 * MySQL-like limit functionality for pagination.
	 *
	 * Cannot be called before a query has been defined, if a defined query is
	 * not a SELECT statement or after a query has been executed.  Limits data
	 * using the method found here:
	 * http://www.oracle.com/technetwork/issue-archive/2006/06-sep/o56asktom-086197.html
	 *
	 * @param int $start
	 * @param int $rows
	 * @return void
	 */
	public abstract function limit($start, $rows);

	/**
	 * Fetch the next row of data as an object
	 *
	 * Always automatically manage LOB or other data that requires special processing
	 *
	 * @return \Bdlm\Core\ObjectAbstract
	 * @throws \RuntimeException    If the query cannot be executed for any reason.
	 *                              Be sure to include a descriptive exception
	 *                              message.
	 */
	public function next($mode = null) {
		throw new \RuntimeException('Method not implemented');
	}

	/**
	 * Reset the data source pointer to the beginning of the data set. Re-execute
	 * the query if necessary for a particular data soruce (oracle...)
	 *
	 * @return bool
	 */
	public function reset() {
		throw new \RuntimeException('Method not implemented');
	}

	/**
	 * Rollback changes, if any
	 * @return boolean
	 */
	public abstract function rollback();

	/**
	 * Return a finalized string representation of the current query with any bound
	 * variable values properly escaped and injected if available, otherwise output
	 * the query variables.
	 *
	 * @return string
	 */
	public function toString() {
		throw new \RuntimeException('Method not implemented');
	}

////////////////////////////////////////////////////////////////////////
// Boilerplate
////////////////////////////////////////////////////////////////////////

	/**
	 * Get the current datasource object
	 * @return \Bdlm\Core\Datasource\Iface\Base
	 * @throws \RuntimeException If the current datasource is invalid
	 */
	public function getDatasource() {
		if (!$this->_datasource instanceof \Bdlm\Core\Datasource\Iface\Base) {
			throw new \RuntimeException("No valid data source has been set", self::EXCEPTION_NO_DATA_SOURCE);
		}
		return $this->_datasource;
	}
	/**
	 * Set a datasource object to use
	 * @param \Bdlm\Core\Datasource\Iface\Base $datasource
	 * @return  QueryAbstract $this
	 */
	public function setDatasource(\Bdlm\Core\Datasource\Iface\Base $datasource) {
		$this->_datasource = $datasource;
		return $this;
	}
	/**
	 * Get the current query string
	 * @return string
	 */
	public function getQuery() {
		return (string) $this->_query;
	}
	/**
	 * Set the query string to execute against the datasource
	 * @param string $query
	 */
	public function setQuery($query) {
		$this->_query = (string) $query;
		return $this;
	}
	/**
	 * Get the current result
	 * @return string
	 */
	public function getResult() {
		return $this->_result;
	}
	/**
	 * Set the current result
	 * @param string $result
	 */
	public function setResult($result) {
		if (false !== $result && !$result instanceof \Bdlm\Core\Object\Iface\Base) {
			throw new \RuntimeException('Invalid result set given');
		}
		$this->_result = $result;
		return $this;
	}
}
