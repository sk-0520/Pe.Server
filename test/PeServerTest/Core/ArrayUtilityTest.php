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
}
