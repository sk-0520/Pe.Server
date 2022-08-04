<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use PeServerTest\Data;
use PeServerTest\TestClass;
use PeServer\Core\UrlUtility;

class UrlUtilityTest extends TestClass
{
	public function test_buildQuery()
	{
		$tests = [
			new Data('a=b', ['a' => 'b']),
			new Data('a=b&c=d', ['a' => 'b', 'c' => 'd']),
			new Data('c=d&a=b', ['c' => 'd', 'a' => 'b']),
			new Data('x', ['x']),
			new Data('a=b&x', ['a' => 'b', 'x']),
			new Data('a=b&x&y=z', ['a' => 'b', 'x', 'y' => 'z']),
		];
		foreach ($tests as $test) {
			$actual = UrlUtility::buildQuery(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}
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
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}
}
