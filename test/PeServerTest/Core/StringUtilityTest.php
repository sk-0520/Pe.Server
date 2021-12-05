<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use \PeServerTest\Data;
use \PeServerTest\TestClass;
use \PeServer\Core\StringUtility;

class StringUtilityTest extends TestClass
{
	public function test_isNullOrEmpty()
	{
		$tests = [
			new Data(true, null),
			new Data(true, ''),
			new Data(false, ' '),
			new Data(false, '0'),
			new Data(false, 'abc'),
		];
		foreach ($tests as $test) {
			$actual = StringUtility::isNullOrEmpty(...$test->args);
			$this->assertBoolean($test->expected, $actual);
		}
	}

	public function test_isNullOrWhiteSpace()
	{
		$tests = [
			new Data(true, null),
			new Data(true, ''),
			new Data(true, ' '),
			new Data(false, '0'),
			new Data(false, 'abc'),
		];
		foreach ($tests as $test) {
			$actual = StringUtility::isNullOrWhiteSpace(...$test->args);
			$this->assertBoolean($test->expected, $actual);
		}
	}

	public function test_replaceMap()
	{
		$tests = [
			new Data('abc', '{A}{B}{C}', [ 'A' => 'a', 'B' => 'b', 'C' => 'c',]),
			new Data('', '{x}{y}{z}', [ 'A' => 'a', 'B' => 'b', 'C' => 'c',]),
			new Data('a!?', '{A}{a}{!}', [ 'A' => 'a', 'a' => '!', '!' => '?',]),
		];
		foreach ($tests as $test) {
			$actual = StringUtility::replaceMap(...$test->args);
			$this->assertEquals($test->expected, $actual);
		}
	}
}
