<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Model\Iface;

use \Bdlm\Core;

/**
 * Base model interface
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
interface Base extends \Bdlm\Core\Object\Iface\Base {
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
     * @param  bool $loaded
     * @return bool
     */
    public function isLoaded($loaded = null);
    /**
     * Get/set the loading flag
     * @param  bool $loading
     * @return bool
     */
    public function isLoading($loading = null);
    /**
     * Load data relevant to this object
     *
     * load() should always return the current instance or throw an exception on
     * failure.
     *
     * @return Core\Model\Iface\Base $this
     * @throws \Exception on failure
     */
    public function load();
    /**
     * Save this object's data to appropriate data storage
     *
     * save() should always return the current instance or throw an exception on
     * failure.
     *
     * @param  boolean                 $as_new If true, save this object's data as a new record
     * @return Core\Model\Iface\Base $this
     */
    public function save($force = false);
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
