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

	#endregion
}
