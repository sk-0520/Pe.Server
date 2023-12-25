<?php

declare(strict_types=1);

namespace PeServerUT\Core\Web;

use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\IndexOutOfRangeException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Throws\NotSupportedException;
use PeServer\Core\Web\UrlPath;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;
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

	#[DataProvider('provider_constructor_throw')]
	public function test_constructor_throw(string $input)
	{
		$this->expectException(ArgumentException::class);
		new UrlPath($input);
		$this->fail();
	}

	public static function provider_isValidElement()
	{
		return [
			[false, '/'],
			[false, '?'],
			[false, '#'],

			[true, 'a'],
			[true, '1'],

			[true, '!'],
			[true, '$'],
			[true, '&'],
			[true, "'"],
			[true, '('],
			[true, ')'],
			[true, '*'],
			[true, '+'],
			[true, ','],
			[true, ':'],
			[true, '@'],
			[true, ';'],
			[true, '='],
		];
	}

	#[DataProvider('provider_isValidElement')]
	public function test_isValidElement(bool $expected, string $element)
	{
		$actual = UrlPath::isValidElement($element);
		$this->assertSame($expected, $actual);
	}

	public static function provider_isEmpty()
	{
		return [
			[true, ''],
			[true, ' '],
			[false, '/'],
			[false, 'a'],
		];
	}

	#[DataProvider('provider_isEmpty')]
	public function test_isEmpty(bool $expected, string $path)
	{
		$actual = new UrlPath($path);
		$this->assertSame($expected, $actual->isEmpty());
	}

	public static function provider_getElements()
	{
		return [
			[[], '/'],
			[['a'], 'a'],
			[['a', 'b'], 'a/b'],
			[['a', 'b'], '/a/b'],
		];
	}

	#[DataProvider('provider_getElements')]
	public function test_getElements(array $expected, string $path)
	{
		$actual = new UrlPath($path);
		$this->assertSame($expected, $actual->getElements());
	}

	public function test_getElements_throw()
	{
		$path = new UrlPath('');
		$this->expectException(InvalidOperationException::class);
		$path->getElements();
		$this->fail();
	}

	public static function provider_add()
	{
		return [
			[[], '/', ''],
			[['b'], '/', 'b'],
			[['a', 'b', 'c'], 'a/b', 'c'],
			[['a', 'b', 'c'], 'a/b', ['c']],
			[['a', 'b', 'c', 'd'], 'a/b', ['c', 'd']],
		];
	}

	#[DataProvider('provider_add')]
	public function test_add(array $expected, string $path, string|array $element)
	{
		$path = new UrlPath($path);
		$actual = $path->add($element);
		$this->assertSame($expected, $actual->getElements());
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
	#[DataProvider('provider_add_throw')]
	public function test_add_throw($input, $message)
	{
		$path = new UrlPath('');
		$this->expectException(ArgumentException::class);
		$this->expectExceptionMessage($message);
		$path->add($input);
		$this->fail();
	}

	public static function provider_toString()
	{
		return [
			['', '', false],
			['/a/b', '/a//b', false],
			['/', '/', false],

			['', '', true],
			['/a/b/', '/a//b', true],
			['/', '/', true],
		];
	}

	#[DataProvider('provider_toString')]
	public function test_toString($expected, string $path, bool $trailingSlash)
	{
		$actual = new UrlPath($path);
		$this->assertSame($expected, $actual->toString($trailingSlash));
		if (!$trailingSlash) {
			$this->assertSame($expected, (string)$actual);
			$this->assertSame($expected, strval($actual));
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
