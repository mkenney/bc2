<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Datasource\Record;
use Bdlm\Core\Datasource;
use Bdlm\Core\Datasource\Schema;
use Bdlm\Core;

/**
 * Abstract interface for representing a single record from a Datasource
 *
 * Requires implementing
 *     copy()
 *         Copy the represented record to a new row/blob/whatever in your
 *         Datasource
 *
 *     deleteRecord($real_delete = false)
 *         Mark the represented record as deleted by default with an option to
 *         override and remove the record permanently
 *
 *     dump()
 *         Create a file or data blob containing the current record's data
 *         formatted for insertion into a data source of same type as the
 *         one containing the represented record
 *
 *     load()
 *         Load the data for a record specified by the current 'id' value
 *         @todo Unfortunately, this expects a field in your schema called 'id'
 *             that is the primary key for the represented record. I'll add
 *             custom key field names and multi-field keys after I have all
 *             this working right.
 *
 *     reset($field = null)
 *         Reset all fields to their default value. If data has not been loaded
 *         by calling 'load()' then set the field to it's default value specified
 *         by the Datasource's Schema.  If the Schema doesn't specify a default
 *         value, set it to null.
 *
 *         Optionally, specify an individual field to reset. Follow the same
 *         rules for individual fields.
 *
 *     save()
 *         Save any changes to this record to the current Datasource, optionally
 *         as a new record. Saving as a new record implies a copy() operation
 *         rather than 'moving' the current record in some way.
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 2.0.0
 */
