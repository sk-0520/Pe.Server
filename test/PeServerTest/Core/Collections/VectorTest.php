<?php

declare(strict_types=1);

namespace PeServerTest\Core\Collections;

use PeServer\Core\Collections\Vector;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\IndexOutOfRangeException;
use PeServer\Core\TypeUtility;
use PeServerTest\TestClass;
use TypeError;

interface TestI {
	function get(): string;
}
class TestA implements TestI {
	public function __construct(
		private string $var
	) {
	}
	public function get(): string
	{
		return $this->var;
	}
}
class TestB extends TestA {
	public function __construct(
		string $var
	) {
		parent::__construct($var);
	}
}
class VectorTest extends TestClass
{
	function test_create()
	{
		$list1 = new Vector(TypeUtility::TYPE_INTEGER, null, true);
		$this->assertSame(0, $list1->count());

		$list2 = Vector::create([1, 2, 3]);
		$this->assertSame(3, $list2->count());
	}

	function test_create_no_throw()
	{
		$list1 = Vector::create([5], false);
		$this->assertSame(1, $list1->count());
		$this->assertSame(5, $list1[0]);

		$list2 = Vector::create([1 => 10]);
		$this->assertSame(1, $list2->count());
		$this->assertSame(10, $list2[0]);
	}

	function test_create_throw()
	{
		$this->expectException(ArgumentException::class);
		Vector::create([1 => 10], false);
		$this->fail();
	}

	function test_add()
	{
		/** @var Vector<int> */
		$list = new Vector(TypeUtility::TYPE_INTEGER, null, true);

		$list->add(1);
		$this->assertSame(1, $list->count());
		$this->assertSame(1, $list[0]);

		$list->add(2);
		$this->assertSame(2, $list->count());
		$this->assertSame(2, $list[1]);
	}

	function test_add_type()
	{
		$list1 = Vector::create([1], false);
		$list1->add(2);
		try {
			$list1->add('3');
			$this->fail();
		} catch(TypeError $err) {
			$this->success();
		}

		$list2 = Vector::create([new TestA('A')], false);
		$list2->add(new TestB('B'));

		try {
			$list2->add(new TestB('B'));
		} catch(TypeError $err) {
			$this->success();
		}

		try {
			$list2->add(new class('I') implements TestI {
				public function get(): string
				{
					return 'I';
				}
			});
		} catch(TypeError $err) {
			$this->success();
		}
	}

	function test_addRange()
	{
		/** @var Vector<int> */
		$list = Vector::create([1, 2, 3]);

		$list->addRange([4, 5, 6]);
		$this->assertSame(6, $list->count());
		$this->assertSame(1, $list[0]);
		$this->assertSame(2, $list[1]);
		$this->assertSame(3, $list[2]);
		$this->assertSame(4, $list[3]);
		$this->assertSame(5, $list[4]);
		$this->assertSame(6, $list[5]);
	}

	function test_addRange_no_throw()
	{
		$list = Vector::create([1, 2, 3]);

		$list->addRange([1 => 10]);
		$this->assertSame(4, $list->count());
		$this->assertSame(10, $list[3]);
	}

	function test_addRange_throw()
	{
		$list = Vector::create([1, 2, 3]);

		$this->expectException(ArgumentException::class);
		$list->addRange([1 => 10], false);
		$this->fail();
	}

	function test_offsetExists()
	{
		$list = Vector::create([1, 2, 3]);
		$this->assertFalse(isset($list[-1]));
		$this->assertTrue(isset($list[0]));
		$this->assertTrue(isset($list[1]));
		$this->assertTrue(isset($list[2]));
		$this->assertFalse(isset($list[3]));
	}

	function test_offsetGet()
	{
		$list = Vector::create([1, 2, 3]);
		$this->assertSame(1, $list[0]);
		$this->assertSame(2, $list[1]);
		$this->assertSame(3, $list[2]);
	}

	function test_offsetGet_null_throw()
	{
		$list = Vector::create([1, 2, 3]);
		$this->expectException(TypeError::class);
		$list[null];
	}

	function test_offsetGet_not_int_throw()
	{
		$list = Vector::create([1, 2, 3]);
		$this->expectException(TypeError::class);
		$list['str'];
	}

	public static function provider_offsetGet_throw()
	{
		return [
			[-1],
			[3],
		];
	}

	/** @dataProvider provider_offsetGet_throw */
	function test_offsetGet_throw(int $index)
	{
		$list = Vector::create([1, 2, 3]);
		$this->expectException(IndexOutOfRangeException::class);
		$list[$index];
		$this->fail();
	}

	function test_offsetSet()
	{
		$list = Vector::create([1, 2, 3]);
		$this->assertSame(1, $list[0]);
		$this->assertSame(2, $list[1]);
		$this->assertSame(3, $list[2]);

		$list[0] = 10;
		$list[1] = $list[1] * 2;
		$list[2] += 3;

		$this->assertSame(10, $list[0]);
		$this->assertSame(4, $list[1]);
		$this->assertSame(6, $list[2]);

		$list[] = 100;
		$this->assertSame(4, $list->count());
		$this->assertSame(100, $list[3]);
	}

	public static function provider_offsetSet_throw()
	{
		return [
			[-1],
			[3],
		];
	}

	/** @dataProvider provider_offsetSet_throw */
	function test_offsetSet_throw(int $index)
	{
		$list = Vector::create([1, 2, 3]);
		$this->expectException(IndexOutOfRangeException::class);
		$list[$index] = 123;
		$this->fail();
	}

	function test_offsetUnset()
	{
		$list = Vector::create([1, 2, 3]);

		unset($list[2]);
		$this->assertSame(2, $list->count());

		unset($list[1]);
		$this->assertSame(1, $list->count());

		unset($list[0]);
		$this->assertSame(0, $list->count());
	}

	function test_offsetUnset_index_throw()
	{
		$list = Vector::create([1, 2, 3]);
		$this->expectException(IndexOutOfRangeException::class);
		unset($list[1]);
		$this->fail();
	}

	function test_offsetUnset_out_throw()
	{
		$list = Vector::create([1, 2, 3]);
		$this->expectException(IndexOutOfRangeException::class);
		unset($list[3]);
		$this->fail();
	}

	function test_getArray()
	{
		$list = Vector::create([1, 2, 3]);
		$array = $list->getArray();

		$this->assertCount($list->count(), $array);
		for ($i = 0; $i < $list->count(); $i++) {
			$this->assertSame($list[$i], $array[$i]);
		}

		$array[0] *= 10;
		$array[1] *= 10;
		$array[2] *= 10;
		for ($i = 0; $i < $list->count(); $i++) {
			$this->assertNotSame($list[$i], $array[$i]);
		}
	}

	function test_getIterator()
	{
		$expected = [1, 2, 3];
		$list = Vector::create($expected);
		foreach ($list as $key => $actual) {
			$this->assertSame($expected[$key], $actual);
		}
	}
}
