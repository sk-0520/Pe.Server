<?php

declare(strict_types=1);

namespace PeServerTest\Core\Version;

use PeServerTest\Data;
use PeServerTest\TestClass;
use PeServer\Core\Version\CliVersion;

class CliVersionTest extends TestClass
{
	public function test_tryParse_success()
	{
		$tests = [
			new Data('1.0.0', '1'),
			new Data('10.0.0', '10'),
			new Data('1.2.0', '1.2'),
			new Data('1.20.0', '1.20'),
			new Data('1.2.3', '1.2.3'),
			new Data('1.2.30', '1.2.30'),
			new Data('1.2.3.4', '1.2.3.4'),
			new Data('1.2.3.40', '1.2.3.40'),
		];
		foreach ($tests as $test) {
			$actual = CliVersion::tryParse($test->args[0], $result);
			$this->assertTrue($actual, $test->str());
			$this->assertSame($test->expected, $result->toString(), $test->str());
		}
	}
}