abstract class RecordAbstract implements
    Core\Object\Iface\Base
    , \ArrayAccess
    , \Countable
    , \Iterator
    , \Serializable
{

    /**
     * Object base
     */
    use Core\Object\Mixin\Base {
        Core\Object\Mixin\Base::get   as _coreGet;
        Core\Object\Mixin\Base::set   as _coreSet;
        Core\Object\Mixin\Base::reset as _coreReset;
    }

    /**
     * ArrayAccess
     */
    use Core\Object\Mixin\ArrayAccess;

    /**
     * Countable
     */
    use Core\Object\Mixin\Countable;

    /**
     * Iterator
     */
    use Core\Object\Mixin\Iterator;

    /**
     * Magic
     */
    use Core\Object\Mixin\Magic;

    /**
     * Serializable
     */
    use Core\Object\Mixin\Serializable;

    /**
     * Datasource boilerplate
     */
    use Datasource\Mixin\Boilerplate;

    /**
     * Schema boilerplate
     */
    use Schema\Mixin\Boilerplate;

    /**
     * The origial "clean" data loaded by load()
     * This is used by the reset() method.
     * @var array $_clean_data
     */
    protected $_clean_data = [];
    /**
     * Dirty flag
     * @var bool $_dirty
     */
    protected $_dirty = false;
    /**
     * Store error messages for later
     * @var array $_error_messages
     */
    protected $_error_messages = [];
    /**
     * Columns used for the primary key to select a SINGLE record
     * This data is _always_ used to find and load a row.  For example, if loading a row by id
     * this should contain the array array('id' => 1)
     * @var array $_key_name
     */
    protected $_primary_key = ['id'];
    /**
     * Columns used for the primary key to select a SINGLE record
     * This data is _always_ used to find and load a row.  For example, if loading a row by id
     * this should contain the array array('id' => 1)
     * @var array $_key_name
     */
    protected $_primary_id = [];
    /**
     * Loaded flag
     * @var bool $_loaded
     */
    protected $_loaded = false;
    /**
     * Loading flag
     * @var bool $_loading
     */
    protected $_loading = false;

    /**
     * Set the Schema and initialize
     *
     * @param Schema\Iface\Base $schema   Object representing this record's schema
     *                                    or table
     * @param array|\Bdlm\Core\Object $id Optional, the value of the primary key
     * @return RecordAbstract
     */
    final public function __construct(
        Schema\Iface\Base $schema
        , array $primary_key
        , array $primary_id = null
    ) {
        $this->setSchema($schema);
        $this->setPk($primary_key);
        if (!is_null($primary_id)) {
            $this->setId($primary_id);
        }
    }
    /**
     * Copy this record to a new record
     *
     * @return RecordAbstract
     */
    public abstract function copy();
    /**
     * Mark this record as deleted in the current Datasource
     *
     * If $real_delete is true completely remove the record from the Datasource
     *
     * @param boolean $real_delete
     * @return RecordAbstract
     */
    public abstract function deleteRecord($real_delete = false);
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
    public function describe() {
        return $this->getSchema()->describe();
    }
    /**
     * Create an dump file which can be loaded into a new Datasource
     *
     * Formatting for your Datasource implementation (SQL, ElasticeSearch, etc.)
     * is dependent on the Datasource type
     *
     * @return string A Datasource insert statement or data blob
     */
    public abstract function dump();
    /**
     * Add an error message to the stack
     *
     * @param string $message
     * @return RecordAbstract
     */
    public function addError($message) {
        $this->_error_messages[] = (string) $message;
        return $this;
    }
    /**
     * Get/set error messages.
     * @return array All messages
     */
    public function getErrors() {
        return $this->_error_messages;
    }
    /**
     * Get/set error messages.
     * @param array $messages
     * @return RecordAbstract
     */
    public function setErrors(array $messages) {
        $this->_error_messages = $messages;
        return $this;
    }
    /**
     * Get the field names for this table.
     * @return array An array containing the field names.
     */
    public function fields() {
        return $this->arrayKeys();
    }
    /**
     * Get the value of a specific field
     *
     * @param string $field The field name.
     * @return mixed The value of that field.
     */
    public function get($field, $default = null) {
        if (!$this->getSchema()->has($field)) {
            throw new \RuntimeException("The field '{$field}' does not exist in the schema '{$this->getSchema()->getName()}'");
        }
        $ret_val = $this->_coreGet($field);
        return $ret_val;
    }
    /**
     * Get the unique identifier (primary key) for this record.
     *
     * @return int|string The unique ID, or a string of all keys if it's multi-keyed.
     * @throws Bdlm_Exception
     */
    public function getId() {
        return $this->_primary_id;
    }
    /**
     * Get the columns that make up the unique record identifier
     *
     * @return array          The list of field names that define the primary key
     * @throws Bdlm_Exception
     */
    public function getPk() {
        return $this->_primary_key;
    }
    /**
     * [isDirty description]
     * @param  bool $dirty
     * @return bool
     */
    public function isDirty($dirty = null) {
        if (!is_null($dirty)) {$this->_dirty = (bool) $dirty;}
        return $this->_dirty;
    }

    /**
     * [isDirty description]
     * @param  bool $dirty
     * @return bool
     */
    public function isLoaded($loaded = null) {
        if (!is_null($loaded)) {$this->_loaded = (bool) $loaded;}
        return $this->_loaded;
    }

    /**
     * [isDirty description]
     * @param  bool $dirty
     * @return bool
     */
    public function isLoading($loading = null) {
        if (!is_null($loading)) {$this->_loading = (bool) $loading;}
        return $this->_loading;
    }

    /**
     * Load the specified row from the database
     *
     * On success, set $this->_clean_data to a COPY of the raw data and set
     * the dirty flag to false.
     *
     * @return RecordAbstract
     */
    public abstract function load();
    /**
     * Clear data.
     * This will set all fields to an empty string, except certain "standard"
     * fields (id, status, etc.).
     * @param string $field The name of a specific field to reset.  <b>If left out, all fields will be reset</b>.
     * @return bool True on success, else false
     */
    public function reset($field = null) {
        if (!is_null($field)) {
            $this->resetField($field);

        } else {
            foreach ($this->arrayKeys() as $field) {
                $this->resetField($field);
            }
            $this->isDirty(false);
        }

        return $this;
    }
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
    public function resetField($field) {
        $dirty = $this->isDirty();

        // Specified field doesn't exist in the current schema, make sure references
        // to it are removed from the current data set
        if (!$this->getSchema()->has($field)) {
            $this->delete($field);

        // Restore the value from the last load() call if possible
        } else if (isset($this->_clean_data[$field])) {
            $this->set($field, $this->_clean_data[$field]);

        // Set the value to the schema's default value
        } else {
            $default = $this->describe()[$field]['default'];
            if ('NULL' === $default) {
                $default = null;
            } else {
                $default = trim($default);
            }
            $this->set($field, $default);
        }
        $this->isDirty($dirty);
        return $this;
    }
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
    public abstract function save($force = true);

    /**
     * Set the value of a field.
     *
     * If no such field name exists, then call the Object set() method to set
     * the value of a key.
     *
     * @param string $field The field name.
     * @param mixed $value The value to store in the field.
     * @return RecordAbstract
     * @throws \RuntimeException If the specified field name does not exist in the current Schema
     */
    public function set($field, $value) {
        // Only set if the key actually exists in this table
        if (!$this->getSchema()->has($field)) {
            throw new \RuntimeException("The field '{$field}' does not exist in the schema '{$this->getSchema()->getName()}'");
        }

        if ($this->get($field) !== $value) {
            $this->isDirty(true);
            $this->_coreSet($field, $value);
        }

        return $this;
    }
    /**
     * Set the unique identifier (primary key) for this record.
     *
     * @param  array $id      A hash of key => value pairs that satisfy the primary
     *                        key definition
     * @return RecordAbstract
     * @throws Bdlm_Exception
     */
    public function setId(array $id) {
        $this->_primary_id = [];
        foreach ($this->getPk() as $field) {
            if (!isset($id[$field])) {
                $this->_primary_id = [];
                throw new \RuntimeException("'{$field}' is a required field in the primary key");
            }
            $this->_primary_id[$field] = $id[$field];
            $this->set($field, $id[$field]);
        }
        return $this;
    }
    /**
     * Set the unique identifier (primary key) for this record.
     *
     * @param  array $pk      The list of field names that define the primary key
     * @return RecordAbstract
     */
    public function setPk(array $pk) {
        $this->_primary_key = [];
        foreach ($pk as $field) {
            $this->_primary_key[] = $this->getSchema()->getDatasource()->quote((string) $field, false);
        }
        return $this;
    }
}