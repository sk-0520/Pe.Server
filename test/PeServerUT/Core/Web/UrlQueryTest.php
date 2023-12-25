<?php

declare(strict_types=1);

namespace PeServerUT\Core\Web;

use stdClass;
use PeServer\Core\Encoding;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Web\UrlEncoding;
use PeServer\Core\Web\UrlQuery;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class UrlQueryTest extends TestClass
{
	public static function provider_constructor_throw()
	{
		return [
			[['A' => 1]],
			[['A' => 'a']],
			[['A' => new stdClass()]],
			[['A' => [1]]],
		];
	}

	#[DataProvider('provider_constructor_throw')]
	public function test_constructor_throw($query)
	{
		$this->expectException(ArgumentException::class);
		new UrlQuery($query);
		$this->fail();
	}

	public function test_from()
	{
		$actual1 = UrlQuery::from(['A' => ['a', 'b', 'c']]);
		$this->assertEqualsWithInfo('配列の比較が微妙', ['A' => ['a', 'b', 'c']], $actual1->getQuery());

		$actual2 = UrlQuery::from(['A' => null]);
		$this->assertEqualsWithInfo('配列の比較が微妙', ['A' => []], $actual2->getQuery());

		$actual3 = UrlQuery::from(['A' => 'a']);
		$this->assertEqualsWithInfo('配列の比較が微妙', ['A' => ['a']], $actual3->getQuery());

		$actual4 = UrlQuery::from(['A' => 123]);
		$this->assertEqualsWithInfo('配列の比較が微妙', ['A' => ['123']], $actual4->getQuery());

		$actual5 = UrlQuery::from(['A' => []]);
		$this->assertEqualsWithInfo('配列の比較が微妙', ['A' => []], $actual5->getQuery());

		$actual5 = UrlQuery::from(['A' => [1]]);
		$this->assertEqualsWithInfo('配列の比較が微妙', ['A' => ['1']], $actual5->getQuery());

		$actual6 = UrlQuery::from([123 => 456]);
		$this->assertEqualsWithInfo('配列の比較が微妙', ['123' => ['456']], $actual6->getQuery());
	}

	public static function provider_from_throw()
	{
		return [
			[['A' => true]],
			[['A' => 3.14]],
			[['A' => new stdClass()]],
			[['A' => [true]]],
			[['A' => [3.14]]],
			[['A' => [new stdClass()]]],
		];
	}

	#[DataProvider('provider_from_throw')]
	public function test_from_throw($query)
	{
		$this->expectException(ArgumentException::class);
		UrlQuery::from($query);
	}

	public static function provider_create_and_get()
	{
		return [
			[['a' => [null]], 'a'],
			[['a' => ['']], 'a='],
			[['a' => ['1']], 'a=1'],
			[['a' => ['1', '2']], 'a=1&a=2'],
			[['a' => ['1'], 'b' => ['2']], 'a=1&b=2'],
			[['💀' => ['👻']], '%F0%9F%92%80=%F0%9F%91%BB'],
			[[], '=1'],
			[['a' => ['1']], 'a=1&=2'],
			[['a' => ['1']], '=0&a=1'],
			[['a' => ['=1']], 'a=%3D1'],
			[['a' => ['=1']], 'a==1'],
		];
	}

	#[DataProvider('provider_create_and_get')]
	public function test_create_and_get(array $expected, string|array|null $query)
	{
			$this->assertEqualsWithInfo('配列の比較が微妙', $expected, (new UrlQuery($query))->getQuery());
	}

	public function test_empty()
	{
		$this->assertTrue((new UrlQuery(null))->isEmpty());
		$this->assertFalse((new UrlQuery(''))->isEmpty());
	}

	public function test_empty_throw()
	{
		$query = new UrlQuery(null);
		$this->expectException(InvalidOperationException::class);
		$query->getQuery();
		$this->fail();
	}

	public static function provider_toString()
	{
		return [
			['', null],
			['?', ''],
			['?a', 'a'],
			['?a=', 'a='],
			['?a=1', 'a=1'],
			['?a=1&a=2', 'a=1&a=2'],
			['?%F0%9F%92%80=%F0%9F%91%BB', '%F0%9F%92%80=%F0%9F%91%BB'],
			['?a=1', 'a=1&=2'],
			['?a=1', '=0&a=1'],
			['?a=%3D1', 'a=%3D1'],
			['?a=%3D1', 'a==1'],
		];
	}

	#[DataProvider('provider_toString')]
	public function test_toString(string $expected, string|array|null $query)
	{
		$query = new UrlQuery($query);
		$this->assertSame($expected, $query->toString());
		$this->assertSame($expected, (string)$query);
	}
}
