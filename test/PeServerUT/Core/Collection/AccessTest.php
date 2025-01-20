<?php

declare(strict_types=1);

namespace PeServerUT\Core\Collection;

use PeServer\Core\Collection\Access;
use PeServer\Core\Throws\AccessKeyNotFoundException;
use PeServer\Core\Throws\AccessValueTypeException;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\TestWith;

class AccessTest extends TestClass
{
	#region function

	#[TestWith([10, [10, 'a', 'key' => 'value'], 0])]
	#[TestWith(['a', [10, 'a', 'key' => 'value'], 1])]
	#[TestWith(['value', [10, 'a', 'key' => 'value'], 'key'])]
	#[TestWith([null, [10, 'a', 'key' => 'value', 'none' => null], 'none'])]
	public function test_getValue(mixed $expected, array $array, string|int $key)
	{
		$actual = Access::getValue($array, $key);
		$this->assertSame($expected, $actual);
	}

	#[TestWith([[], 0])]
	#[TestWith([[], 'key'])]
	public function test_getValue_throw(array $array, string|int $key): void
	{
		$this->expectException(AccessKeyNotFoundException::class);
		Access::getValue($array, $key);
		$this->fail();
	}

	public function test_getInteger()
	{
		$actual = Access::getInteger([10], 0);
		$this->assertSame(10, $actual);
	}

	public function test_getInteger_key_throw()
	{
		$this->expectException(AccessKeyNotFoundException::class);
		Access::getInteger([10], 1);
		$this->fail();
	}

	public function test_getInteger_type_throw()
	{
		$this->expectException(AccessValueTypeException::class);
		Access::getInteger(['10'], 0);
		$this->fail();
	}

	public function test_getFloat()
	{
		$actual = Access::getFloat([10.0], 0);
		$this->assertSame(10.0, $actual);
	}

	public function test_getFloat_key_throw()
	{
		$this->expectException(AccessKeyNotFoundException::class);
		Access::getFloat([10], 1);
		$this->fail();
	}

	public function test_getFloat_type_throw()
	{
		$this->expectException(AccessValueTypeException::class);
		Access::getFloat([10], 0);
		$this->fail();
	}

	public function test_geString()
	{
		$actual = Access::getString(['str'], 0);
		$this->assertSame('str', $actual);
	}

	public function test_getString_key_throw()
	{
		$this->expectException(AccessKeyNotFoundException::class);
		Access::getString(['str'], 1);
		$this->fail();
	}

	public function test_getString_type_throw()
	{
		$this->expectException(AccessValueTypeException::class);
		Access::getString([10], 0);
		$this->fail();
	}

	#endregion
}
