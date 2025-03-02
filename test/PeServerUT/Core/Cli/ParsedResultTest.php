<?php

declare(strict_types=1);

namespace PeServerUT\Core\Cli;

use PeServer\Core\Cli\CommandLine;
use PeServer\Core\Cli\LongOptionKey;
use PeServer\Core\Cli\ParameterKind;
use PeServer\Core\Cli\ParsedResult;
use PeServer\Core\Collections\Access;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Throws\AccessKeyNotFoundException;
use PeServer\Core\Throws\AccessValueTypeException;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\CommandLineException;
use PeServer\Core\Throws\KeyNotFoundException;
use PeServer\Core\Throws\NotImplementedException;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\TestWith;
use TypeError;

class ParsedResultTest extends TestClass
{
	#region function

	public function test_getValue()
	{
		$actual = new ParsedResult(
			"test",
			[],
			[
				"abc" => "ABC",
			]
		);

		$this->assertSame("ABC", $actual->getValue("abc"));
	}

	public function test_getValue_kv_throw()
	{
		$actual = new ParsedResult(
			"test",
			[],
			[
				"abc" => "ABC",
			]
		);

		$this->expectException(KeyNotFoundException::class);
		$this->expectExceptionMessage("xyz");
		$actual->getValue("xyz");
		$this->fail();
	}

	public function test_getValue_switch_throw()
	{
		$actual = new ParsedResult(
			"test",
			[
				"abc"
			],
			[]
		);

		$this->expectException(KeyNotFoundException::class);
		$this->expectExceptionMessage("xyz");
		$actual->getValue("xyz");
		$this->fail();
	}

	public function test_hasValue()
	{
		$actual = new ParsedResult(
			"test",
			[
				"abc"
			],
			[]
		);

		$this->assertTrue($actual->hasSwitch("abc"));
		$this->assertFalse($actual->hasSwitch("ABC"));
	}



	#endregion
}
