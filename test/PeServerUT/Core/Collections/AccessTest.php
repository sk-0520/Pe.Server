<?php

declare(strict_types=1);

namespace PeServerUT\Core\Collections;

use ArrayAccess;
use PeServer\Core\Collections\Access;
use PeServer\Core\Throws\AccessInvalidLogicalTypeException;
use PeServer\Core\Throws\AccessKeyNotFoundException;
use PeServer\Core\Throws\AccessValueTypeException;
use PeServer\Core\Throws\NotImplementedException;
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

	#[TestWith([10, 0])]
	#[TestWith(['a', 1])]
	#[TestWith(['value', 'key'])]
	public function test_getValue_arrayAccess(mixed $expected, string|int $key)
	{
		$array = new LocalArrayAccess();
		$actual = Access::getValue($array, $key);
		$this->assertSame($expected, $actual);
	}

	#[TestWith([2])]
	#[TestWith(['none'])]
	public function test_getValue_arrayAccess_throw(string|int $key)
	{
		$array = new LocalArrayAccess();
		$this->expectException(AccessKeyNotFoundException::class);
		Access::getValue($array, 'none');
		$this->fail();
	}

	public function test_getBool()
	{
		$actual1 = Access::getBool([true], 0);
		$this->assertTrue($actual1);

		$actual2 = Access::getBool([false], 0);
		$this->assertFalse($actual2);
	}

	public function test_getBool_key_throw()
	{
		$this->expectException(AccessKeyNotFoundException::class);
		Access::getBool([true], 1);
		$this->fail();
	}

	public function test_getBool_type_throw()
	{
		$this->expectException(AccessValueTypeException::class);
		Access::getBool(['true'], 0);
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

	public function test_getUInteger()
	{
		$actual = Access::getUInteger([10], 0);
		$this->assertSame(10, $actual);
	}

	public function test_getUInteger_key_throw()
	{
		$this->expectException(AccessKeyNotFoundException::class);
		Access::getUInteger([10], 1);
		$this->fail();
	}

	public function test_getUInteger_type_throw()
	{
		$this->expectException(AccessValueTypeException::class);
		Access::getUInteger(['10'], 0);
		$this->fail();
	}

	public function test_getUInteger_type_throw_negative()
	{
		$this->expectException(AccessInvalidLogicalTypeException::class);
		Access::getUInteger([-1], 0);
		$this->fail();
	}

	public function test_getPositiveInteger()
	{
		$actual = Access::getPositiveInteger([10], 0);
		$this->assertSame(10, $actual);
	}

	public function test_getPositiveInteger_key_throw()
	{
		$this->expectException(AccessKeyNotFoundException::class);
		Access::getPositiveInteger([10], 1);
		$this->fail();
	}

	public function test_getPositiveInteger_type_throw()
	{
		$this->expectException(AccessValueTypeException::class);
		Access::getPositiveInteger(['10'], 0);
		$this->fail();
	}

	public function test_getPositiveInteger_type_throw_negative()
	{
		$this->expectException(AccessInvalidLogicalTypeException::class);
		Access::getPositiveInteger([0], 0);
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

	#[TestWith([" ", " ", false])]
	#[TestWith([" a ", " a ", false])]
	#[TestWith(["a", " a ", true])]
	public function test_getNonEmptyString(string $expected, string $input, bool $trim)
	{
		$actual = Access::getNonEmptyString([$input], 0, $trim);
		$this->assertSame($expected, $actual);
	}

	public function test_getNonEmptyString_default()
	{
		$actual = Access::getNonEmptyString([" a b c "], 0);
		$this->assertSame("a b c", $actual);
	}

	public function test_getNonEmptyString_key_throw()
	{
		$this->expectException(AccessKeyNotFoundException::class);
		Access::getNonEmptyString([' str '], 1);
		$this->fail();
	}

	public function test_getNonEmptyString_type_throw_empty()
	{
		$this->expectException(AccessInvalidLogicalTypeException::class);
		Access::getNonEmptyString([" "], 0);
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

	public function test_getArrayOfBool()
	{
		$actual = Access::getArrayOfBool([[true, false, true]], 0);
		$this->assertSame([true, false, true], $actual);

		$this->isEmpty(Access::getArrayOfBool([[]], 0));
	}

	#[TestWith([[[true, 10]], 0])]
	#[TestWith([[[10, true]], 0])]
	#[TestWith([[[true, 'str']], 0])]
	#[TestWith([[[true, null]], 0])]
	public function test_getArrayOfBool_type_throw(array $array, string|int $key)
	{
		$this->expectException(AccessValueTypeException::class);
		Access::getArrayOfBool($array, $key);
		$this->fail();
	}

	public function test_getArrayOfInteger()
	{
		$actual = Access::getArrayOfInteger([[1, 2, 3]], 0);
		$this->assertSame([1, 2, 3], $actual);

		$this->isEmpty(Access::getArrayOfInteger([[]], 0));
	}

	#[TestWith([[[3.14, 10]], 0])]
	#[TestWith([[[10, 3.14]], 0])]
	#[TestWith([[[10, true]], 0])]
	#[TestWith([[[10, 'str']], 0])]
	#[TestWith([[[10, null]], 0])]
	public function test_getArrayOfInteger_type_throw(array $array, string|int $key)
	{
		$this->expectException(AccessValueTypeException::class);
		Access::getArrayOfInteger($array, $key);
		$this->fail();
	}

	public function test_getArrayOfUInteger()
	{
		$actual = Access::getArrayOfUInteger([[0, 1, 2, 3]], 0);
		$this->assertSame([0, 1, 2, 3], $actual);

		$this->isEmpty(Access::getArrayOfUInteger([[]], 0));
	}

	#[TestWith([AccessInvalidLogicalTypeException::class, [[-1]], 0])]
	#[TestWith([AccessValueTypeException::class, [[3.14, 10]], 0])]
	#[TestWith([AccessValueTypeException::class, [[10, 3.14]], 0])]
	#[TestWith([AccessValueTypeException::class, [[10, true]], 0])]
	#[TestWith([AccessValueTypeException::class, [[10, 'str']], 0])]
	#[TestWith([AccessValueTypeException::class, [[10, null]], 0])]
	public function test_getArrayOfUInteger_type_throw(string $expectedException, array $array, string|int $key)
	{
		$this->expectException($expectedException);
		Access::getArrayOfUInteger($array, $key);
		$this->fail();
	}

	public function test_getArrayOfPositiveInteger()
	{
		$actual = Access::getArrayOfPositiveInteger([[1, 2, 3]], 0);
		$this->assertSame([1, 2, 3], $actual);

		$this->isEmpty(Access::getArrayOfPositiveInteger([[]], 0));
	}

	#[TestWith([AccessInvalidLogicalTypeException::class, [[0]], 0])]
	#[TestWith([AccessValueTypeException::class, [[3.14, 10]], 0])]
	#[TestWith([AccessValueTypeException::class, [[10, 3.14]], 0])]
	#[TestWith([AccessValueTypeException::class, [[10, true]], 0])]
	#[TestWith([AccessValueTypeException::class, [[10, 'str']], 0])]
	#[TestWith([AccessValueTypeException::class, [[10, null]], 0])]
	public function test_getArrayOfPositiveInteger_type_throw(string $expectedException, array $array, string|int $key)
	{
		$this->expectException($expectedException);
		Access::getArrayOfPositiveInteger($array, $key);
		$this->fail();
	}

	public function test_getArrayOfFloat()
	{
		$actual = Access::getArrayOfFloat([[1.0, 2.0, 3.0]], 0);
		$this->assertSame([1.0, 2.0, 3.0], $actual);

		$this->isEmpty(Access::getArrayOfFloat([[]], 0));
	}

	#[TestWith([[[3.14, 10]], 0])]
	#[TestWith([[[10, 3.14]], 0])]
	#[TestWith([[[3.14, true]], 0])]
	#[TestWith([[[3.14, 'str']], 0])]
	#[TestWith([[[3.14, null]], 0])]
	public function test_getArrayOfFloat_type_throw(array $array, string|int $key)
	{
		$this->expectException(AccessValueTypeException::class);
		Access::getArrayOfFloat($array, $key);
		$this->fail();
	}

	public function test_getArrayOfString()
	{
		$actual = Access::getArrayOfString([['a', 'b', 'c']], 0);
		$this->assertSame(['a', 'b', 'c'], $actual);

		$this->isEmpty(Access::getArrayOfString([[]], 0));
	}

	#[TestWith([[['str', 10]], 0])]
	#[TestWith([[[10, 'str']], 0])]
	#[TestWith([[['str', true]], 0])]
	#[TestWith([[['str', 3.14]], 0])]
	#[TestWith([[['str', null]], 0])]
	public function test_getArrayOfString_type_throw(array $array, string|int $key)
	{
		$this->expectException(AccessValueTypeException::class);
		Access::getArrayOfString($array, $key);
		$this->fail();
	}

	public function test_getArrayOfNonEmptyString()
	{
		$actual = Access::getArrayOfNonEmptyString([[" a", "b ", " c "]], 0);
		$this->assertSame(["a", "b", "c"], $actual);

		$this->isEmpty(Access::getArrayOfNonEmptyString([[]], 0));
	}

	#[TestWith([AccessInvalidLogicalTypeException::class, [[" "]], 0])]
	#[TestWith([AccessValueTypeException::class, [[3.14, 10]], 0])]
	#[TestWith([AccessValueTypeException::class, [[10, 3.14]], 0])]
	#[TestWith([AccessValueTypeException::class, [[10, true]], 0])]
	#[TestWith([AccessValueTypeException::class, [[10, 'str']], 0])]
	#[TestWith([AccessValueTypeException::class, [[10, null]], 0])]
	public function test_getArrayOfNonEmptyString_type_throw(string $expectedException, array $array, string|int $key)
	{
		$this->expectException($expectedException);
		Access::getArrayOfNonEmptyString($array, $key);
		$this->fail();
	}

	public function test_getArrayOfObject()
	{
		$actual = Access::getArrayOfObject([[new AccessTestA1(), new AccessTestA1(), new AccessTestA1()]], 0);
		$this->assertEqualsWithInfo('オブジェクト比較なので緩い比較', [new AccessTestA1(), new AccessTestA1(), new AccessTestA1()], $actual);

		$this->isEmpty(Access::getArrayOfObject([[]], 0));
	}

	public function test_getArrayOfObject_type_throw()
	{
		$this->expectException(AccessValueTypeException::class);
		Access::getArrayOfObject([[new AccessTestA1(), null]], 0);
		$this->fail();
	}

	#endregion
}

class LocalArrayAccess implements ArrayAccess
{
	private array $array = [
		10,
		'a',
		'key' => 'value',
		'none' => null
	];

	#region ArrayAccess

	public function offsetExists(mixed $offset): bool
	{
		return isset($this->array[$offset]);
	}

	public function offsetGet(mixed $offset): mixed
	{
		return $this->array[$offset];
	}

	public function offsetSet(mixed $offset, mixed $value): void
	{
		throw new NotImplementedException();
	}

	public function offsetUnset(mixed $offset): void
	{
		throw new NotImplementedException();
	}

	#endregion
}

class AccessTestA1
{
	//NOP
}

class AccessTestA2 extends AccessTestA1
{
	//NOP
}

class AccessTestB
{
	//NOP
}
