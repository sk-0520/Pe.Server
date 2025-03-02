<?php

declare(strict_types=1);

namespace PeServerUT\Core\Collections;

use Countable;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Collections\OrderBy;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\KeyNotFoundException;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TypeError;

class ArrTest extends TestClass
{
	public static function provider_isNullOrEmpty()
	{
		return [
			[true, null],
			[true, []],
			[false, [0]],
			[false, [0, 1]],
		];
	}

	#[DataProvider('provider_isNullOrEmpty')]
	public function test_isNullOrEmpty(bool $expected, ?array $array)
	{
		$actual = Arr::isNullOrEmpty($array);
		$this->assertSame($expected, $actual);
	}

	public static function provider_tryGet()
	{
		return [
			[['actual' => true, 'result' => 10], [10, 20, 30], 0],
			[['actual' => true, 'result' => 20], [10, 20, 30], 1],
			[['actual' => true, 'result' => 30], [10, 20, 30], 2],
			[['actual' => false, 'result' => 'ないんだわ'], [10, 20, 30], 3],
			[['actual' => true, 'result' => '123'], ['A' => '123', 'B' => '456', 'C' => '789'], 'A'],
			[['actual' => true, 'result' => '456'], ['A' => '123', 'B' => '456', 'C' => '789'], 'B'],
			[['actual' => true, 'result' => '789'], ['A' => '123', 'B' => '456', 'C' => '789'], 'C'],
			[['actual' => false, 'result' => 'ないんだわ'], ['A' => '123', 'B' => '456', 'C' => '789'], 0],
		];
	}

	#[DataProvider('provider_tryGet')]
	public function test_tryGet($expected, ?array $array, int|string $key)
	{
		$actual = Arr::tryGet($array, $key, $result);
		$this->assertSame($expected['actual'], $actual);
		if ($actual) {
			$this->assertSame($expected['result'], $result);
		}
	}

	public static function provider_getCount()
	{
		return [
			[0, null],
			[0, []],
			[1, [0]],
			[1, ['A' => 0]],
			[2, ['A' => 0, 'B' => 1]],
			[3, ['A' => 0, 'B' => 1, 9]],
		];
	}

	#[DataProvider('provider_getCount')]
	public function test_getCount(int $expected, array|Countable|null $array)
	{
		$actual = Arr::getCount($array);
		$this->assertSame($expected, $actual);
	}

	public static function provider_containsValue()
	{
		$input = [10, 20, 30, 40];
		return [
			[true, $input, 10],
			[true, $input, 20],
			[true, $input, 30],
			[true, $input, 40],
			[false, $input, -10],
			[false, $input, -20],
			[false, $input, -30],
			[false, $input, -40],
		];
	}

	#[DataProvider('provider_containsValue')]
	public function test_containsValue(bool $expected, array $haystack, mixed $needle)
	{
		$actual = Arr::containsValue($haystack, $needle);
		$this->assertSame($expected, $actual);
	}

	public static function provider_existsKey()
	{
		return [
			[true, [100], 0],
			[true, [50 => 100], 50],
			[false, [50 => 100], 0],
			[false, ['A' => 100], 0],
			[true, ['A' => 100], 'A'],
			[false, ['A' => 100], 'B'],
		];
	}

	#[DataProvider('provider_existsKey')]
	public function test_existsKey(bool $expected, array $haystack, int|string $key)
	{
		$actual = Arr::containsKey($haystack, $key);
		$this->assertSame($expected, $actual);
	}

	public function test_getKeys()
	{
		$expected = [
			0, 1, 2
		];
		$input = [
			$expected[0] => 'A',
			$expected[1] => 'B',
			$expected[2] => 'C',
		];
		$actual = Arr::getKeys($input);
		for ($i = 0; $i < count($expected); $i++) {
			$this->assertSame($expected[$i], $actual[$i]);
		}
	}

	public function test_getValues()
	{
		$expected = [
			0, 1, 2
		];
		$input = [
			'A' => $expected[0],
			'B' => $expected[1],
			'C' => $expected[2],
		];
		$actual = Arr::getValues($input);
		for ($i = 0; $i < count($expected); $i++) {
			$this->assertSame($expected[$i], $actual[$i]);
		}
	}

