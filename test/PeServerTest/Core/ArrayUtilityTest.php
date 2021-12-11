<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use \PeServerTest\Data;
use \PeServerTest\TestClass;
use \PeServer\Core\ArrayUtility;

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
}
