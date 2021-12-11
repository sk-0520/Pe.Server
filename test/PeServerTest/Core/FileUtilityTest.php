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
		$sep = DIRECTORY_SEPARATOR;
		$tests = [
			new Data("a${sep}b", "a", "b"),
			new Data("a${sep}b", "a", '', "b"),
			new Data("a${sep}b${sep}c", '', "a", 'b', "c", ''),
			new Data("${sep}", "${sep}"),
			new Data("abc", 'abc'),
			new Data("abc${sep}def${sep}GHI", 'abc', 'def', 'ghi', '..', '.', 'GHI'),
			new Data("${sep}abc${sep}def${sep}GHI", "${sep}abc", 'def', 'ghi', '..', '.', 'GHI'),
		];
		foreach ($tests as $test) {
			$actual = FileUtility::joinPath(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}
}
