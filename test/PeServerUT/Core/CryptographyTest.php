<?php

declare(strict_types=1);

namespace PeServerUT\Core;

use PeServer\Core\Binary;
use PeServer\Core\Cryptography;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\CryptoException;
use PeServerTest\Data;
use PeServerTest\TestClass;

class CryptographyTest extends TestClass
{
	public function test_generateRandomInteger_throw()
	{
		$this->expectException(CryptoException::class);

		Cryptography::generateRandomInteger(1, 2);
		$this->fail();
	}

	public function test_generateRandomBinary_throw_arg()
	{
		Cryptography::generateRandomBinary(1);
		$this->success();

		$this->expectException(ArgumentException::class);
		Cryptography::generateRandomBinary(0);
		$this->fail();
	}

	public function test_enc_dec()
	{
		$tests = [
			['input' => 'abc', 'algorithm' => 'aes-128-cbc', 'password' => '123456'],
			['input' => 'abc', 'algorithm' => 'aes-192-cbc', 'password' => '123456'],
			['input' => 'abc', 'algorithm' => 'aes-256-cbc', 'password' => '123456'],
			// ['input' => 'abc', 'algorithm' => 'aria-128-cbc', 'password' => '123456'],
			// ['input' => 'abc', 'algorithm' => 'bf-cbc', 'password' => '123456'],
			// ['input' => 'abc', 'algorithm' => 'camellia-128-cbc', 'password' => '123456'],
			// ['input' => 'abc', 'algorithm' => 'sm4-cbc', 'password' => '123456'],
		];
		foreach ($tests as $test) {
			$enc = Cryptography::encrypt($test['algorithm'], $test['input'], $test['password']);
			$dec = Cryptography::decrypt($enc, $test['password']);
			$this->assertSame($test['input'], $dec, Text::dump(['test' => $test, 'enc' => $enc]));
		}
	}

	public function test_enc_throw()
	{
		$this->expectException(CryptoException::class);
		@Cryptography::encrypt('💩', 'ABC', 'a');
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
		$this->expectException(ArgumentException::class);
		Cryptography::decrypt('', 'b');
		$this->fail();
	}

	public function test_dec_data_list4_throw()
	{
		$this->expectException(ArgumentException::class);
		Cryptography::decrypt('@@@', 'b');
		$this->fail();
	}

	public function test_dec_data_alg_throw()
	{
		$this->expectException(CryptoException::class);
		@Cryptography::decrypt('💩@@', 'b');
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
			$this->assertSame($test->expected, Text::getLength($actual), $test->str());
		}
	}

	public static function provider_generateRandomString_throw()
	{
		return [
			['$length: 0', 0, 'abc'],
			['$length: -1', -1, ''],
			['$characters: empty', 1, ''],
		];
	}
	/** @dataProvider provider_generateRandomString_throw */
	public function test_generateRandomString_throw($expected, int $length, string $characters)
	{
		$this->expectException(ArgumentException::class);
		$this->expectExceptionMessage($expected);

		Cryptography::generateRandomString($length, $characters);
		$this->fail();
	}

	public function test_password()
	{
		$plainText = 'passwd';

		$legacyPassword = password_hash($plainText, PASSWORD_BCRYPT, ['cost' => 4]);
		$hashPassword = Cryptography::hashPassword($plainText);

		$info = Cryptography::getPasswordInformation($legacyPassword);
		$this->assertSame(PASSWORD_BCRYPT, $info['algo']);
		$this->assertSame('bcrypt', $info['algoName']);
		$this->assertSame(['cost' => 4], $info['options']);

		$this->assertTrue(Cryptography::verifyPassword($plainText, $legacyPassword));
		$this->assertTrue(Cryptography::verifyPassword($plainText, $hashPassword));

		$this->assertTrue(Cryptography::needsRehashPassword($legacyPassword));
		$this->assertFalse(Cryptography::needsRehashPassword($hashPassword));
	}

	public function test_getPasswordAlgorithms()
	{
		$this->assertSame(password_algos(), Cryptography::getPasswordAlgorithms());
	}

	public function test_getHashAlgorithms()
	{
		$this->assertSame(hash_algos(), Cryptography::getHashAlgorithms());
	}

	public function test_hash()
	{
		$algorithms = Cryptography::getHashAlgorithms();
		$inputBinary = Cryptography::generateRandomBinary(64);
		foreach ($algorithms as $algorithm) {
			$actualString = Cryptography::generateHashString($algorithm, $inputBinary);
			$actualBinary = Cryptography::generateHashBinary($algorithm, $inputBinary);
			$this->assertSame($actualString, $actualBinary->toHex());
		}
	}

	public function test_generateHashString_throw()
	{
		$this->expectException(CryptoException::class);

		Cryptography::generateHashString('💩', new Binary(''));
		$this->fail();
	}

	public function test_generateHashBinary_throw()
	{
		$this->expectException(CryptoException::class);

		Cryptography::generateHashBinary('💩', new Binary(''));
		$this->fail();
	}
}
