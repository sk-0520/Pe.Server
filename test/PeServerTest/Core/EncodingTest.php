<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use PeServer\Core\Encoding;
use PeServer\Core\Throws\ArgumentException;
use PeServerTest\Data;
use PeServerTest\TestClass;

class EncodingTest extends TestClass
{
	public function test_construct()
	{
		new Encoding('ASCII');
		$this->success();
	}

	public function test_construct_error()
	{
		$this->expectException(ArgumentException::class);
		new Encoding('ascii');
		$this->fail();
	}

	public function test_construct_empty_error()
	{
		$this->expectException(ArgumentException::class);
		new Encoding('');
		$this->fail();
	}

	public function test_convert()
	{
		$tests = [
			new Data('abc', 'ASCII', 'abc'),
			new Data('???', 'ASCII', 'あいう'),
			new Data('?<?>?', 'ASCII', 'あ<い>う'),
			new Data('????', 'ASCII', '🥚🍳🐔💦'),

			new Data('abc', 'JIS', 'abc'),
			new Data('あいう', 'JIS', 'あいう'),
			new Data('あ<い>う', 'JIS', 'あ<い>う'),
			new Data('????', 'JIS', '🥚🍳🐔💦'),

			new Data('abc', 'SJIS', 'abc'),
			new Data('あいう', 'SJIS', 'あいう'),
			new Data('あ<い>う', 'SJIS', 'あ<い>う'),
			new Data('????', 'SJIS', '🥚🍳🐔💦'),

			new Data('abc', 'EUC-JP-2004', 'abc'),
			new Data('あいう', 'EUC-JP-2004', 'あいう'),
			new Data('あ<い>う', 'EUC-JP-2004', 'あ<い>う'),
			new Data('????', 'EUC-JP-2004', '🥚🍳🐔💦'),

			new Data('abc', 'UTF-8', 'abc'),
			new Data('あいう', 'UTF-8', 'あいう'),
			new Data('あ<い>う', 'UTF-8', 'あ<い>う'),
			new Data('🥚🍳🐔💦', 'UTF-8', '🥚🍳🐔💦'),

			new Data('abc', 'UTF-16', 'abc'),
			new Data('あいう', 'UTF-16', 'あいう'),
			new Data('あ<い>う', 'UTF-16', 'あ<い>う'),
			new Data('🥚🍳🐔💦', 'UTF-16', '🥚🍳🐔💦'),

			new Data('abc', 'UTF-32', 'abc'),
			new Data('あいう', 'UTF-32', 'あいう'),
			new Data('あ<い>う', 'UTF-32', 'あ<い>う'),
			new Data('🥚🍳🐔💦', 'UTF-32', '🥚🍳🐔💦'),
		];
		foreach($tests as $test) {
			$encoding = new Encoding($test->args[0]);
			$binary = $encoding->getBinary($test->args[1]);
			$actual = $encoding->toString($binary);
			$this->assertEquals($test->expected, $actual);
		}
	}
}
