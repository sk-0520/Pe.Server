<?php

declare(strict_types=1);

namespace PeServerTest;

//set_include_path(__DIR__ . ':' .  __DIR__ . '/../public_html');

$files = glob(__DIR__ . '/phpunit.phar.*');
require_once($files[0]);
require_once(__DIR__ . '/../public_html/PeServer/Core/AutoLoader.php');

use PeServer\App\Models\Initializer;
use PeServer\Core\Store\SpecialStore;

\PeServer\Core\AutoLoader::initialize(
	[
		__DIR__,
		__DIR__ . '/../public_html',
	],
	'/^PeServer/'
);
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

	protected function success()
	{
		$this->assertTrue(true);
	}
}
