<?php

namespace Test\Bdlm\Core\Object;

class SerializableTests extends \PHPUnit_Framework_TestCase {
    protected $object = null;
    protected function _getTestObject() {
        return new \Bdlm\Core\Object([
            'one' => 'foo',
            'two' => 'bar',
        ]);
    }

//////////////////////////////////////////////////////////////////////////////

    public function testSerialize() {
        $object = $this->_getTestObject();
        $serialized_object = serialize($object);
        $object2 = unserialize($serialized_object);
        $this->assertEquals($object, $object2);
    }
}
