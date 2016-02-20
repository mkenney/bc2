<?php

namespace Test\Bdlm\Core\Object;


class TestObjectWithConstants extends \Bdlm\Core\Object {
    const CONSTANT1 = true;
    const CONSTANT2 = 'true';
}

class BaseTests extends \PHPUnit_Framework_TestCase {
//    protected $object = null;
//    protected function _getTestObject() {
//        return new \Bdlm\Core\Object([
//            'one' => 'foo',
//            'two' => 'bar',
//        ]);
//    }

//////////////////////////////////////////////////////////////////////////////

    public function testAddTo() {
        $object = new \Bdlm\Core\Object();
        $test_data = [
            'key' => ['val1']
        ];

        // Should create key if it doesn't exist
        $object->addTo('key', 'val1');
        $this->assertEquals($test_data, $object->getData());

        $test_data['key'][] = 'val2';
        $object->addTo('key', 'val2');
        $this->assertEquals($test_data, $object->getData());
    }

    public function testGet() {
        $object = new \Bdlm\Core\Object([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        $this->assertEquals('value1', $object->get('key1'));
        $this->assertEquals('value2', $object->get('key2'));
    }

    public function testGetConstants() {
        $constants = (new TestObjectWithConstants())->getConstants();
        $this->assertEquals(true, $constants['CONSTANT1']);
        $this->assertEquals('true', $constants['CONSTANT2']);
    }

    public function testGetData() {
        $object = new \Bdlm\Core\Object([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);
        $this->assertEquals([
            'key1' => 'value1',
            'key2' => 'value2',
        ], $object->getData());
    }

    public function testHas() {
        $object = new \Bdlm\Core\Object();
        $this->assertFalse($object->has('key'));

        $object = new \Bdlm\Core\Object(['key' => null]);
        $this->assertTrue($object->has('key'));

        $object = new \Bdlm\Core\Object(['key' => false]);
        $this->assertTrue($object->has('key'));

        $object = new \Bdlm\Core\Object(['key' => '']);
        $this->assertTrue($object->has('key'));

        $object = new \Bdlm\Core\Object(['key' => '0']);
        $this->assertTrue($object->has('key'));

        $object = new \Bdlm\Core\Object(['key' => 0]);
        $this->assertTrue($object->has('key'));
    }

    public function testIsEmpty() {
        $object = new \Bdlm\Core\Object();
        $this->assertTrue($object->isEmpty());

        $object = new \Bdlm\Core\Object(['key' => 'val']);
        $this->assertFalse($object->isEmpty());

        $object = new \Bdlm\Core\Object(['key' => null]);
        $this->assertTrue($object->isEmpty('key'));

        $object = new \Bdlm\Core\Object(['key' => '']);
        $this->assertTrue($object->isEmpty('key'));

        $object = new \Bdlm\Core\Object(['key' => false]);
        $this->assertFalse($object->isEmpty('key'));

        $object = new \Bdlm\Core\Object(['key' => true]);
        $this->assertFalse($object->isEmpty('key'));

        $object = new \Bdlm\Core\Object(['key' => 0]);
        $this->assertFalse($object->isEmpty('key'));

        $object = new \Bdlm\Core\Object(['key' => '0']);
        $this->assertFalse($object->isEmpty('key'));
    }

    public function testIsStatic() {
        // Default false
        $object = new \Bdlm\Core\Object();
        $this->assertFalse($object->isStatic());

        $this->assertTrue($object->isStatic(true));
        $this->assertTrue($object->isStatic());

        $this->assertFalse($object->isStatic(false));
        $this->assertFalse($object->isStatic());
    }

    public function testGetSetMax() {
        $object = new \Bdlm\Core\Object();
        $this->assertTrue(is_null($object->getMax()));

        $object->setMax(null);
        $this->assertTrue(is_null($object->getMax()));

        $object->setMax(10);
        $this->assertEquals(10, $object->getMax());

        $object->setMax('20');
        $this->assertEquals(20, $object->getMax());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid max value 'a'. Must be numeric and greater than the current min value.
     */
    public function testGetSetMaxExceptionInvalidData1() {
        $object = new \Bdlm\Core\Object();
        $object->setMax('a');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid max value ''. Must be numeric and greater than the current min value.
     */
    public function testGetSetMaxExceptionInvalidData2() {
        $object = new \Bdlm\Core\Object();
        $object->setMax('');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid max value ''. Must be numeric and greater than the current min value.
     */
    public function testGetSetMaxExceptionInvalidData3() {
        $object = new \Bdlm\Core\Object();
        $object->setMax(false);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid max value '1'. Must be numeric and greater than the current min value.
     */
    public function testGetSetMaxExceptionInvalidData4() {
        $object = new \Bdlm\Core\Object();
        $object->setMax(true);
    }

    public function testGetSetMin() {
        $object = new \Bdlm\Core\Object();
        $this->assertTrue(is_null($object->getMin()));

        $object->setMin(null);
        $this->assertTrue(is_null($object->getMin()));

        $object->setMin(10);
        $this->assertEquals(10, $object->getMin());

        $object->setMin('20');
        $this->assertEquals(20, $object->getMin());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid min value 'a'. Must be numeric and less than the current max value.
     */
    public function testGetSetMinExceptionInvalidData1() {
        $object = new \Bdlm\Core\Object();
        $object->setMin('a');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid min value ''. Must be numeric and less than the current max value.
     */
    public function testGetSetMinExceptionInvalidData2() {
        $object = new \Bdlm\Core\Object();
        $object->setMin('');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid min value ''. Must be numeric and less than the current max value.
     */
    public function testGetSetMinExceptionInvalidData3() {
        $object = new \Bdlm\Core\Object();
        $object->setMin(false);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid min value '1'. Must be numeric and less than the current max value.
     */
    public function testGetSetMinExceptionInvalidData4() {
        $object = new \Bdlm\Core\Object();
        $object->setMin(true);
    }

    public function testGetSetMode() {
        $object = new \Bdlm\Core\Object();
        $this->assertEquals('list', $object->getMode());

        $object->setMode('fixed');
        $this->assertEquals('fixed', $object->getMode());

        $object->setMode('singleton');
        $this->assertEquals('singleton', $object->getMode());

        $object->setMode('list');
        $this->assertEquals('list', $object->getMode());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid mode 'test'. Valid values are 'list', 'fixed' and 'singleton'
     */
    public function testSetModeException1() {
        $object = new \Bdlm\Core\Object();
        $object->setMode('test');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid mode '1'. Valid values are 'list', 'fixed' and 'singleton'
     */
    public function testSetModeException2() {
        $object = new \Bdlm\Core\Object();
        $object->setMode(1);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid mode '1'. Valid values are 'list', 'fixed' and 'singleton'
     */
    public function testSetModeException3() {
        $object = new \Bdlm\Core\Object();
        $object->setMode(true);
    }

    public function testGetSetName() {
        $object = new \Bdlm\Core\Object();
        $this->assertTrue(is_null($object->getName()));

        $object->setName('test name');
        $this->assertEquals('test name', $object->getName());

        $object->setName(true);
        $this->assertEquals('1', $object->getName());

        $object->setName(new \Bdlm\Core\Object());
        $this->assertEquals('[]', $object->getName());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid name ''
     */
    public function testSetNameException1() {
        $object = new \Bdlm\Core\Object();
        $object->setName('');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid name ''
     */
    public function testSetNameException2() {
        $object = new \Bdlm\Core\Object();
        $object->setName(null);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid name ''
     */
    public function testSetNameException3() {
        $object = new \Bdlm\Core\Object();
        $object->setName(false);
    }

    public function testReset() {
        $object = new \Bdlm\Core\Object(['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $object->getData());
        $object->reset();
        $this->assertEquals([], $object->getData());
    }

    public function testSetAnyType() {
        $test_resource = fopen(__FILE__, 'r');
        $test_data = [
            'array'    => [1,2,3],
            'bool'     => true,
            'boolean'  => false,
            'date'     => new \DateTime(),
            'double'   => 1.1,
            'file'     => '/etc/hosts',
            'float'    => 1.1,
            'int'      => 1,
            'integer'  => 1,
            'long'     => 1,
            'mbstring' => '',
            'mixed'    => 1,
            'mixed2'   => '1',
            'object'   => new \Bdlm\Core\Object(),
            'real'     => 1.1,
            'resource' => $test_resource,
            'scalar1'  => '1',
            'scalar1'  => 1,
            'scalar1'  => 1.1,
            'scalar1'  => true,
            'string'   => 'abcd',
        ];

        $object = new \Bdlm\Core\Object();
        $object->set('array',    [1,2,3]);
        $object->set('bool',     true);
        $object->set('boolean',  false);
        $object->set('date',     new \DateTime());
        $object->set('double',   1.1);
        $object->set('file',     '/etc/hosts');
        $object->set('float',    1.1);
        $object->set('int',      1);
        $object->set('integer',  1);
        $object->set('long',     1);
        $object->set('mbstring', '');
        $object->set('mixed',    1);
        $object->set('mixed2',   '1');
        $object->set('object',   new \Bdlm\Core\Object());
        $object->set('real',     1.1);
        $object->set('resource', $test_resource);
        $object->set('scalar1',  '1');
        $object->set('scalar1',  1);
        $object->set('scalar1',  1.1);
        $object->set('scalar1',  true);
        $object->set('string',   'abcd');

        $this->assertEquals($test_data, $object->getData());
    }

    public function testSetSpecifiedType() {
        $self = $this;
        $test_function = function ($type, $data) use ($self) {
            $object = new \Bdlm\Core\Object();
            $object->setType($type);
            $object->set("{$type}", $data);
            $self->assertEquals($data, $object->get("{$type}"));
        };

        $test_resource = fopen(__FILE__, 'r');
        $test_data = [
            'array'    => [1,2,3],
            'bool'     => true,
            'boolean'  => false,
            'date'     => new \DateTime(),
            'double'   => 1.1,
            'file'     => '/etc/hosts',
            'float'    => 1.1,
            'int'      => 1,
            'integer'  => 1,
            'long'     => 1,
            'mbstring' => '',
            'mixed'    => 1,
            'object'   => new \Bdlm\Core\Object(),
            'real'     => 1.1,
            'resource' => $test_resource,
            'scalar'  => '1',
            'string'   => 'abcd',
        ];

        foreach ($test_data as $type => $data) {
            $test_function($type, $data);

        }
    }

    public function testSetSpecifiedTypeExceptions() {
        $self = $this;
        $test_function = function ($type, $data) use ($self) {
            $object = new \Bdlm\Core\Object();
            $object->setType('resource');
            try {
                $object->set("{$type}", $data);
                throw new \Exception("Test failed");
            } catch (\DomainException $e) {
                $data_type = gettype($data);
                $self->assertEquals("Invalid data type '{$data_type}', expecting 'resource'.", $e->getMessage());
            }
        };

        $test_data = [
            'array'    => [1,2,3],
            'bool'     => true,
            'boolean'  => false,
            'date'     => new \DateTime(),
            'double'   => 1.1,
            'file'     => '/etc/hosts',
            'float'    => 1.1,
            'int'      => 1,
            'integer'  => 1,
            'long'     => 1,
            'mbstring' => '',
            'mixed'    => 1,
            'object'   => new \Bdlm\Core\Object(),
            'real'     => 1.1,
            'scalar'  => '1',
            'string'   => 'abcd',
        ];
        foreach ($test_data as $type => $data) {
            $test_function($type, $data);

        }

        $test_data = [
            'array'    => [1,2,3],
            'bool'     => true,
            'float'    => 1.1,
            'int'      => 1,
            'object'   => new \Bdlm\Core\Object(),
            'scalar'  => '1',
            'string'   => 'abcd',
        ];
        foreach ($test_data as $type => $data) {
            $object = new \Bdlm\Core\Object();
            $object->setType($type);
            try {
                $object->set("{$type}", fopen(__FILE__, 'r'));
                throw new \Exception("Test failed");
            } catch (\DomainException $e) {
                $data_type = gettype($data);
                $self->assertEquals("Invalid data type 'resource', expecting '{$type}'.", $e->getMessage());
            }
        }
    }

    /**
     * @expectedException \DomainException
     * @expectedExceptionMessage Static objects cannot be modified.
     */
    public function testSetStaticException() {
        $object = new \Bdlm\Core\Object();
        $object->isStatic(true);
        $object->set('key', 'val');
    }

    public function testSetSingleton() {
        $object = new \Bdlm\Core\Object();
        $object->setMode('singleton');
        $object->set('key1', 'val1');
        $object->set('key2', 'val2');
        $this->assertEquals(['key2' => 'val2'], $object->getData());
    }

    public function testSetFixed() {
        $object = new \Bdlm\Core\Object([
            'key1' => null,
            'key2' => null,
        ]);
        $object->setMode('fixed');

        $object->set('key1', 'val1');
        $object->set('key2', 'val2');
        $this->assertEquals(['key1' => 'val1', 'key2' => 'val2'], $object->getData());
    }

    /**
     * @expectedException \DomainException
     * @expectedExceptionMessage This is a fixed list and the specified key 'key3' does not exist.
     */
    public function testSetFixedException() {
        $object = new \Bdlm\Core\Object([
            'key1' => null,
            'key2' => null,
        ]);
        $object->setMode('fixed');

        $object->set('key3', 'val3');
    }
}
