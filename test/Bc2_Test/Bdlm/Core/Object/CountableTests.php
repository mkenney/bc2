<?php

namespace Bc2_Test\Bdlm\Core\Object;

class CountableTest extends \PHPUnit_Framework_TestCase {
    protected $object = null;
    protected function _getTestObject() {
        return new \Bdlm\Core\Object([
            'one' => 'foo',
            'two' => 'bar',
        ]);
    }

//////////////////////////////////////////////////////////////////////////////

    public function testCount() {
        $object = $this->_getTestObject();
        $this->assertEquals(2, count($object));
        unset($object['one']);
        $this->assertEquals(1, count($object));
    }
}
