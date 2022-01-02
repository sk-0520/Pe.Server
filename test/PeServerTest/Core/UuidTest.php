<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use \LogicException;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\TrueKeeper;
use PeServer\Core\Uuid;
use PeServerTest\Data;
use PeServerTest\TestClass;

class UuidTest extends TestClass
{
	public function test_isEqualGuid()
	{
		$input = '70457e15-8928-4418-9b27-30bd46b1ae30';
		$tests = [
			new Data(true, $input, '70457e15-8928-4418-9b27-30bd46b1ae30'),
			new Data(true, $input, '70457E15-8928-4418-9B27-30BD46B1AE30'),

			new Data(true, $input, '70457e15892844189b2730bd46b1ae30'),
			new Data(true, $input, '70457E15892844189B2730BD46B1AE30'),

			new Data(true, $input, '{70457e15-8928-4418-9b27-30bd46b1ae30}'),
			new Data(true, $input, '{70457E15-8928-4418-9B27-30BD46B1AE30}'),

			new Data(true, $input, '{70457e15892844189b2730bd46b1ae30}'),
			new Data(true, $input, '{70457E15892844189B2730BD46B1AE30}'),
		];
		foreach ($tests as $test) {
			$actual = Uuid::isEqualGuid(...$test->args);
			$this->assertBoolean($test->expected, $actual, $test->str());
		}
	}
}
