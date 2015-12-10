<?php
/**
 * Bedlam CORE 2
 *
 * @link      https://github.com/mkenney/bc2 Source repository
 * @copyright 2005 - present Michael Kenney <mkenney@webbedlam.com>
 * @license   https://github.com/mkenney/bc2/blob/master/LICENSE The MIT License (MIT)
 */

namespace Bdlm\Core\Object\Mixin;

/**
 * Implementations of \Serializable for objects that use Object\Mixin\Base
 *
 * @author Michael Kenney <mkenney@webbedlam.com>
 * @package Bdlm
 * @version 0.1.0
 */
trait Serializable {

    /**
     * Serializable implementation of serialize()
     * @return string The serialized _data array
     */
    public function serialize() {
        return serialize([
            '_is_static' => $this->isStatic(),
            '_max' => $this->max(),
            '_min' => $this->min(),
            '_mode' => $this->mode(),
            '_name' => $this->name(),
            '_type' => $this->type(),
            '_data' => $this->getData(),
        ]);
    }

    /**
     * Serializable implementation of serialize()
     * @param string $data A serialized instance of ObjectAbstract
     * @return void
     */
    public function unserialize($data) {
        $data = unserialize($data);
        $this->isStatic($data['_is_static']);
        $this->max($data['_max']);
        $this->min($data['_min']);
        $this->mode($data['_mode']);
        $this->name($data['_name']);
        $this->type($data['_type']);
        $this->setData($data['_data']);
    }
}
