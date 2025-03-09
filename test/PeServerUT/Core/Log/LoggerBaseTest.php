<?php

declare(strict_types=1);

namespace PeServerUT\Core\Log;

use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\LoggerBase;
use PeServer\Core\Log\Logging;
use PeServer\Core\Log\LogOptions;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Text;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\TestWith;

class LoggerBaseTest extends TestClass
{
	#region function

	#[TestWith(["", ""])]
	#[TestWith(["1", "{0}", 1])]
	public function test_format(string $expected, string $message, ...$parameters)
	{
		$stubSpecialStore = $this->createStub(SpecialStore::class);
		$stubSpecialStore
			->method("getServer")
			->willReturnMap([
				["REMOTE_ADDR", "<REMOTE_ADDR>"],
				["REMOTE_HOST", "<REMOTE_HOST>"],
				["HTTP_USER_AGENT", "<HTTP_USER_AGENT>"],
				["REQUEST_METHOD", "<REQUEST_METHOD>"],
				["REQUEST_URI", "<REQUEST_URI>"],
				["HTTP_REFERER", "<HTTP_REFERER>"],
			])
		;

		$obj = new class (
			new Logging(
				$stubSpecialStore
			),
			new LogOptions(self::class, 1, 0, "{MESSAGE}", [])
		) extends LoggerBase
		{
			public function __construct(Logging $logging, LogOptions $options)
			{
				parent::__construct($logging, $options);
			}

			protected function logImpl(int $level, int $traceIndex, $message, ...$parameters): void
			{
				assert(false);
			}
		};

		$actual = $this->callInstanceMethod($obj, "format", [ILogger::LOG_LEVEL_TRACE, 0, $message, ...$parameters]);
		$this->assertSame($expected, $actual);
	}

	#[TestWith([1, ILogger::LOG_LEVEL_TRACE])]
	#[TestWith([0, ILogger::LOG_LEVEL_DEBUG])]
	#[TestWith([0, ILogger::LOG_LEVEL_INFORMATION])]
	#[TestWith([0, ILogger::LOG_LEVEL_WARNING])]
	#[TestWith([0, ILogger::LOG_LEVEL_ERROR])]
	public function test_trace(int $expected, int $level)
	{
		$mock = $this->getMockBuilder(LoggerBase::class)
			->setConstructorArgs([
				new Logging($this->createStub(SpecialStore::class)),
				new LogOptions(self::class, 1, $level, "{MESSAGE}", [])
			])
			->onlyMethods(["logImpl"])
			->getMock();

		$mock->expects($this->exactly($expected))
			->method('logImpl')
		;
		$mock->trace("log");
	}

	#[TestWith([1, ILogger::LOG_LEVEL_TRACE])]
	#[TestWith([1, ILogger::LOG_LEVEL_DEBUG])]
	#[TestWith([0, ILogger::LOG_LEVEL_INFORMATION])]
	#[TestWith([0, ILogger::LOG_LEVEL_WARNING])]
	#[TestWith([0, ILogger::LOG_LEVEL_ERROR])]
	public function test_debug(int $expected, int $level)
	{
		$mock = $this->getMockBuilder(LoggerBase::class)
			->setConstructorArgs([
				new Logging($this->createStub(SpecialStore::class)),
				new LogOptions(self::class, 1, $level, "{MESSAGE}", [])
			])
			->onlyMethods(["logImpl"])
			->getMock();

		$mock->expects($this->exactly($expected))
			->method('logImpl')
		;
		$mock->debug("log");
	}

	#[TestWith([1, ILogger::LOG_LEVEL_TRACE])]
	#[TestWith([1, ILogger::LOG_LEVEL_DEBUG])]
	#[TestWith([1, ILogger::LOG_LEVEL_INFORMATION])]
	#[TestWith([0, ILogger::LOG_LEVEL_WARNING])]
	#[TestWith([0, ILogger::LOG_LEVEL_ERROR])]
	public function test_info(int $expected, int $level)
	{
		$mock = $this->getMockBuilder(LoggerBase::class)
			->setConstructorArgs([
				new Logging($this->createStub(SpecialStore::class)),
				new LogOptions(self::class, 1, $level, "{MESSAGE}", [])
			])
			->onlyMethods(["logImpl"])
			->getMock();

		$mock->expects($this->exactly($expected))
			->method('logImpl')
		;
		$mock->info("log");
	}

	#[TestWith([1, ILogger::LOG_LEVEL_TRACE])]
	#[TestWith([1, ILogger::LOG_LEVEL_DEBUG])]
	#[TestWith([1, ILogger::LOG_LEVEL_INFORMATION])]
	#[TestWith([1, ILogger::LOG_LEVEL_WARNING])]
	#[TestWith([0, ILogger::LOG_LEVEL_ERROR])]
	public function test_warn(int $expected, int $level)
	{
		$mock = $this->getMockBuilder(LoggerBase::class)
			->setConstructorArgs([
				new Logging($this->createStub(SpecialStore::class)),
				new LogOptions(self::class, 1, $level, "{MESSAGE}", [])
			])
			->onlyMethods(["logImpl"])
			->getMock();

		$mock->expects($this->exactly($expected))
			->method('logImpl')
		;
		$mock->warn("log");
	}

	#[TestWith([1, ILogger::LOG_LEVEL_TRACE])]
	#[TestWith([1, ILogger::LOG_LEVEL_DEBUG])]
	#[TestWith([1, ILogger::LOG_LEVEL_INFORMATION])]
	#[TestWith([1, ILogger::LOG_LEVEL_WARNING])]
	#[TestWith([1, ILogger::LOG_LEVEL_ERROR])]
	public function test_error(int $expected, int $level)
	{
		$mock = $this->getMockBuilder(LoggerBase::class)
			->setConstructorArgs([
				new Logging($this->createStub(SpecialStore::class)),
				new LogOptions(self::class, 1, $level, "{MESSAGE}", [])
			])
			->onlyMethods(["logImpl"])
			->getMock();

		$mock->expects($this->exactly($expected))
			->method('logImpl')
		;
		$mock->error("log");
	}

	#endregion
}

// abstract class LocalLoggerBaseClass extends LoggerBase
// {
// 	protected function __construct(Logging $logging, LogOptions $options)
// 	{
// 		parent::__construct($logging, $options);
// 	}
// }
