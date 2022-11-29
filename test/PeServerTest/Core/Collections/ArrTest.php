<?php

declare(strict_types=1);

namespace PeServerTest\Core\Collections;

use PeServer\Core\Collections\Arr;
use PeServer\Core\Collections\OrderBy;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Throws\KeyNotFoundException;
use PeServerTest\Data;
use PeServerTest\TestClass;

class ArrTest extends TestClass
{
	public function test_isNullOrEmpty()
	{
		$tests = [
			new Data(true, null),
			new Data(true, []),
			new Data(false, [0]),
			new Data(false, [0, 1]),
		];
		foreach ($tests as $test) {
			$actual = Arr::isNullOrEmpty(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_getOr()
	{
		$tests = [
			new Data(10, [10, 20, 30], 0, -1),
			new Data(20, [10, 20, 30], 1, -1),
			new Data(30, [10, 20, 30], 2, -1),
			new Data(-1, [10, 20, 30], 3, -1),
			new Data('A', ['a' => 'A', 'b' => 'B'], 'a', 'c'),
			new Data('B', ['a' => 'A', 'b' => 'B'], 'b', 'c'),
			new Data('c', ['a' => 'A', 'b' => 'B'], 'c', 'c'),
			new Data('c', ['a' => 'A', 'b' => 'B'], 'C', 'c'),
		];
		foreach ($tests as $test) {
			$actual = Arr::getOr(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_tryGet()
	{
		$tests = [
			new Data(['actual' => true, 'result' => 10], [10, 20, 30], 0),
			new Data(['actual' => true, 'result' => 20], [10, 20, 30], 1),
			new Data(['actual' => true, 'result' => 30], [10, 20, 30], 2),
			new Data(['actual' => false, 'result' => 'ないんだわ'], [10, 20, 30], 3),
			new Data(['actual' => true, 'result' => '123'], ['A' => '123', 'B' => '456', 'C' => '789'], 'A'),
			new Data(['actual' => true, 'result' => '456'], ['A' => '123', 'B' => '456', 'C' => '789'], 'B'),
			new Data(['actual' => true, 'result' => '789'], ['A' => '123', 'B' => '456', 'C' => '789'], 'C'),
			new Data(['actual' => false, 'result' => 'ないんだわ'], ['A' => '123', 'B' => '456', 'C' => '789'], 0),
		];
		foreach ($tests as $test) {
			$actual = Arr::tryGet($test->args[0], $test->args[1], $result);
			$this->assertSame($test->expected['actual'], $actual, $test->str());
			if ($actual) {
				$this->assertSame($test->expected['result'], $result, $test->str());
			}
		}
	}

	public function test_getCount()
	{
		$tests = [
			new Data(0, null),
			new Data(0, []),
			new Data(1, [0]),
			new Data(1, ['A' => 0]),
			new Data(2, ['A' => 0, 'B' => 1]),
			new Data(3, ['A' => 0, 'B' => 1, 9]),
		];
		foreach ($tests as $test) {
			$actual = Arr::getCount(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_containsValue()
	{
		$input = [10, 20, 30, 40];
		$tests = [
			new Data(true, $input, 10),
			new Data(true, $input, 20),
			new Data(true, $input, 30),
			new Data(true, $input, 40),
			new Data(false, $input, -10),
			new Data(false, $input, -20),
			new Data(false, $input, -30),
			new Data(false, $input, -40),
		];
		foreach ($tests as $test) {
			$actual = Arr::containsValue(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_existsKey()
	{
		$tests = [
			new Data(true, [100], 0),
			new Data(true, [50 => 100], 50),
			new Data(false, [50 => 100], 0),
			new Data(false, ['A' => 100], 0),
			new Data(true, ['A' => 100], 'A'),
			new Data(false, ['A' => 100], 'B'),
		];
		foreach ($tests as $test) {
			$actual = Arr::containsKey(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
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

	public function test_in()
	{
		$tests = [
			new Data(true, [100], 100),
			new Data(true, [50 => 100], 100),
			new Data(false, [50 => 100], 50),
			new Data(true, ['A' => 100], 100),
			new Data(false, ['A' => 100], 'A'),
		];
		foreach ($tests as $test) {
			$actual = Arr::in(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_getFirstKey()
	{
		$tests = [
			new Data(0, [100]),
			new Data(50, [50 => 100]),
			new Data('A', ['A' => 100]),
			new Data(0, [0, 'A' => 100]),
			new Data('A', ['A' => 100, 0]),
		];
		foreach ($tests as $test) {
			$actual = Arr::getFirstKey(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_getFirstKey_throw()
	{
		$this->expectException(KeyNotFoundException::class);
		Arr::getFirstKey([]);
		$this->fail();
	}

	public function test_getLastKey()
	{
		$tests = [
			new Data(0, [100]),
			new Data(50, [50 => 100]),
			new Data('A', ['A' => 100]),
			new Data('A', [0, 'A' => 100]),
			new Data(0, ['A' => 100, 0]), // 0なんかぁ
		];
		foreach ($tests as $test) {
			$actual = Arr::getLastKey(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_getLastKey_throw()
	{
		$this->expectException(KeyNotFoundException::class);
		Arr::getLastKey([]);
		$this->fail();
	}

	public function test_isListImpl()
	{
		$tests = [
			new Data(true, []),
			new Data(true, [100]),
			new Data(true, [0 => 100]),
			new Data(false, [1 => 100]),
			new Data(false, [50 => 100]),
			new Data(false, ['A' => 100]),
			new Data(false, [0, 'A' => 100]),
			new Data(false, ['A' => 100, 0]), // 0なんかぁ
		];
		foreach ($tests as $test) {
			$actual = Arr::isListImpl(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_isList()
	{
		$tests = [
			new Data(true, []),
			new Data(true, [100]),
			new Data(true, [0 => 100]),
			new Data(false, [1 => 100]),
			new Data(false, [50 => 100]),
			new Data(false, ['A' => 100]),
			new Data(false, [0, 'A' => 100]),
			new Data(false, ['A' => 100, 0]), // 0なんかぁ
		];
		foreach ($tests as $test) {
			$actual = Arr::isList(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_toUnique()
	{
		$tests = [
			new Data([], []),
			new Data([0], [0, 0]),
			new Data([1, 2, 3], [1, 2, 3, 1, 2, 3, 3, 2, 1]),
		];
		foreach ($tests as $test) {
			$actual = Arr::toUnique(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_replace()
	{
		$tests = [
			new Data([], [], []),
			new Data([2], [1], [2]),
			new Data([2, 3], [1], [2, 3]),
			new Data(['a' => 'A', 'b' => 'B'], ['a' => 'A'], ['b' => 'B']),
		];
		foreach ($tests as $test) {
			$actual = Arr::replace(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_getRandomKeys()
	{
		$tests = [
			new Data(1, [1], 1),
			new Data(1, [1, 2, 3], 1),
			new Data(2, [1, 2, 3], 2),
		];
		foreach ($tests as $test) {
			$actual = Arr::getRandomKeys(...$test->args);
			$this->assertSame($test->expected, Arr::getCount($actual), $test->str());
		}
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

	public function test_reverse()
	{
		$tests = [
			new Data([3, 2, 1], [1, 2, 3]),
		];
		foreach ($tests as $test) {
			$actual = Arr::reverse(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}


	public function test_flip()
	{
		$tests = [
			new Data([0 => 'a', 1 => 'b'], ['a' => 0, 'b' => 1]),
		];
		foreach ($tests as $test) {
			$actual = Arr::flip(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
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

		$actual4 = Arr::map($input, 'PeServerTest\Core\Collections\map_function');
		$this->assertSame('aa', $actual4['A']);
		$this->assertSame('bb', $actual4[10]);
	}

	public function test_range()
	{
		$tests = [
			new Data([], 0, 0),
			new Data([0], 0, 1),
			new Data([1], 1, 1),
			new Data([0, 1], 0, 2),
			new Data([10], 10, 1),
			new Data([10, 11], 10, 2),
			new Data([], -10, 0),
			new Data([-10], -10, 1),
			new Data([-10, -9, -8], -10, 3),
		];
		foreach ($tests as $test) {
			$actual = Arr::range(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_range_throw()
	{
		$this->expectException(ArgumentException::class);
		Arr::range(0, -1);
		$this->fail();
	}

	public function test_repeat()
	{
		$tests = [
			new Data([], 0, 0),
			new Data([0, 0, 0], 0, 3),
			new Data([3, 3, 3], 3, 3),
			new Data(['AZ', 'AZ'], 'AZ', 2),
		];
		foreach ($tests as $test) {
			$actual = Arr::repeat(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_repeat_throw()
	{
		$this->expectException(ArgumentException::class);
		Arr::repeat('VALUE', -1);
		$this->fail();
	}

	public function test_sortByValue()
	{
		$tests = [
			new Data([], [], OrderBy::ASCENDING),
			new Data([], [], OrderBy::DESCENDING),
			new Data([-1, 0, 1, 2], [2, 1, 0, -1], OrderBy::ASCENDING),
			new Data([2, 1, 0, -1], [-1, 0, 1, 2], OrderBy::DESCENDING),
			new Data(['A', 'a', 'z'], ['z', 'A', 'a'], OrderBy::ASCENDING),
			new Data(['z', 'a', 'A'], ['z', 'A', 'a'], OrderBy::DESCENDING),
		];
		foreach ($tests as $test) {
			$actual = Arr::sortByValue(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_sortByKey()
	{
		$tests = [
			new Data([], [], OrderBy::ASCENDING),
			new Data([], [], OrderBy::DESCENDING),
			new Data(['M' => '13', 'a' => 'Z', 'z' => 'A'], ['z' => 'A', 'M' => '13', 'a' => 'Z'], OrderBy::ASCENDING),
			new Data(['z' => 'A', 'a' => 'Z', 'M' => '13'], ['z' => 'A', 'M' => '13', 'a' => 'Z'], OrderBy::DESCENDING),
		];
		foreach ($tests as $test) {
			$actual = Arr::sortByKey(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_sortNaturalByValue()
	{
		$tests = [
			new Data([], [], false),
			new Data([], [], true),
			new Data(['A-100', 'a-1', 'a-200', 'b'], ['a-200', 'a-1', 'A-100', 'b'], false),
			new Data(['a-1', 'A-100', 'a-200', 'b'], ['a-200', 'a-1', 'A-100', 'b'], true),
		];
		foreach ($tests as $test) {
			$actual = Arr::sortNaturalByValue(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_sortCallbackByValue()
	{
		$tests = [
			new Data([], [], fn ($a, $b) => $a <=> $b),
			new Data([1, 3, 10], [3, 10, 1], fn ($a, $b) => $a <=> $b),
			new Data([10, 3, 1], [3, 10, 1], fn ($a, $b) => $b <=> $a),
			new Data([['key' => 1], ['key' => 3], ['key' => 10]], [['key' => 3], ['key' => 10], ['key' => 1]], fn ($a, $b) => $a['key'] <=> $b['key']),
			new Data([['key' => 10], ['key' => 3], ['key' => 1]], [['key' => 3], ['key' => 10], ['key' => 1]], fn ($a, $b) => $b['key'] <=> $a['key']),
		];
		foreach ($tests as $test) {
			$actual = Arr::sortCallbackByValue(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_sortCallbackByKey()
	{
		$tests = [
			new Data([], [], fn ($a, $b) => $a <=> $b),
			new Data(['a' => 10, 'b' => 3, 'c' => 1], ['b' => 3, 'a' => 10, 'c' => 1], fn ($a, $b) => $a <=> $b),
			new Data(['c' => 1, 'b' => 3, 'a' => 10], ['b' => 3, 'a' => 10, 'c' => 1], fn ($a, $b) => $b <=> $a),
		];
		foreach ($tests as $test) {
			$actual = Arr::sortCallbackByKey(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}
}

function map_function($value, $key)
{
	return $value . $value;
}
