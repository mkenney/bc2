<?php

namespace Test\Bdlm\Core\Object;

class ArrayAccessTest extends \PHPUnit_Framework_TestCase {
    protected $object = null;
    protected function _getTestObject() {
        return new \Bdlm\Core\Object([
            'one' => 'foo',
            'two' => 'bar',
        ]);
    }

//////////////////////////////////////////////////////////////////////////////

    public function testSet() {
        $object = $this->_getTestObject();
        $this->assertFalse($object->has('three'));
        $object['three'] = 'baz';
        $this->assertEquals('baz', $object->get('three'));
    }

    public function testIsset() {
        $object = $this->_getTestObject();
        $this->assertTrue(isset($object['one']));
        $this->assertTrue(isset($object['two']));
        $this->assertFalse(isset($object['three']));
    }

    public function testUnset() {
        $object = $this->_getTestObject();
        $this->assertTrue(isset($object['one']));
        unset($object['one']);
        $this->assertFalse(isset($object['one']));
    }

    public function testAccess() {
        $object = $this->_getTestObject();
        $this->assertEquals('foo', $object['one']);
        $this->assertEquals('bar', $object['two']);
    }
}
