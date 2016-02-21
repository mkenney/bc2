<?php

namespace Bc2_Test\Bdlm\Core\Object;

class IteratorTests extends \PHPUnit_Framework_TestCase {
    protected $object = null;
    protected function _getTestObject() {
        return new \Bdlm\Core\Object([
            'one' => 'foo',
            'two' => 'bar',
        ]);
    }

//////////////////////////////////////////////////////////////////////////////

    public function testCurrent() {
        $object = $this->_getTestObject();
        $this->assertEquals('foo', $object->current());
    }

    public function testEach() {
        $object = $this->_getTestObject();
        $this->assertEquals(['one', 'foo', 'key' => 'one', 'value' => 'foo'], $object->each());
    }

    public function testEnd() {
        $object = $this->_getTestObject();
        $this->assertEquals('bar', $object->end());
        $this->assertEquals('two', $object->key());
    }

    public function testKey() {
        $object = $this->_getTestObject();
        $this->assertEquals('one', $object->key());
    }

    public function testNext() {
        $object = $this->_getTestObject();
        $this->assertEquals('bar', $object->next());
        $this->assertEquals('two', $object->key());
        $this->assertFalse($object->next());
    }

    public function testPrev() {
        $object = $this->_getTestObject();
        $this->assertFalse($object->prev());
        $object->end();
        $this->assertEquals('foo', $object->prev());
        $this->assertEquals('one', $object->key());
    }

    public function testRewind() {
        $object = $this->_getTestObject();
        $this->assertEquals('bar', $object->end());
        $this->assertEquals('two', $object->key());
        $this->assertEquals('foo', $object->rewind());
        $this->assertEquals('one', $object->key());
    }

    public function testIteration() {
        $object = $this->_getTestObject();

        // Set the array pointer to the middle of the array
        $count = 0;
        foreach ($object as $k => $v) {
            $count++;
            $this->assertEquals('one', $k);
            $this->assertEquals('foo', $v);
            break;
        }
        $this->assertEquals(1, $count);

        // foreach should rewind the array pointer
        $count = 0;
        foreach ($object as $k => $v) {
            $count++;
            switch ($k) {
                case 'one': $this->assertEquals('foo', $v); break;
                case 'two': $this->assertEquals('bar', $v); break;
                default: $this->assertTrue(false); break;
            }
        }
        $this->assertEquals(count($object), $count);
    }
}
