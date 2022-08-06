<?php

declare(strict_types=1);

namespace PeServerTest;

error_reporting(E_ALL);

//set_include_path(__DIR__ . ':' .  __DIR__ . '/../public_html');

$files = glob(__DIR__ . '/phpunit.phar.*');
require_once($files[0]);
require_once(__DIR__ . '/../public_html/PeServer/Core/AutoLoader.php');

use Exception;
use PeServer\App\Models\Initializer;
use PeServer\Core\Store\SpecialStore;

$autoLoader = new \PeServer\Core\AutoLoader(
	[
		'PeServer' => [
			'directory' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'public_html',
		],
		'PeServerTest' => [
			'directory' => __DIR__,
		],
	]
);
$autoLoader->register();

Initializer::initialize(
	__DIR__ . '/../public_html',
	__DIR__ . '/../public_html/PeServer',
	new SpecialStore(),
	'test',
	':REVISION:'
);

class Data
{
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
		return is_null($s) ? '' : $s;
	}
}

class TestClass extends \PHPUnit\Framework\TestCase
{
	protected static function s($s): string
	{
		return $s;
	}

	public static function assertEquals($expected, $actual, string $message = ''): void
	{
		throw new Exception('empty: $info');
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
