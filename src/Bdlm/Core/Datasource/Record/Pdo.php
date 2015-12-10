<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Datasource\Record;

/**
 * PDO-based data records
 *
 * Manages a single database row
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.0.1
 */
class Pdo extends RecordAbstract {

    /**
     * Copy this row
     * This assumes that the id column is a generated sequence and that setting
     * it to null and forcing a save call will create a new row
     * create a new row.
     * @return bool
     */
    public function copy() {
        $this->set('id', null);
        $this->save(true);
    }

    /**
     * Delete this row.
     * If the table contains a field named "status", it's assumed setting it's
     * value to 'deleted' will delete this row for your application. If
     * $real_delete is true or no status field exists, a standard SQL delete is
     * performed using the `id` column as the primary key.
     *
     * @param boolean $real_delete
     * @return void
     */
    public function deleteRecord($real_delete = false) {
        if ($this->getSchema()->has('status') && !$real_delete) {
            $query = $this->getSchema()->getDatasource()->prepareQuery(
<<<SQL
    UPDATE `{$this->getSchema()->getName()}`
    SET `status` = :status
SQL
            );
            $query->bind('status', 'deleted');

        } else {
            $query = $this->getSchema()->getDatasource()->prepareQuery(
<<<SQL
    DELETE FROM `{$this->getSchema()->getName()}`
    WHERE `id` = :id
SQL
            );
            $query->bind('id', $this->get('id'));
        }
        return $query->execute() && $query->commit();
    }

    /**
     * Create a data string which can be loaded into a new PDO Datasource
     *
     * @return string A Datasource insert statement
     */
    public function dump() {
        $fields = '';
        $values = '';
        foreach ($this->getData() as $key => $value) {
            $key = $this->getSchema()->getDatasource()->quote($key);
            $value = $this->getSchema()->getDatasource()->quote($value);
            $fields .= ($fields ? ", " : '')."`$key`";
            $values .= ($values ? ", " : '') . "'$value'";
        }
        return "INSERT INTO `{$this->getSchema()->getName()}` ($fields) VALUES ($values);";
    }

    /**
     * Load the specified row from the database.
     *
     * @return Datasource\Record\RecordAbstract
     * @throws \RuntimeException An Id hash must be set before data can be loaded
     */
    public function load($force = false) {
        if (!count($this->getId())) {throw new \RuntimeException("An Id hash must be set before data can be loaded");}

        if (!$this->isLoading()) {
            if (!$this->isLoaded() || $force) {
                $this->isLoading(true);
                $this->isLoaded(false);

                $fields = '`'.implode('`,`', $this->getSchema()->arrayKeys()).'`';

                $where = [];
                foreach ($this->getPk() as $field) {
                    $where[] = "`{$field}` = :{$field}";
                }
                $where = (count($where) ? implode(' AND ', $where) : '');

                $query = $this->getSchema()->getDatasource()->prepareQuery(
<<<SQL
    SELECT {$fields}
    FROM `{$this->getSchema()->getName()}`
    WHERE
        {$where}
SQL
                );
                foreach ($this->getPk() as $field) {
                    $query->bind($field, $this->getId()[$field]);
                }

                $query->execute();

                $data = $query->next();
                if (false !== $data) {
                    $this->setData($data->toArray());
                    $this->_clean_data = $this->getData();
                    $this->isDirty(false);
                    $this->isLoaded(true);
                }

                $this->isLoading(false);
            }
        }
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
    public function save($force = false) {
        $ret_val = false;
        if ($this->isDirty() || (bool) $force) {
            $is_new_record = !($this->has('id') && $this->get('id'));

            // Update existing records
            if (!$is_new_record) {

                $sets = [];
                foreach ($this->getSchema()->arrayKeys() as $field) {
                    $sets[] = "`{$field}` = :{$field}";
                }
                $sets = (count($sets) ? implode(',', $sets) : '');

                $query = $this->getSchema()->getDatasource()->prepareQuery(
<<<SQL
    UPDATE `{$this->getSchema()->getName()}`
    SET {$sets}
    WHERE id = :id
    LIMIT 1
SQL
                );

            // No existing row, create a new record.
            } else {
                $this->set('id', 0);
                $fields = '`'.implode('`, `', $this->getSchema()->arrayKeys()).'`';
                $values = ':'.implode(', :', $this->getSchema()->arrayKeys());
                $query = $this->getSchema()->getDatasource()->prepareQuery(
<<<SQL
    INSERT INTO `{$this->getSchema()->getName()}` (
        {$fields}
    ) VALUES (
        {$values}
    )
SQL
                );
            }

            $query->setData($this->getData());
            $ret_val = $query->execute() && $query->commit();
        }
        return $ret_val;
    }
}
