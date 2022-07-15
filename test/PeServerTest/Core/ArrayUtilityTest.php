<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use PeServer\Core\ArrayUtility;
use PeServer\Core\Throws\InvalidOperationException;
use PeServerTest\Data;
use PeServerTest\TestClass;

class ArrayUtilityTest extends TestClass
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
			$actual = ArrayUtility::isNullOrEmpty(...$test->args);
			$this->assertBoolean($test->expected, $actual, $test->str());
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
			$actual = ArrayUtility::getOr(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
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
			$actual = ArrayUtility::tryGet($test->args[0], $test->args[1], $result);
			$this->assertBoolean($test->expected['actual'], $actual, $test->str());
			if ($actual) {
				$this->assertEquals($test->expected['result'], $result, $test->str());
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
			$actual = ArrayUtility::getCount(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}

	public function test_contains()
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
			$actual = ArrayUtility::contains(...$test->args);
			$this->assertBoolean($test->expected, $actual, $test->str());
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
			$actual = ArrayUtility::existsKey(...$test->args);
			$this->assertBoolean($test->expected, $actual, $test->str());
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
		$actual = ArrayUtility::getKeys($input);
		for ($i = 0; $i < count($expected); $i++) {
			$this->assertEquals($expected[$i], $actual[$i]);
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
		$actual = ArrayUtility::getValues($input);
		for ($i = 0; $i < count($expected); $i++) {
			$this->assertEquals($expected[$i], $actual[$i]);
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
			$actual = ArrayUtility::in(...$test->args);
			$this->assertBoolean($test->expected, $actual, $test->str());
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
			$actual = ArrayUtility::getFirstKey(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}

	public function test_getFirstKey_error()
	{
		$this->expectException(InvalidOperationException::class);
		ArrayUtility::getFirstKey([]);
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
			$actual = ArrayUtility::getLastKey(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}

	public function test_getLastKey_error()
	{
		$this->expectException(InvalidOperationException::class);
		ArrayUtility::getLastKey([]);
		$this->fail();
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
			$actual = ArrayUtility::isList(...$test->args);
			$this->assertBoolean($test->expected, $actual, $test->str());
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
			$actual = ArrayUtility::toUnique(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}
}
