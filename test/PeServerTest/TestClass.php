<?php

declare(strict_types=1);

namespace PeServerTest;

use ReflectionClass;
use Exception;
use PeServer\Core\DI\IDiContainer;
use PeServer\Core\DI\IDiRegisterContainer;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\Path;
use PeServer\Core\Text;
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

	protected function getProperty(object $object, string $name): mixed
	{
		$reflection = new ReflectionClass($object);
		$property = $reflection->getProperty($name);
		return $property->getValue($object);
	}
	protected function setProperty(object $object, string $name, mixed $value): void
	{
		$reflection = new ReflectionClass($object);
		$property = $reflection->getProperty($name);
		$property->setValue($object, $value);
	}

	protected function testDir(): TestDirectory
	{
		$stackTrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2)[1];
		$class = Text::replace($stackTrace['class'], '\\', '/');
		$function = $stackTrace['function'];

		$tempDir = Directory::getTemporaryDirectory();

		$dirPath = Path::combine($tempDir, $class, $function);
		Directory::createDirectory($dirPath);

		return new TestDirectory($dirPath);
	}
}
