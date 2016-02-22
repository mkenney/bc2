<?php

namespace Bc2_Test\Bdlm\Core;

class ObjectTest extends \PHPUnit_Framework_TestCase {

	public function test() {
		$object = new \Bdlm\Core\Object([
			'key1' => 'val1',
		]);
		$this->assertEquals(['key1' => 'val1'], iterator_to_array($object));
	}

}
