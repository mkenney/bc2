<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Model\Mixin;

/**
 * Getter/setter boilerplate for datasource instances
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
trait Base {
    /**
     * Dirty flag
     * @var bool $_dirty
     */
    protected $_dirty = false;
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
     * Columns used for the primary key to select a SINGLE record
     * This data is _always_ used to find and load a row.  For example, if loading a row by id
     * this should contain the array array('id' => 1)
     * @var array $_key_name
     */
    protected $_primary_id = [];
    /**
     * Columns used for the primary key to select a SINGLE record
     * This data is _always_ used to find and load a row.  For example, if loading a row by id
     * this should contain the array array('id' => 1)
     * @var array $_key_name
     */
    protected $_primary_key = ['id'];
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
     * Get/set the dirty flag
     * @param  bool $dirty
     * @return bool
     */
    public function isDirty($dirty = null) {
        if (!is_null($dirty)) {$this->_dirty = (bool) $dirty;}
        return $this->_dirty;
    }
    /**
     * Get/set the loaded flag
     * @param  bool $loaded
     * @return bool
     */
    public function isLoaded($loaded = null) {
        if (!is_null($loaded)) {$this->_loaded = (bool) $loaded;}
        return $this->_loaded;
    }
    /**
     * Get/set the loading flag
     * @param  bool $loading
     * @return bool
     */
    public function isLoading($loading = null) {
        if (!is_null($loading)) {$this->_loading = (bool) $loading;}
        return $this->_loading;
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
            $this->_primary_key[] = $this->getDatasource()->quote((string) $field, false);
        }
        return $this;
    }
}
