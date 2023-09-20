<?php

declare(strict_types=1);

namespace PeServerUT\Core\Web;

use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Web\UrlPath;
use PeServerTest\Data;
use PeServerTest\TestClass;
use PeServer\Core\Web\UrlUtility;

class UrlPathTest extends TestClass
{
	public static function provider_constructor_throw()
	{
		return [
			['path1/path2?'],
			['path1/path2#'],
		];
	}

	/** @dataProvider provider_constructor_throw */
	public function test_constructor_throw(string $input)
	{
		$this->expectException(ArgumentException::class);
		new UrlPath($input);
		$this->fail();
	}

	public function test_isValidElement()
	{
		$tests = [
			new Data(false, '/'),
			new Data(false, '?'),
			new Data(false, '#'),

			new Data(true, 'a'),
			new Data(true, '1'),

			new Data(true, '!'),
			new Data(true, '$'),
			new Data(true, '&'),
			new Data(true, "'"),
			new Data(true, '('),
			new Data(true, ')'),
			new Data(true, '*'),
			new Data(true, '+'),
			new Data(true, ','),
			new Data(true, ':'),
			new Data(true, '@'),
			new Data(true, ';'),
			new Data(true, '='),
		];
		foreach ($tests as $test) {
			$actual = UrlPath::isValidElement(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_isEmpty()
	{
		$tests = [
			new Data(true, ''),
			new Data(true, ' '),
			new Data(false, '/'),
			new Data(false, 'a'),
		];
		foreach ($tests as $test) {
			$actual = new UrlPath(...$test->args);
			$this->assertSame($test->expected, $actual->isEmpty(), $test->str());
		}
	}

	public function test_getElements()
	{
		$tests = [
			new Data([], '/'),
			new Data(['a'], 'a'),
			new Data(['a', 'b'], 'a/b'),
			new Data(['a', 'b'], '/a/b'),
			new Data(['a', 'b'], '/a/b/'),
		];
		foreach ($tests as $test) {
			$actual = new UrlPath(...$test->args);
			$this->assertSame($test->expected, $actual->getElements(), $test->str());
		}
	}

	public function test_getElements_throw()
	{
		$path = new UrlPath('');
		$this->expectException(InvalidOperationException::class);
		$path->getElements();
		$this->fail();
	}

	public function test_add()
	{
		$tests = [
			new Data([], '/', ''),
			new Data(['b'], '/', 'b'),
			new Data(['a', 'b', 'c'], 'a/b', 'c'),
		];
		foreach ($tests as $test) {
			$path = new UrlPath($test->args[0]);
			$actual = $path->add($test->args[1]);
			$this->assertSame($test->expected, $actual->getElements(), $test->str());
		}
	}

	public function test_add_empty()
	{
		$path = new UrlPath('');
		$actual = $path->add('a');
		$this->assertSame(['a'], $actual->getElements());
	}

	public function test_toString()
	{
		$tests = [
			new Data('', '', false),
			new Data('/a/b', '/a//b', false),
			new Data('/', '/', false),

			new Data('', '', true),
			new Data('/a/b/', '/a//b', true),
			new Data('/', '/', true),
		];
		foreach ($tests as $test) {
			$actual = new UrlPath($test->args[0]);
			$this->assertSame($test->expected, $actual->toString($test->args[1]), $test->str());
			if(!$test->args[1]) {
				$this->assertSame($test->expected, (string)$actual, $test->str());
				$this->assertSame($test->expected, strval($actual), $test->str());
			}
		}
	}
}
