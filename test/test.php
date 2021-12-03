<?php declare(strict_types=1);
require_once('phpunit');

use PHPUnit\Framework\TestCase;

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
	const SRC = __DIR__ . '../../public_html/';

	protected function assertBoolean(bool $expected, bool $actual)
	{
		if($expected) {
			$this->assertTrue($actual);
		} else {
			$this->assertFalse($actual);
		}
	}
}

