<?php declare(strict_types=1);
namespace PeServerTest;

require_once(__DIR__ . '/phpunit');
require_once(__DIR__ . '/../public_html/PeServer/Core/AutoLoader.php');

use \PeServer\App\Models\Initializer;
use \PHPUnit\Framework\TestCase;

\PeServer\Core\registerAutoLoader([
	__DIR__,
	__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'public_html',
]);

Initializer::initialize(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'public_html', 'test');

class Data
{
	public $expected;
	public $args;

	public function __construct($expected, ...$args)
	{
		$this->expected = $expected;
		$this->args = $args;
	}
}

class TestClass extends TestCase
{
	protected function assertBoolean(bool $expected, bool $actual)
	{
		if($expected) {
			$this->assertTrue($actual);
		} else {
			$this->assertFalse($actual);
		}
	}
}

