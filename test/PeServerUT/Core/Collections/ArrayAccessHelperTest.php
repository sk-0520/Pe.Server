<?php

declare(strict_types=1);

namespace PeServerUT\Core\Collections;

use PeServer\Core\Collections\ArrayAccessHelper;
use PeServer\Core\Throws\IndexOutOfRangeException;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;
use TypeError;

class ArrayAccessHelperTest extends TestClass
{
	#region function

	public static function provider_offsetExistsUInt()
	{
		return [
			[false, null],
			[false, 3.14],
			[false, ""],
			[false, true],
			[false, false],
			[false, []],
			[false, new stdClass()],
			[false, -1],
			[true, 0],
			[true, 1],
		];
	}

	#[DataProvider('provider_offsetExistsUInt')]
	public function test_offsetExistsUInt($expected, mixed $value)
	{
		$actual = ArrayAccessHelper::offsetExistsUInt($value);
		$this->assertSame($expected, $actual);
	}

	public static function provider_offsetExistsUInt_throw()
	{
		return [
			[TypeError::class, null],
			[TypeError::class, 3.14],
			[TypeError::class, ""],
			[TypeError::class, true],
			[TypeError::class, false],
			[TypeError::class, []],
			[TypeError::class, new stdClass()],
			[IndexOutOfRangeException::class, -1],
		];
	}

	#[DataProvider('provider_offsetExistsUInt_throw')]
	public function test_offsetExistsUInt_throw($expected, mixed $value)
	{
		$this->expectException($expected);
		ArrayAccessHelper::offsetGetUInt($value);
	}

	public function test_offsetGetUInt()
	{
		ArrayAccessHelper::offsetGetUInt(0);
		ArrayAccessHelper::offsetGetUInt(1);
		$this->success();
	}

	#enderrgion
}
