<?php

declare(strict_types=1);

namespace PeServerUT\Core\Errors;

use Error;
use PeServer\Core\Errors\ErrorData;
use PeServer\Core\Throws\InvalidOperationException;
use PeServerTest\TestClass;

class ErrorDataTest extends TestClass
{
	#region function

	public function test_constructor()
	{
		$actual = new ErrorData(
			123,
			"abc",
			"file",
			789
		);

		$this->assertSame(123, $actual->code);
		$this->assertSame("abc", $actual->message);
		$this->assertSame("file", $actual->file);
		$this->assertSame(789, $actual->line);
	}

	public function test_createFromArray()
	{
		$actual = ErrorData::createFromArray([
			"type" => 456,
			"message" => "MESSAGE",
			"file" => "FILE",
			"line" => 654,
		]);

		$this->assertSame(456, $actual->code);
		$this->assertSame("MESSAGE", $actual->message);
		$this->assertSame("FILE", $actual->file);
		$this->assertSame(654, $actual->line);
	}

	public function test_createFromLastError()
	{
		@trigger_error("ERROR");
		$actual = ErrorData::createFromLastError();
		$this->assertSame("ERROR", $actual->message);
	}

	public function test_createFromLastError_throw()
	{
		$this->expectException(InvalidOperationException::class);
		ErrorData::createFromLastError();
		$this->fail();
	}

	public function test_getLastError()
	{
		@trigger_error("ERROR");
		$actual = ErrorData::getLastError();
		$this->assertSame("ERROR", $actual->message);
	}

	public function test_getLastError_none()
	{
		$actual = ErrorData::getLastError();
		$this->assertNull($actual);
	}

	#endregion
}
