<?php

declare(strict_types=1);

namespace PeServerUT\Core\Collection;

use PeServer\Core\Collection\Access;
use PeServer\Core\Throws\AccessKeyNotFoundException;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\TestWith;

class AccessTest extends TestClass
{
	#region function

	#[TestWith([10, [10, 'a', 'key' => 'value'], 0])]
	#[TestWith(['a', [10, 'a', 'key' => 'value'], 1])]
	#[TestWith(['value', [10, 'a', 'key' => 'value'], 'key'])]
	public function test_getRawValue(mixed $expected, array $array, string|int $key)
	{
		$actual = Access::getRawValue($array, $key);
		$this->assertSame($expected, $actual);
	}

	public function test_getRawValue_throw(): void
	{
		$this->expectException(AccessKeyNotFoundException::class);
		Access::getRawValue([], 'key');
		$this->fail();
	}



	#endregion
}
