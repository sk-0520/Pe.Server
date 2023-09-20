<?php

declare(strict_types=1);

namespace PeServerUT\Core\Serialization;

use PeServer\Core\Serialization\BuiltinSerializer;
use PeServerUT\TestClass;

class BuiltinSerializerTest extends TestClass
{
	public function test_restore()
	{
		$excepted = [
			[1, 2, 3],
			'key' => 'value',
			'array' => [
				10, 20, 30
			]
		];
		$serializer = new BuiltinSerializer();
		$data = $serializer->save($excepted);
		$actual = $serializer->load($data);
		$this->assertSame($excepted, $actual);
	}
}
