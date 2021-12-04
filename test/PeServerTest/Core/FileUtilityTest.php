<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use \PeServerTest\Data;
use \PeServerTest\TestClass;
use \PeServer\Core\FileUtility;

class FileUtilityTest extends TestClass
{
	public function test_join()
	{
		$tests = [
			new Data("a{$this->s(DIRECTORY_SEPARATOR)}b", "a", "b"),
		];
		foreach ($tests as $test) {
			$actual = FileUtility::join(...$test->args);
			$this->assertEquals($test->expected, $actual);
		}
	}
}
