<?php

declare(strict_types=1);

namespace PeServerTest;

ini_set('memory_limit', '-1');
error_reporting(E_ALL);

//set_include_path(__DIR__ . ':' .  __DIR__ . '/../public_html');

$files = glob(__DIR__ . '/phpunit.phar.*');
require_once($files[0]);
require_once(__DIR__ . '/../public_html/PeServer/Core/AutoLoader.php');

use Exception;
use PeServer\App\Models\AppStartup;
use PeServer\Core\DefinedDirectory;
use PeServer\Core\DI\IDiContainer;
use PeServer\Core\DI\IDiRegisterContainer;
use PeServer\Core\IO\Path;
use PeServer\Core\IO\Directory;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Web\UrlHelper;

$autoLoader = new \PeServer\Core\AutoLoader(
	[
		'PeServer' => [
			'directory' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'public_html'. DIRECTORY_SEPARATOR . 'PeServer',
		],
		'PeServerTest' => [
			'directory' => __DIR__ . DIRECTORY_SEPARATOR . 'PeServerTest',
		],
	]
);
$autoLoader->register();

$startup = new AppStartup(
	new DefinedDirectory(
		__DIR__ . '/../public_html/PeServer',
		__DIR__ . '/../public_html'
	)
);
$container = $startup->setup(
	AppStartup::MODE_TEST,
	[
		'environment' => 'test',
		'revision' => ':REVISION:',
		'special_store' => new SpecialStore(),
		'url_helper' => new UrlHelper(''),
	]
);
Directory::setTemporaryDirectory(Path::combine(__DIR__, 'temp'));
TestClass::$_do_not_use_container_user_test = $container;

/**
 * データ。
 *
 * @template TExpected
 */
class Data
{
	/** @phpstan-var TExpected */
	public $expected;
	public $args;
	public $trace;

	public function __construct($expected, ...$args)
	{
		$this->expected = $expected;
		$this->args = $args;
		$this->trace = debug_backtrace(1)[0];
	}

	public function str(): string
	{
		return "{$this->trace["file"]}:{$this->trace["line"]} " . $this->__toString();
	}

	public function __toString(): string
	{
		$s = print_r($this->args, true);
		return $s === null ? '' : $s;
	}
}

class TestClass extends \PHPUnit\Framework\TestCase
{
	/** テストコードで直接使用しないDIコンテナ */
	public static IDiContainer $_do_not_use_container_user_test;

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

	public static function assertEquals($expected, $actual, string $message = ''): void
	{
		if (!strlen($message)) {
			throw new Exception('$message が未設定。 もしくは誤って assertEquals が呼び出されている');
		}

		parent::assertEquals($expected, $actual, $message);
	}

	protected function assertEqualsWithInfo(string $info, mixed $expected, mixed $actual, string $message = '')
	{
		if (empty($info)) {
			// 厳密比較でない理由が不明
			throw new Exception('empty: $info');
		}
		parent::assertEquals($expected, $actual, $message);
	}

	protected function success()
	{
		$this->assertTrue(true);
	}
}
