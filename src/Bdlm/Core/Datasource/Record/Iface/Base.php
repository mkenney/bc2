<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Datasource\Record\Iface;

/**
 * Base model interface
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
interface Base extends \Bdlm\Core\Object\Iface\Base {
    /**
     * Copy this record to a new record
     *
     * @return RecordAbstract
     */
    public function copy();
    /**
     * Mark this record as deleted in the current Datasource
     *
     * If $real_delete is true completely remove the record from the Datasource
     *
     * @param boolean $real_delete
     * @return RecordAbstract
     */
    public function deleteRecord($real_delete = false);
    /**
     * Describe this row by returning a list of the columns as a comma-delimited string.
     * Useful for inserting into queries.
     *
     * @return string JSON blob describing the fields in this record's schema:
     *                {
     *                  "field name": {
     *                    "metadata property 1": "value or description"
     *                    , "metadata property 2": "value or description"
     *                    ...
     *                  }
     *                }
     */
    public function describe();
    /**
     * Create a data string which can be loaded into a new Datasource
     *
     * Formatting for your Datasource implementation (SQL, ElasticeSearch, etc.)
     * is dependent on the Datasource type
     *
     * @return string A Datasource insert statement or data blob
     */
    public function dump();
    /**
     * Add an error message to the stack
     *
     * @param string $message
     * @return RecordAbstract
     */
    public function addError($message);
    /**
     * Get/set error messages.
     * @return array All messages
     */
    public function getErrors();
    /**
     * Get/set error messages.
     * @param array $messages
     * @return RecordAbstract
     */
    public function setErrors(array $messages);
    /**
     * Get the field names for this table.
     * @return array An array containing the field names.
     */
    public function fields();
    /**
     * Get the unique identifier (primary key) for this record.
     *
     * @return int|string The unique ID, or a string of all keys if it's multi-keyed.
     * @throws Bdlm_Exception
     */
    public function getId();
    /**
     * Get the columns that make up the unique record identifier
     *
     * @return array          The list of field names that define the primary key
     * @throws Bdlm_Exception
     */
    public function getPk();
    /**
     * Get/set the dirty flag
     * @param  bool $dirty
     * @return bool
     */
    public function isDirty($dirty = null);
    /**
     * Get/set the loaded flag
     * @param  bool $dirty
     * @return bool
     */
    public function isLoaded($loaded = null);
    /**
     * Get/set the loading flag
     * @param  bool $dirty
     * @return bool
     */
    public function isLoading($loading = null);
    /**
     * Load the specified row from the database
     *
     * On success, set $this->_clean_data to a COPY of the raw data and set
     * the dirty flag to false.
     *
     * @return RecordAbstract
     */
    public function load();
    /**
     * Clear data.
     * This will set all fields to an empty string, except certain "standard"
     * fields (id, status, etc.).
     * @param string $field The name of a specific field to reset.  <b>If left out, all fields will be reset</b>.
     * @return bool True on success, else false
     */
    public function reset($field = null);
    /**
     * Reset data for a specific field name
     *
     * Reset the field value to it's initial value from a load() call or to the
     * field's default value in the current schema. If no default value is
     * available, set it to an empty string.
     *
     * @param  string $field  The name of a specific field to reset. If left out,
     *                        all fields will be reset.
     * @return RecordAbstract
     */
    public function resetField($field);
    /**
     * Save this record to the database.
     * If it's an existing record (already loaded) then saving should overwrite
     * the data. If it's new, saving should create a new record and update the
     * key field with the new unique identifier.
     *
     * Note that the data will NOT be saved if it has not been changed; that is,
     * if the dirty flag is still 'false'.
     *
     * @param  bool $force    If true, force an update process even if the dirty
     *                        flag is false
     * @return RecordAbstract
     */
    public function save($force = true);
    /**
     * Set the unique identifier (primary key) for this record.
     *
     * @param  array $id      A hash of key => value pairs that satisfy the primary
     *                        key definition
     * @return RecordAbstract
     * @throws Bdlm_Exception
     */
    public function setId(array $id);
    /**
     * Set the unique identifier (primary key) for this record.
     *
     * @param  array $pk      The list of field names that define the primary key
     * @return RecordAbstract
     */
    public function setPk(array $pk);
}
