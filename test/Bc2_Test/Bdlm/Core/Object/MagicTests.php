<?php

namespace Bc2_Test\Bdlm\Core\Object;

class MagicTest extends \PHPUnit_Framework_TestCase {
	protected $object = null;
	protected function _getTestObject() {
		return new \Bdlm\Core\Object([
			'one' => 'foo',
			'two' => 'bar',
		]);
	}

//////////////////////////////////////////////////////////////////////////////

	public function testGet() {
		$object = $this->_getTestObject();
		$this->assertEquals('foo', $object->one);
		$this->assertEquals('bar', $object->two);

		$object->set('two', 'baz');
		$this->assertEquals('baz', $object->two);

		$object->set('three', 'oontz');
		$this->assertEquals('oontz', $object->three);
	}

	public function testIsset() {
		$object = $this->_getTestObject();
		$this->assertTrue(isset($object->two));
		$this->assertFalse(isset($object->three));
	}

	public function testSet() {
		$object = $this->_getTestObject();
		$object->test_var = 'test_val';
		$this->assertEquals('test_val', $object->get('test_var'));
	}

	public function testToString() {
		$object = $this->_getTestObject();
		$test_array = [
			'one' => 'foo',
			'two' => 'bar',
		];

		$this->assertEquals($test_array, $object->getData());
		$object->set('three', 'baz');
		$test_array['three'] = 'baz';
		$this->assertEquals(json_encode($test_array, JSON_PRETTY_PRINT), "$object");
	}

	public function testUnset() {
		$object = $this->_getTestObject();

		$this->assertTrue($object->has('one'));
		$this->assertEquals(2, count($object));
		unset($object->one);
		$this->assertFalse($object->has('one'));
		$this->assertEquals(1, count($object));
	}
}
