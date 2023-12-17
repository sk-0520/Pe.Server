<?php

declare(strict_types=1);

namespace PeServerTest;

use ReflectionClass;
use Exception;
use PeServer\Core\DI\IDiContainer;
use PeServer\Core\DI\IDiRegisterContainer;
use ReflectionException;

class TestClass extends \PHPUnit\Framework\TestCase
{
	/**
	 * テストコードで直接使用しないDIコンテナ
	 *
	 * `self::container()` が諸々を肩代わりする。
	 */
	//phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
	public static IDiContainer $_do_not_use_container_user_test;
	/**
	 * テスト設定。
	 *
	 * 本ソースファイルと同じ場所に `@setting.json` が存在すればマージされる。
	 * @var array
	 */
	public static array $setting = [
		'local_server' => 'http://localhost:8080'
	];

	public static function localServer(string $addUrl): string
	{
		$url = rtrim(self::$setting['local_server'], "/");
		if (strlen($addUrl)) {
			$url .= '/' . ltrim($addUrl, "/");
		}
		return $url;
	}

	/**
	 * テスト用コンテナを取得。
	 *
	 * スコープ内でのみ有効。
	 *
	 * @return IDiRegisterContainer
	 */
	protected function container(): IDiRegisterContainer
	{
		return self::$_do_not_use_container_user_test->clone();
	}

	protected static function s($s): string
	{
		return $s;
	}

	protected function assertEqualsWithInfo(string $info, mixed $expected, mixed $actual, string $message = '')
	{
		if (empty($info)) {
			// 厳密比較でない理由が不明
			throw new Exception('empty: $info');
		}
		parent::assertEquals($expected, $actual, $message);
	}

	protected function assertNotEqualsWithInfo(string $info, mixed $expected, mixed $actual, string $message = '')
	{
		if (empty($info)) {
			// 厳密比較でない理由が不明
			throw new Exception('empty: $info');
		}
		parent::assertNotEquals($expected, $actual, $message);
	}

	protected function success()
	{
		$this->assertTrue(true);
	}

	/**
	 * インスタンスメソッドの呼び出し。
	 *
	 * 非 `public` を呼び出す想定。
	 *
	 * @param object $object インスタンス。
	 * @param string $method メソッド名。
	 * @param array $params 引数。
	 * @return mixed 結果。
	 * @throws ReflectionException
	 */
	protected function callInstanceMethod(object $object, string $method, array $params = [])
	{
		$reflection = new ReflectionClass($object);
		$method = $reflection->getMethod($method);
		return $method->invokeArgs($object, $params);
	}
}
