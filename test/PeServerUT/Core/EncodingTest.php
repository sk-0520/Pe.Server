<?php

declare(strict_types=1);

namespace PeServerUT\Core;

use PeServer\Core\Encoding;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\EncodingException;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class EncodingTest extends TestClass
{
	public function test_construct()
	{
		new Encoding('ASCII');
		$this->success();
	}

	public function test_construct_throw()
	{
		$this->expectException(ArgumentException::class);
		new Encoding('ascii');
		$this->fail();
	}

	public function test_construct_empty_throw()
	{
		$this->expectException(ArgumentException::class);
		new Encoding('');
		$this->fail();
	}

	public static function provider_convert()
	{
		return [
			['abc', 'ASCII', 'abc'],
			['???', 'ASCII', 'あいう'],
			['?<?>?', 'ASCII', 'あ<い>う'],
			['????', 'ASCII', '🥚🍳🐔💦'],

			['abc', 'JIS', 'abc'],
			['あいう', 'JIS', 'あいう'],
			['あ<い>う', 'JIS', 'あ<い>う'],
			['????', 'JIS', '🥚🍳🐔💦'],

			['abc', 'SJIS', 'abc'],
			['あいう', 'SJIS', 'あいう'],
			['あ<い>う', 'SJIS', 'あ<い>う'],
			['????', 'SJIS', '🥚🍳🐔💦'],

			['abc', 'EUC-JP-2004', 'abc'],
			['あいう', 'EUC-JP-2004', 'あいう'],
			['あ<い>う', 'EUC-JP-2004', 'あ<い>う'],
			['????', 'EUC-JP-2004', '🥚🍳🐔💦'],

			['abc', 'UTF-8', 'abc'],
			['あいう', 'UTF-8', 'あいう'],
			['あ<い>う', 'UTF-8', 'あ<い>う'],
			['🥚🍳🐔💦', 'UTF-8', '🥚🍳🐔💦'],

			['abc', 'UTF-16', 'abc'],
			['あいう', 'UTF-16', 'あいう'],
			['あ<い>う', 'UTF-16', 'あ<い>う'],
			['🥚🍳🐔💦', 'UTF-16', '🥚🍳🐔💦'],

			['abc', 'UTF-32', 'abc'],
			['あいう', 'UTF-32', 'あいう'],
			['あ<い>う', 'UTF-32', 'あ<い>う'],
			['🥚🍳🐔💦', 'UTF-32', '🥚🍳🐔💦'],
		];
	}

	#[DataProvider('provider_convert')]
	public function test_convert(string $expected, string $name, string $input)
	{
		$encoding = new Encoding($name);
		$binary = $encoding->getBinary($input);
		$actual = $encoding->toString($binary);
		$this->assertSame($expected, $actual);
	}

	public static function provider_getAliasNames()
	{
		return [
			[['utf8'], 'UTF-8'],
			[['utf8'], 'utf8'],
		];
	}

	#[DataProvider('provider_getAliasNames')]
	public function test_getAliasNames(array $expected, string $input)
	{
		$actual = Encoding::getAliasNames($input);
		$this->assertSame(count($expected), count($actual));
		$this->assertSame($expected, $actual);
	}

	public function test_getAliasNames_throw()
	{
		$this->expectException(EncodingException::class);

		Encoding::getAliasNames('💩');
		$this->fail();
	}
}
