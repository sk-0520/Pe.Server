<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use PeServerTest\Data;
use PeServerTest\TestClass;
use PeServer\Core\Cryptography;
use PeServer\Core\StringUtility;
use PeServer\Core\Throws\CryptoException;

class CryptographyTest extends TestClass
{
	public function test_enc_dec()
	{
		$tests = [
			['input' => 'abc', 'algorithm' => 'aes-128-cbc', 'password' => '123456'],
			['input' => 'abc', 'algorithm' => 'aes-192-cbc', 'password' => '123456'],
			['input' => 'abc', 'algorithm' => 'aes-256-cbc', 'password' => '123456'],
			['input' => 'abc', 'algorithm' => 'aria-128-cbc', 'password' => '123456'],
			['input' => 'abc', 'algorithm' => 'bf-cbc', 'password' => '123456'],
			['input' => 'abc', 'algorithm' => 'camellia-128-cbc', 'password' => '123456'],
			['input' => 'abc', 'algorithm' => 'sm4-cbc', 'password' => '123456'],
		];
		foreach ($tests as $test) {
			$enc = Cryptography::encrypt($test['algorithm'], $test['input'], $test['password']);
			$dec = Cryptography::decrypt($enc, $test['password']);
			$this->assertEquals($test['input'], $dec, StringUtility::dump(['test' => $test, 'enc' => $enc]));
		}
	}

	public function test_enc_throw()
	{
		$this->expectException(CryptoException::class);
		Cryptography::encrypt('💩', 'ABC', 'a');
		$this->fail();
	}

	public function test_dec_throw()
	{
		$enc = Cryptography::encrypt('aes-256-cbc', 'ABC', 'a');
		$this->expectException(CryptoException::class);
		Cryptography::decrypt($enc, 'b');
		$this->fail();
	}

	public function test_dec_data_list0_throw()
	{
		$this->expectException(CryptoException::class);
		Cryptography::decrypt('', 'b');
		$this->fail();
	}

	public function test_dec_data_list4_throw()
	{
		$this->expectException(CryptoException::class);
		Cryptography::decrypt('@@@', 'b');
		$this->fail();
	}

	public function test_dec_data_alg_throw()
	{
		$this->expectException(CryptoException::class);
		Cryptography::decrypt('💩@@', 'b');
		$this->fail();
	}

	public function test_dec_data_iv_throw()
	{
		$this->expectException(CryptoException::class);
		Cryptography::decrypt('aes-256-cbc@@', 'b');
		$this->fail();
	}

	public function test_generateRandomString()
	{
		$tests = [
			new Data(4, 4, 'a'),
			new Data(4, 4, 'ab'),
			new Data(4, 4, 'abc'),
			new Data(4, 4, 'abcd'),
			new Data(4, 4, 'abcde'),
			new Data(4, 4, 'あ'),
			new Data(4, 4, 'あい'),
			new Data(4, 4, 'あいう'),
			new Data(4, 4, 'あいうえ'),
			new Data(4, 4, 'あいうえお'),
			new Data(4, 4, '🐁'),
			new Data(4, 4, '🐁🐄'),
			new Data(4, 4, '🐁🐄🐅'),
			new Data(4, 4, '🐁🐄🐅🐇'),
			new Data(4, 4, '🐁🐄🐅🐇🐉'),
		];
		foreach ($tests as $test) {
			$actual = Cryptography::generateRandomString(...$test->args);
			$this->assertEquals($test->expected, StringUtility::getLength($actual), $test->str());
		}
	}
}
