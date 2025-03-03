<?php

declare(strict_types=1);

namespace PeServerUT\Core\Log;

use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\Logging;
use PeServer\Core\Store\SpecialStore;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\TestWith;

class LoggingTest extends TestClass
{
	#region function

	public function test_constructor()
	{
		$logging = new Logging(new SpecialStore());
		$this->success();
	}

	#[TestWith(["TRACE", ILogger::LOG_LEVEL_TRACE])]
	#[TestWith(["DEBUG", ILogger::LOG_LEVEL_DEBUG])]
	#[TestWith(["INFO ", ILogger::LOG_LEVEL_INFORMATION])]
	#[TestWith(["WARN ", ILogger::LOG_LEVEL_WARNING])]
	#[TestWith(["ERROR", ILogger::LOG_LEVEL_ERROR])]
	public function test_formatLevel(string $expected, int $level)
	{
		$actual = $this->callStaticMethod(Logging::class, "formatLevel", [$level]);
		$this->assertSame($expected, $actual);
	}

	public function test_formatMessage_message_none_parameters()
	{
		$actual = $this->callStaticMethod(Logging::class, "formatMessage", ["message"]);
		$this->assertSame("message", $actual);
	}

	public function test_formatMessage_null_message_empty_parameters()
	{
		$actual = $this->callStaticMethod(Logging::class, "formatMessage", [null]);
		$this->assertSame("", $actual);
	}

	public function test_formatMessage_null_message_single_parameters()
	{
		$actual = $this->callStaticMethod(Logging::class, "formatMessage", [null, 1]);
		$this->assertSame(
			"array(1) {
  [0]=>
  int(1)
}
",
			$actual
		);
	}

	public function test_formatMessage_null_message_double_parameters()
	{
		$actual = $this->callStaticMethod(Logging::class, "formatMessage", [null, 1, "str"]);
		$this->assertSame(
			"array(2) {
  [0]=>
  int(1)
  [1]=>
  string(3) \"str\"
}
",
			$actual
		);
	}

	public function test_formatMessage_no_format_message_single_parameters()
	{
		$actual = $this->callStaticMethod(Logging::class, "formatMessage", ["message", 1]);
		$this->assertSame("message", $actual);
	}

	public function test_formatMessage_format_message_none_parameters()
	{
		$actual = $this->callStaticMethod(Logging::class, "formatMessage", ["message {0}"]);
		$this->assertSame("message {0}", $actual);
	}

	public function test_formatMessage_format_message_single_parameters_int()
	{
		$actual = $this->callStaticMethod(Logging::class, "formatMessage", ["message {0}", 1]);
		$this->assertSame("message 1", $actual);
	}

	public function test_formatMessage_format_message_single_parameters_str()
	{
		$actual = $this->callStaticMethod(Logging::class, "formatMessage", ["message {0}", "STR"]);
		$this->assertSame("message STR", $actual);
	}

	public function test_formatMessage_format_message_single_parameters_arr()
	{
		$actual = $this->callStaticMethod(Logging::class, "formatMessage", ["message {0}", [1, "b" => 2, "c" => 'C']]);
		$this->assertSame(
			"message array(3) {
  [0]=>
  int(1)
  [\"b\"]=>
  int(2)
  [\"c\"]=>
  string(1) \"C\"
}
",
			$actual
		);
	}

	public function test_formatMessage_format_message_single_parameters_obj()
	{
		$obj = new LocalObj();
		$id = spl_object_id($obj);
		$actual = $this->callStaticMethod(Logging::class, "formatMessage", ["message {0}", $obj]);
		$this->assertSame(
			"message object(PeServerUT\Core\Log\LocalObj)#{$id} (2) {
  [\"str\"]=>
  string(3) \"STR\"
  [\"number\"]=>
  int(123)
}
",
			$actual
		);
	}

	public function test_formatMessage_format_message_obj_none_parameters()
	{
		$obj = new LocalObj();
		$id = spl_object_id($obj);
		$actual = $this->callStaticMethod(Logging::class, "formatMessage", [$obj]);
		$this->assertSame(
			"object(PeServerUT\Core\Log\LocalObj)#{$id} (2) {
  [\"str\"]=>
  string(3) \"STR\"
  [\"number\"]=>
  int(123)
}
",
			$actual
		);
	}

	public function test_formatMessage_int_message_single_none_parameters()
	{
		$actual = $this->callStaticMethod(Logging::class, "formatMessage", [123]);
		$this->assertSame(
			"int(123)
",
			$actual
		);
	}

	public function test_formatMessage_str_message_single_none_parameters()
	{
		$actual = $this->callStaticMethod(Logging::class, "formatMessage", ["abc"]);
		$this->assertSame(
			"abc",
			$actual
		);
	}

	public function test_formatMessage_int_message_single_single_parameters()
	{
		$actual = $this->callStaticMethod(Logging::class, "formatMessage", [123, 456]);
		$this->assertSame(
			"array(2) {
  [\"message\"]=>
  int(123)
  [\"parameters\"]=>
  array(1) {
    [0]=>
    int(456)
  }
}
",
			$actual
		);
	}

	#endregion
}

class LocalObj
{
	public string $str = "STR";
	public int $number = 123;

	public function method(): void
	{
	}
}
