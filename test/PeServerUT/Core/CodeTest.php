<?php

declare(strict_types=1);

namespace PeServerUT\Core;

use Error;
use PeServer\Core\Code;
use PeServer\Core\DisposerBase;
use PeServer\Core\IDisposable;
use PeServerTest\TestClass;
use Throwable;

class CodeTest extends TestClass
{
	#region function

	public function test_toLiteralString()
	{
		$this->assertSame("", Code::toLiteralString(""));
		$this->assertSame("a", Code::toLiteralString("a"));
		$this->assertSame(" a ", Code::toLiteralString(" a "));
	}

	public function test_using_normal()
	{
		$disposer = new class extends DisposerBase
		{
			public function disposeImpl(): void
			{
				//NOP
			}
		};
		$this->assertFalse($disposer->isDisposed());
		Code::using($disposer, function (IDisposable $disposable) {
			$this->assertFalse($disposable->isDisposed());
		});
		$this->assertTrue($disposer->isDisposed());
	}

	public function test_using_error()
	{
		$disposer = new class extends DisposerBase
		{
			public function disposeImpl(): void
			{
				//NOP
			}
		};
		$this->assertFalse($disposer->isDisposed());
		try {
			Code::using($disposer, function (IDisposable $disposable) {
				$this->assertFalse($disposable->isDisposed());
				throw new \Error();
			});
			$this->fail();
		} catch (Throwable) {
			$this->success();
		}
		$this->assertTrue($disposer->isDisposed());
	}

	public function test_nameof_simple()
	{
		$var = "abc";
		$actual = Code::nameof($var);
		$this->assertSame('$var', $actual);
	}

	#endregion
}
