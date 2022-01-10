<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use PeServerTest\TestClass;
use PeServerTest\Data;
use PeServer\Core\SizeConverter;

class SizeConverterTest extends TestClass
{
	public function test_convertHumanReadableByte()
	{
		$tests = [
			new Data('0 byte', 0),
			new Data('1 byte', 1),
			new Data('1023 byte', 1023),
			new Data('1 KB', 1024),
		];
		foreach ($tests as $test) {
			$sc = new SizeConverter();
			$actual = $sc->convertHumanReadableByte(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}
}