	public static function provider_in()
	{
		return [
			[true, [100], 100],
			[true, [50 => 100], 100],
			[false, [50 => 100], 50],
			[true, ['A' => 100], 100],
			[false, ['A' => 100], 'A'],
		];
	}

	#[DataProvider('provider_in')]
	public function test_in(bool $expected, array $haystack, mixed $needle)
	{
		$actual = Arr::in($haystack, $needle);
		$this->assertSame($expected, $actual);
	}

	public static function provider_getFirstKey()
	{
		return [
			[0, [100]],
			[50, [50 => 100]],
			['A', ['A' => 100]],
			[0, [0, 'A' => 100]],
			['A', ['A' => 100, 0]],
		];
	}

	#[DataProvider('provider_getFirstKey')]
	public function test_getFirstKey($expected, array $array)
	{
		$actual = Arr::getFirstKey($array);
		$this->assertSame($expected, $actual);
	}

	public function test_getFirstKey_throw()
	{
		$this->expectException(KeyNotFoundException::class);
		Arr::getFirstKey([]);
		$this->fail();
	}

	public static function provider_getLastKey()
	{
		return [
			[0, [100]],
			[50, [50 => 100]],
			['A', ['A' => 100]],
			['A', [0, 'A' => 100]],
			[0, ['A' => 100, 0]], // 0なんかぁ
		];
	}

	#[DataProvider('provider_getLastKey')]
	public function test_getLastKey($expected, array $array)
	{
		$actual = Arr::getLastKey($array);
		$this->assertSame($expected, $actual);
	}

	public function test_getLastKey_throw()
	{
		$this->expectException(KeyNotFoundException::class);
		Arr::getLastKey([]);
		$this->fail();
	}

	public static function provider_isListImpl()
	{
		return [
			[true, []],
			[true, [100]],
			[true, [0 => 100]],
			[false, [1 => 100]],
			[false, [50 => 100]],
			[false, ['A' => 100]],
			[false, [0, 'A' => 100]],
			[false, ['A' => 100, 0]],
		];
	}

	#[DataProvider('provider_isListImpl')]
	public function test_isListImpl($expected, array $array)
	{
		$actual = Arr::isListImpl($array);
		$this->assertSame($expected, $actual);
	}

	public static function provider_isList()
	{
		return [
			[true, []],
			[true, [100]],
			[true, [0 => 100]],
			[false, [1 => 100]],
			[false, [50 => 100]],
			[false, ['A' => 100]],
			[false, [0, 'A' => 100]],
			[false, ['A' => 100, 0]], // 0なん]ぁ
		];
	}

	#[DataProvider('provider_isList')]
	public function test_isList(bool $expected, array $array)
	{
		$actual = Arr::isList($array);
		$this->assertSame($expected, $actual);
	}

	public static function provider_toUnique()
	{
		return [
			[[], []],
			[[0], [0, 0]],
			[[1, 2, 3], [1, 2, 3, 1, 2, 3, 3, 2, 1]],
		];
	}

	#[DataProvider('provider_toUnique')]
	public function test_toUnique(array $expected, array $array)
	{
		$actual = Arr::toUnique($array);
		$this->assertSame($expected, $actual);
	}

	public static function provider_replace()
	{
		return [
			[[], [], [], true],
			[[2], [1], [2], true],
			[[2, 3], [1], [2, 3], true],
			[['a' => 'A', 'b' => 'B'], ['a' => 'A'], ['b' => 'B'], true],
		];
	}

	#[DataProvider('provider_replace')]
	public function test_replace(array $expected, array $base, array $overwrite, bool $recursive)
	{
		$actual = Arr::replace($base, $overwrite, $recursive);
		$this->assertSame($expected, $actual);
	}

	public static function provider_getRandomKeys()
	{
		return [
			[1, [1], 1],
			[1, [1, 2, 3], 1],
			[2, [1, 2, 3], 2],
		];
	}

