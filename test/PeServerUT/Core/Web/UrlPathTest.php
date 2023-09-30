<?php

declare(strict_types=1);

namespace PeServerUT\Core\Web;

use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\IndexOutOfRangeException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Throws\NotSupportedException;
use PeServer\Core\Web\UrlPath;
use PeServerTest\Data;
use PeServerTest\TestClass;
use TypeError;

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
			new Data(['a', 'b', 'c'], 'a/b', ['c']),
			new Data(['a', 'b', 'c', 'd'], 'a/b', ['c', 'd']),
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

	public static function provider_add_throw()
	{
		return [
			[[], '$element: empty array'],
			[[''], '$element[0]: whitespace string'],
			[['a', ''], '$element[1]: whitespace string'],
			[[1], '$element[0]: not string'],
			[['a', 1], '$element[1]: not string'],
		];
	}
	/** @dataProvider provider_add_throw */
	public function test_add_throw($input, $message)
	{
		$path = new UrlPath('');
		$this->expectException(ArgumentException::class);
		$this->expectExceptionMessage($message);
		$path->add($input);
		$this->fail();
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
			if (!$test->args[1]) {
				$this->assertSame($test->expected, (string)$actual, $test->str());
				$this->assertSame($test->expected, strval($actual), $test->str());
			}
		}
	}

	public function test_ArrayAccess()
	{
		$url = new UrlPath("/a/b/c");
		$this->assertSame('a', $url[0]);
		$this->assertSame('b', $url[1]);
		$this->assertSame('c', $url[2]);


		try {
			$_ = $url['key'];
			$this->fail();
		} catch (TypeError) {
			$this->success();
		}

		try {
			$_ = $url[-1];
			$this->fail();
		} catch (IndexOutOfRangeException) {
			$this->success();
		}

		try {
			$_ = $url[3];
			$this->fail();
		} catch (IndexOutOfRangeException) {
			$this->success();
		}

		$this->assertTrue(isset($url[0]));
		$this->assertTrue(isset($url[1]));
		$this->assertTrue(isset($url[2]));
		$this->assertFalse(isset($url[3]));
		$this->assertFalse(isset($url['key']));
		$this->assertFalse(isset($url[-1]));

		try {
			$url[0] = 'A';
			$this->fail();
		} catch (NotSupportedException) {
			$this->success();
		}

		try {
			unset($url[0]);
			$this->fail();
		} catch (NotSupportedException) {
			$this->success();
		}

		$this->assertCount(3, $url);
		$this->assertSame(['a', 'b', 'c'], iterator_to_array($url));
	}

	public function test_ArrayAccess_empty()
	{
		$url = new UrlPath('');

		try {
			$_ = $url[0];
			$this->fail();
		} catch (IndexOutOfRangeException) {
			$this->success();
		}

		$this->assertFalse(isset($url[0]));

		$this->assertCount(0, $url);
		$this->assertSame([], iterator_to_array($url));
	}
}
