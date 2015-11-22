<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Datasource\Query\Iface;
use \Bdlm\Core\Datasource;

/**
 * Base interface
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
interface Base extends \Bdlm\Core\Object\Iface\Base {

    /**
     * @param \Bdlm\Core\Datasource\Iface\Base $datasource A datasource to query against
     * @param string                              $query      A query string to execute
     * @param array                               $data       Any data to bind to the query
     */
    public function __construct(
        \Bdlm\Core\Datasource\Iface\Base $datasource
        , $query = ''
        , $data = []
    );

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
    public function bind($var, $val = '');

    /**
     * Commit changes, if any
     *
     * @return boolean
     */
    public function commit();

    /**
     * Execute the current statement
     * @param int $mode
     * @return boolean
     * @todo Implement remaining execute() logic (cursors...)
     */
    public function execute($bind_data = null);

    /**
     * Get the current query string
     * @return string
     */
    public function getQuery();

    /**
     * Get the current result
     * @return string
     */
    public function getResult();

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
    public function limit($start, $rows);

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
    public function next($mode = null);

    /**
     * Reset the data source pointer to the beginning of the data set. Re-execute
     * the query if necessary for a particular data soruce (oracle...)
     *
     * @return bool
     */
    public function reset();

    /**
     * Rollback changes, if any
     * @return boolean
     */
    public function rollback();

    /**
     * Return a finalized string representation of the current query with any bound
     * variable values properly escaped and injected if available, otherwise output
     * the query variables.
     *
     * @return string
     */
    public function toString();

    /**
     * Set the query string to execute against the datasource
     * @param string $query
     */
    public function setQuery($query);

    /**
     * Set the current result
     * @param  false|\Bdlm\Core\Object\Iface\Base $result
     * @return \Bdlm\Core\Datasource\Query\Iface\Base
     */
    public function setResult($result);
}