	#[DataProvider('provider_getRandomKeys')]
	public function test_getRandomKeys(int $expected, array $array, int $count)
	{
		$actual = Arr::getRandomKeys($array, $count);
		$this->assertSame($expected, Arr::getCount($actual));
	}

	public function test_getRandomKeys_throw_1()
	{
		$this->expectException(ArgumentException::class);
		$this->expectExceptionMessage('$count');

		Arr::getRandomKeys([], 0);

		$this->fail();
	}

	public function test_getRandomKeys_throw_2()
	{
		$this->expectException(ArgumentException::class);
		$this->expectExceptionMessage('$length < $count');

		Arr::getRandomKeys([], 1);

		$this->fail();
	}

	public static function provider_reverse()
	{
		return [
			[[3, 2, 1], [1, 2, 3]],
		];
	}

	#[DataProvider('provider_reverse')]
	public function test_reverse(array $expected, array $input)
	{
		$actual = Arr::reverse($input);
		$this->assertSame($expected, $actual);
	}


	public static function provider_flip()
	{
		return [
			[[0 => 'a', 1 => 'b'], ['a' => 0, 'b' => 1]],
		];
	}

	#[DataProvider('provider_flip')]
	public function test_flip(array $expected, array $input)
	{
		$actual = Arr::flip($input);
		$this->assertSame($expected, $actual);
	}

	public function test_flip_throw()
	{
		$this->expectException(ArgumentException::class);
		Arr::flip([0 => ['array' => 'value']]);
		$this->fail();
	}

	public function map_instance($value, $key)
	{
		return $value . $value;
	}
	public static function map_static($value, $key)
	{
		return $value . $value;
	}

	public function test_map()
	{
		$input = [
			'A' => 'a',
			10 => 'b'
		];

		$actual1 = Arr::map($input, fn ($v) => $v . $v);
		$this->assertSame('aa', $actual1['A']);
		$this->assertSame('bb', $actual1[10]);

		$actual2 = Arr::map($input, [$this, 'map_instance']);
		$this->assertSame('aa', $actual2['A']);
		$this->assertSame('bb', $actual2[10]);

		$actual3 = Arr::map($input, $this::class . '::map_static');
		$this->assertSame('aa', $actual3['A']);
		$this->assertSame('bb', $actual3[10]);

		$actual4 = Arr::map($input, 'PeServerUT\Core\Collections\map_function');
		$this->assertSame('aa', $actual4['A']);
		$this->assertSame('bb', $actual4[10]);
	}

	public static function provider_range()
	{
		return [
			[[], 0, 0],
			[[0], 0, 1],
			[[1], 1, 1],
			[[0, 1], 0, 2],
			[[10], 10, 1],
			[[10, 11], 10, 2],
			[[], -10, 0],
			[[-10], -10, 1],
			[[-10, -9, -8], -10, 3],
		];
	}

	#[DataProvider('provider_range')]
	public function test_range(array $expected, int $start, int $count)
	{
		$actual = Arr::range($start, $count);
		$this->assertSame($expected, $actual);
	}

	public function test_range_throw()
	{
		$this->expectException(ArgumentException::class);
		Arr::range(0, -1);
		$this->fail();
	}

	public static function provider_repeat()
	{
		return [
			[[], 0, 0],
			[[0, 0, 0], 0, 3],
			[[3, 3, 3], 3, 3],
			[['AZ', 'AZ'], 'AZ', 2],
		];
	}

	#[DataProvider('provider_repeat')]
	public function test_repeat(array $expected, mixed $value, int $count)
	{
		$actual = Arr::repeat($value, $count);
		$this->assertSame($expected, $actual);
	}

	public function test_repeat_throw()
	{
		$this->expectException(ArgumentException::class);
		Arr::repeat('VALUE', -1);
		$this->fail();
	}

