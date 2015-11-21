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
 * PDO-based data queries
 *
 * Manages a single database statement
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
class Pdo extends QueryAbstract {

	protected $_pdo_statement = null;
	protected $_is_executed = false;

	/**
	 * Commit changes, if any
	 *
	 * @return boolean
	 */
	public function commit() {
		return $this->getDatasource()->commit();
	}

	/**
	 * Execute the current statement
	 * @param int $mode
	 * @return boolean
	 * @todo Implement remaining execute() logic (cursors...)
	 */
	public function execute($bind_data = null) {
		if (!is_null($bind_data)) {$this->setData(array_merge($this->getData(), $bind_data));}
		foreach ($this->getData() as $var => $val) {
			$this->getStatement()->bindValue($var, $val);
		}
		$this->_is_executed = true;
		return $this->getStatement()->execute();
	}

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
	public function limit($start, $rows) {
		$start = (int) $start;
		$rows  = (int) $rows;
		$end   = (int) $start + $rows;

		if ($this->_is_executed) {throw new \RuntimeException('The current query has already been executed, you must add your limit clause before executing any queries');}
		if (strtoupper(substr(trim($this->getQuery()), 0, 6)) !== 'SELECT') {throw new \RuntimeException('Only SELECT queries support limit functionality');}
		if ($start < 0) {throw new \RuntimeException('The start row must be greater than or equal to 0');}
		if ($rows < 0)  {throw new \RuntimeException('The number of rows must be greater than or equal to 0');}

		switch ($this->getDatasource()->getDriver()) {

			// Oracle style limit clause
			case 'oci':
				$this->setQuery(
<<<SQL
SELECT *
FROM (
	SELECT subq.*, ROWNUM minrow_d6e9471e782d42 -- Random name that one one will use...
	FROM (
		{$this->getQuery()}
	) subq
	WHERE ROWNUM < {$end}
) WHERE minrow_d6e9471e782d42 >= {$start}
SQL
				);
			break;

			// mySQL style limit clause
			case 'mysql'; // break omitted
			default:
				$this->setQuery(
<<<SQL
SELECT *
FROM (
	{$this->getQuery()}
) subq
LIMIT {$start}, {$rows}
SQL
				);
			break;

		}

		return $this;
	}

	/**
	 * Fetch the next row of data as an object
	 *
	 * @return \Bdlm\Core\ObjectAbstract
	 * @throws \RuntimeException    If the query cannot be executed for any reason.
	 *                              Be sure to include a descriptive exception
	 *                              message.
	 */
	public function next($mode = null) {
		if (!$this->_is_executed) {$this->execute();}

		if ($this->getDatasource()->isOci()) {

			$oci_lob = 'OCI-Lob';
			$result = \oci_fetch_array($this->get("statement_resource"), OCI_ASSOC + OCI_RETURN_NULLS);
			if (is_array($result)) {
				foreach ($result as $k => $v) {
					if ($v instanceof $oci_lob) {
						$result[$k] = $v->read($v->size());
					}
				}
			}
		} else {

			$result = $this->getStatement()->fetch(\PDO::FETCH_ASSOC);
		}

		if (is_array($result)) {
			$result = new \Bdlm\Core\Object($result);
		}
		$this->setResult($result);
		return $this->getResult();
	}

	/**
	 * Reset the data source pointer to the beginning of the data set. Re-execute
	 * the query if necessary for a particular data soruce (oracle...)
	 *
	 * @return bool
	 */
	public function reset() {
		$this->execute();
	}

	/**
	 * Rollback changes, if any
	 * @return boolean
	 */
	public function rollback() {
		if ($this->getDatasource()->isOci()) {
			$ret_val = oci_rollback($this->getDatasource());
		} else {
			$ret_val = $this->getDatasource()->getConnection()->rollBack();
		}
		return $ret_val;
	}


	/**
	 * Return a finalized string representation of the current query with any bound
	 * variable values properly escaped and injected if available, otherwise output
	 * the query variables.
	 *
	 * @return string
	 * @throws \RuntimeException If no query has been defined
	 */
	public function toString() {
		if ('' === trim($this->getQuery())) {
			throw new \RuntimeException('No query given');
		}

		$query = $this->getQuery();
		foreach ($this->getData() as $k => $v) {
			if (is_array($v)) {$v = implode(',', $v);}

			if ($this->getDatasource()->isOci()) {
				$quote_char = '';
				if (!is_numeric($v)) {$quote_char = "'";}
				$v = $quote_char.addslashes($v).$quote_char;

			} else {
				$v = $this->getDatasource()->quote($v);
			}

			$query = preg_replace("/:".addslashes($k)."(\\b)/", $v.'\\1', $query);
		}

		return $query;
	}

	public function getStatement() {
		if (!$this->_pdo_statement instanceof \PDOStatement) {
			$this->_pdo_statement = $this->getDatasource()->getConnection()->prepare($this->getQuery());
		}
		return $this->_pdo_statement;
	}

	public function setStatement(\PDOStatement $statement) {
		$this->_pdo_statement = $statement;
		return $this;
	}
}
