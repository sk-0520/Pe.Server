<?php

declare(strict_types=1);

namespace PeServerTest;

set_include_path(__DIR__ . ':' .  __DIR__ . '/../public_html');

require_once(__DIR__ . '/phpunit');
require_once(__DIR__ . '/../public_html/PeServer/Core/AutoLoader.php');
require_once(__DIR__ . '/../public_html/PeServer/Libs/smarty/libs/Smarty.class.php');

use \PeServer\App\Models\Initializer;

\PeServer\Core\registerAutoLoader([
	__DIR__,
	__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'public_html',
]);

Initializer::initialize(
	__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'public_html',
	__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'public_html' . DIRECTORY_SEPARATOR . 'PeServer',
	'test'
);

class Data
{
	public $expected;
	public $args;

	public function __construct($expected, ...$args)
	{
		$this->expected = $expected;
		$this->args = $args;
	}

	public function str(): string
	{
		return $this->__toString();
	}

	public function __toString(): string
	{
		$s = var_export($this->args, true);
		return is_null($s) ? '' : $s;
	}
}

class TestClass extends \PHPUnit\Framework\TestCase
{
	protected static function s($s): string
	{
		return $s;
	}

	protected function assertBoolean(bool $expected, bool $actual, string $message = '')
	{
		if ($expected) {
			$this->assertTrue($actual, $message);
		} else {
			$this->assertFalse($actual, $message);
		}
	}
}
