<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use \PeServerTest\Data;
use \PeServerTest\TestClass;
use \PeServer\Core\Cryptography;
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
			$enc = Cryptography::encrypt($test['input'], $test['algorithm'], $test['password']);
			$dec = Cryptography::decrypt($enc, $test['password']);
			$this->assertEquals($test['input'], $dec, StringUtility::dump(['test' => $test, 'enc' => $enc]));
		}
	}

	public function test_enc_error()
	{
		$this->expectException(CryptoException::class);
		Cryptography::encrypt('ABC', '💩', 'a');
	}

	public function test_dec_error()
	{
		$enc = Cryptography::encrypt('ABC', 'aes-256-cbc', 'a');
		$this->expectException(CryptoException::class);
		Cryptography::decrypt($enc, 'b');
	}

	public function test_dec_data_list0_error()
	{
		$this->expectException(CryptoException::class);
		Cryptography::decrypt('', 'b');
	}

	public function test_dec_data_list4_error()
	{
		$this->expectException(CryptoException::class);
		Cryptography::decrypt('@@@', 'b');
	}

	public function test_dec_data_alg_error()
	{
		$this->expectException(CryptoException::class);
		Cryptography::decrypt('💩@@', 'b');
	}

	public function test_dec_data_iv_error()
	{
		$this->expectException(CryptoException::class);
		Cryptography::decrypt('aes-256-cbc@@', 'b');
	}
}