	public static function provider_sortByValue()
	{
		return [
			[[], [], OrderBy::Ascending],
			[[], [], OrderBy::Descending],
			[[-1, 0, 1, 2], [2, 1, 0, -1], OrderBy::Ascending],
			[[2, 1, 0, -1], [-1, 0, 1, 2], OrderBy::Descending],
			[['A', 'a', 'z'], ['z', 'A', 'a'], OrderBy::Ascending],
			[['z', 'a', 'A'], ['z', 'A', 'a'], OrderBy::Descending],
		];
	}

	#[DataProvider('provider_sortByValue')]
	public function test_sortByValue(array $expected, array $array, OrderBy $orderBy)
	{
		$actual = Arr::sortByValue($array, $orderBy);
		$this->assertSame($expected, $actual);
	}

	public static function provider_sortByKey()
	{
		return [
			[[], [], OrderBy::Ascending],
			[[], [], OrderBy::Descending],
			[['M' => '13', 'a' => 'Z', 'z' => 'A'], ['z' => 'A', 'M' => '13', 'a' => 'Z'], OrderBy::Ascending],
			[['z' => 'A', 'a' => 'Z', 'M' => '13'], ['z' => 'A', 'M' => '13', 'a' => 'Z'], OrderBy::Descending],
		];
	}

	#[DataProvider('provider_sortByKey')]
	public function test_sortByKey(array $expected, array $array, OrderBy $orderBy)
	{
		$actual = Arr::sortByKey($array, $orderBy);
		$this->assertSame($expected, $actual);
	}

	public static function provider_sortNaturalByValue()
	{
		return [
			[[], [], false],
			[[], [], true],
			[['A-100', 'a-1', 'a-200', 'b'], ['a-200', 'a-1', 'A-100', 'b'], false],
			[['a-1', 'A-100', 'a-200', 'b'], ['a-200', 'a-1', 'A-100', 'b'], true],
		];
	}

	#[DataProvider('provider_sortNaturalByValue')]
	public function test_sortNaturalByValue(array $expected, array $array, bool $ignoreCase)
	{
		$actual = Arr::sortNaturalByValue($array, $ignoreCase);
		$this->assertSame($expected, $actual);
	}

	public static function provider_sortCallbackByValue()
	{
		return [
			[[], [], fn ($a, $b) => $a <=> $b],
			[[1, 3, 10], [3, 10, 1], fn ($a, $b) => $a <=> $b],
			[[10, 3, 1], [3, 10, 1], fn ($a, $b) => $b <=> $a],
			[[['key' => 1], ['key' => 3], ['key' => 10]], [['key' => 3], ['key' => 10], ['key' => 1]], fn ($a, $b) => $a['key'] <=> $b['key']],
			[[['key' => 10], ['key' => 3], ['key' => 1]], [['key' => 3], ['key' => 10], ['key' => 1]], fn ($a, $b) => $b['key'] <=> $a['key']],
		];
	}

	#[DataProvider('provider_sortCallbackByValue')]
	public function test_sortCallbackByValue(array $expected, array $array, callable $callback)
	{
		$actual = Arr::sortCallbackByValue($array, $callback);
		$this->assertSame($expected, $actual);
	}

	public static function provider_sortCallbackByKey()
	{
		return [
			[[], [], fn ($a, $b) => $a <=> $b],
			[[], [], fn ($a, $b) => $a <=> $b],
			[['a' => 10, 'b' => 3, 'c' => 1], ['b' => 3, 'a' => 10, 'c' => 1], fn ($a, $b) => $a <=> $b],
			[['c' => 1, 'b' => 3, 'a' => 10], ['b' => 3, 'a' => 10, 'c' => 1], fn ($a, $b) => $b <=> $a],
		];
	}

	#[DataProvider('provider_sortCallbackByKey')]
	public function test_sortCallbackByKey(array $expected, array $array, callable $callback)
	{
		$actual = Arr::sortCallbackByKey($array, $callback);
		$this->assertSame($expected, $actual);
	}
}

function map_function($value, $key)
{
	return $value . $value;
}

class LocalGetOr1
{
	public function __construct(public int $value)
	{
		//NOP
	}
}

class LocalGetOr2 extends LocalGetOr1
{
	public function __construct(public int $value)
	{
		parent::__construct($value);
	}
}
