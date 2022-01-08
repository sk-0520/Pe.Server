<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use PeServerTest\Data;
use PeServerTest\TestClass;
use PeServer\Core\UrlUtility;

class UrlUtilityTest extends TestClass
{
	public function test_joinPath()
	{
		$tests = [
			new Data('a/b', 'a', 'b'),
			new Data('http://localhost/a', 'http://localhost', 'a'),
			new Data('http://localhost/a', 'http://localhost/', 'a'),
			new Data('http://localhost/a', 'http://localhost/', '/a'),
			new Data('http://localhost/a', 'http://localhost/', '/a/'),
			new Data('http://localhost/a/b', 'http://localhost', 'a', '/b/'),
			new Data('http://localhost/a?q=Q', 'http://localhost?q=Q', 'a'),
		];
		foreach ($tests as $test) {
			$actual = UrlUtility::joinPath(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}
}
