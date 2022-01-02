<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use \PeServerTest\Data;
use \PeServerTest\TestClass;
use \PeServer\Core\Regex;

class RegexTest extends TestClass
{
	public function test_isMatch()
	{
		$tests = [
			new Data(true, 'abc', '/a/')
		];
		foreach ($tests as $test) {
			$actual = Regex::isMatch(...$test->args);
			$this->assertBoolean($test->expected, $actual, $test->str());
		}
	}
}
