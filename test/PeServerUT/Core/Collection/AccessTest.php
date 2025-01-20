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


	public function test_getString()
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


	public function test_getArray()
	{
		$actual = Access::getArray([['array']], 0);
		$this->assertSame(['array'], $actual);
	}

	public function test_getArray_key_throw()
	{
		$this->expectException(AccessKeyNotFoundException::class);
		Access::getArray(['array'], 1);
		$this->fail();
	}

	public function test_getArray_type_throw()
	{
		$this->expectException(AccessValueTypeException::class);
		Access::getArray([10], 0);
		$this->fail();
	}


	public function test_getObject()
	{
		$actual1 = Access::getObject([new AccessTestA1()], 0);
		$this->assertInstanceOf(AccessTestA1::class, $actual1);

		$actual2 = Access::getObject([new AccessTestA2()], 0);
		$this->assertInstanceOf(AccessTestA2::class, $actual2);

		$actual1_2 = Access::getObject([new AccessTestA2()], 0);
		$this->assertInstanceOf(AccessTestA1::class, $actual1_2);

		$actual2_2 = Access::getObject([new AccessTestA1()], 0);
		$this->assertNotInstanceOf(AccessTestA2::class, $actual2_2);

		$actual1_3 = Access::getObject([new AccessTestA2()], 0, AccessTestA1::class);
		$this->assertInstanceOf(AccessTestA1::class, $actual1_3);
		$this->assertInstanceOf(AccessTestA2::class, $actual1_3);

		$actual2_3 = Access::getObject([new AccessTestA2()], 0, AccessTestA2::class);
		$this->assertInstanceOf(AccessTestA1::class, $actual2_3);
		$this->assertInstanceOf(AccessTestA2::class, $actual2_3);

		$actual1_4 = Access::getObject([new AccessTestA1()], 0, AccessTestA1::class);
		$this->assertInstanceOf(AccessTestA1::class, $actual1_4);
		$this->assertNotInstanceOf(AccessTestA2::class, $actual1_4);

		$this->expectException(AccessValueTypeException::class);
		Access::getObject([new AccessTestB()], 0, AccessTestA1::class);
		$this->fail();
	}

	public function test_getObject_key_throw()
	{
		$this->expectException(AccessKeyNotFoundException::class);
		Access::getObject(['array'], 1);
		$this->fail();
	}

	public function test_getObject_type_throw()
	{
		$this->expectException(AccessValueTypeException::class);
		Access::getObject([10], 0);
		$this->fail();
	}

	#endregion
}

class AccessTestA1
{
}

class AccessTestA2 extends AccessTestA1
{
}

class AccessTestB
{
}
