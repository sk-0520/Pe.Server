<?php

declare(strict_types=1);

namespace PeServerTest\Core\Serialization;

use PeServer\Core\Serialization\JsonSerializer;
use PeServerTest\TestClass;

class JsonSerializerTest extends TestClass
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
		$serializer = new JsonSerializer();
		$data = $serializer->save($excepted);
		$actual = $serializer->load($data);
		$this->assertSame($excepted, $actual);
	}
}
