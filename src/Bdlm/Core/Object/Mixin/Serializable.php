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
 * Implementations for \Serializable
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
            '_max' => $this->getMax(),
            '_min' => $this->getMin(),
            '_mode' => $this->getMode(),
            '_name' => $this->getName(),
            '_type' => $this->getType(),
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
        $this->isStatic(isset($data['_is_static']) && (bool) $data['_is_static']);
        if (isset($data['_max'])  && is_numeric($data['_max'])) {$this->setMax($data['_max']);}
        if (isset($data['_max'])  && is_numeric($data['_max'])) {$this->setMin($data['_min']);}
        if (isset($data['_mode']) && $data['_mode'])            {$this->setMode((string) $data['_mode']);}
        if (isset($data['_name']) && $data['_name'])            {$this->setName((string) $data['_name']);}
        if (isset($data['_type']) && $data['_type'])            {$this->setType((string) $data['_type']);}
        if (isset($data['_data']) && is_array($data['_data']))  {$this->setData((array)  $data['_data']);}
    }
}
