<?php

declare(strict_types=1);

namespace PeServerUT\Core\Cli;

use PeServer\Core\Cli\CommandLine;
use PeServer\Core\Cli\LongOptionKey;
use PeServer\Core\Cli\ParameterKind;
use PeServer\Core\Collection\Access;
use PeServer\Core\Collection\Arr;
use PeServer\Core\Throws\AccessKeyNotFoundException;
use PeServer\Core\Throws\AccessValueTypeException;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\CommandLineException;
use PeServer\Core\Throws\NotImplementedException;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\TestWith;
use TypeError;

//TODO: ParsedResult とごっちゃになってる
class CommandLineTest extends TestClass
{
	#region function

	public function test_constructor_0_throw()
	{
		$this->expectException(ArgumentException::class);
		$this->expectExceptionMessage('$longOptions: 0');
		new CommandLine([]);
		$this->fail();
	}

	public function test_constructor_dup_throw()
	{
		$this->expectException(ArgumentException::class);
		$this->expectExceptionMessage('$longOptions: KEY');
		new CommandLine([
			new LongOptionKey("KEY", ParameterKind::Switch),
			new LongOptionKey("KEY", ParameterKind::Switch),
		]);
		$this->fail();
	}

	public function test_parse_simple()
	{
		$commandline = new CommandLine([
			new LongOptionKey("abc", ParameterKind::NeedValue),
			new LongOptionKey("def", ParameterKind::OptionValue),
			new LongOptionKey("efg", ParameterKind::Switch),
			new LongOptionKey("hij", ParameterKind::OptionValue),
			new LongOptionKey("klm", ParameterKind::Switch),
		]);

		$actual = $commandline->parse([
			"test",
			"--abc",
			"ABC",
			"--def",
			"DEF",
			"--efg",
			"--hij",
		]);

		$this->assertSame("test", $actual->command);
		$this->assertSame("ABC", $actual->keyValues["abc"]);
		$this->assertSame("DEF", $actual->keyValues["def"]);
		$this->assertArrayNotHasKey("hij", $actual->keyValues);
		$this->assertTrue(Arr::in($actual->switches, "efg"));

		$this->assertArrayHasKey("abc", $actual);
		$this->assertArrayHasKey("def", $actual);
		$this->assertArrayHasKey("efg", $actual);
		$this->assertArrayNotHasKey("hij", $actual);
		$this->assertArrayNotHasKey("klm", $actual);

		$this->assertSame("ABC", $actual["abc"]);
		$this->assertSame("DEF", $actual["def"]);
		$this->assertTrue($actual["efg"]);
		$this->assertFalse($actual["klm"]);
	}

	public function test_parse_0_throw()
	{
		$commandline = new CommandLine([
			new LongOptionKey("abc", ParameterKind::NeedValue),
		]);

		$this->expectException(ArgumentException::class);
		$this->expectExceptionMessage('$arguments: 0');

		$commandline->parse([]);

		$this->fail();
	}

	public function test_parse_need_throw()
	{
		$commandline = new CommandLine([
			new LongOptionKey("abc", ParameterKind::NeedValue),
		]);

		$this->expectException(CommandLineException::class);
		$this->expectExceptionMessage('need: abc');

		$commandline->parse([
			"test",
			"--abc",
		]);

		$this->fail();
	}

	public function test_offsetExists_throw()
	{
		$commandline = new CommandLine([
			new LongOptionKey("abc", ParameterKind::NeedValue),
		]);

		$actual = $commandline->parse([
			"test",
			"--abc",
			"ABC",
		]);

		$this->expectException(TypeError::class);
		isset($actual[0]);
	}

	public function test_offsetGet_throw()
	{
		$commandline = new CommandLine([
			new LongOptionKey("abc", ParameterKind::NeedValue),
		]);

		$actual = $commandline->parse([
			"test",
			"--abc",
			"ABC",
		]);

		$this->expectException(TypeError::class);
		$actual[0];
	}

	public function test_offsetSet_throw()
	{
		$commandline = new CommandLine([
			new LongOptionKey("abc", ParameterKind::NeedValue),
		]);

		$actual = $commandline->parse([
			"test",
			"--abc",
			"ABC",
		]);

		$this->expectException(NotImplementedException::class);
		$actual["abc"] = "xyz";
	}

	public function test_offsetUnset_throw()
	{
		$commandline = new CommandLine([
			new LongOptionKey("abc", ParameterKind::NeedValue),
		]);

		$actual = $commandline->parse([
			"test",
			"--abc",
			"ABC",
		]);

		$this->expectException(NotImplementedException::class);
		unset($actual["abc"]);
	}


	#endregion
}
