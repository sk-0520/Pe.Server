<?php

declare(strict_types=1);

namespace PeServerUT\Core;

use Error;
use PeServer\Core\Code;
use PeServer\Core\DisposerBase;
use PeServer\Core\IDisposable;
use PeServer\Core\Throws\InvalidOperationException;
use PeServerTest\TestClass;
use stdClass;
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

	public function test_nameof_variable()
	{
		$var = "abc";
		$actual = Code::nameof($var);
		$this->assertSame('var', $actual);
	}

	public function test_nameof_member()
	{
		$var = new LocalNameOf();
		$actual = Code::nameof($var->instance_member);
		$this->assertSame('instance_member', $actual);
	}

	public function test_nameof_sub_member()
	{
		$var = new LocalNameOf();
		$actual = Code::nameof($var->sub->member);
		$this->assertSame('member', $actual);
	}

	public function test_nameof_static_member_direct()
	{
		$actual = Code::nameof(LocalNameOf::$static_member);
		$this->assertSame('static_member', $actual);
	}

	public function test_nameof_static_member_instance()
	{
		$var = new LocalNameOf();
		$actual = Code::nameof($var::$static_member);
		$this->assertSame('static_member', $actual);
	}

	public function test_nameof_const_member_direct()
	{
		$actual = Code::nameof(LocalNameOf::CONST);
		$this->assertSame('CONST', $actual);
	}

	public function test_nameof_const_member_instance()
	{
		$var = new LocalNameOf();
		$actual = Code::nameof($var::CONST);
		$this->assertSame('CONST', $actual);
	}

	public function test_nameof_throw_dup()
	{
		$var1 = "abc";
		$var2 = "xyz";
		$this->expectException(InvalidOperationException::class);
		$this->expectExceptionMessage('Code::nameof($var1) . Code::nameof($var2);');
		Code::nameof($var1) . Code::nameof($var2);
	}

	//TODO: このパターンで死ぬ
	// public function test_nameof_throw_op()
	// {
	// 	$var1 = "abc";
	// 	$var2 = "xyz";
	// 	$this->expectException(InvalidOperationException::class);
	// 	$this->expectExceptionMessage('$var1 . $var2');
	// 	Code::nameof($var1 . $var2);
	// }

	public function test_nameof_throw_not_symbol_string()
	{
		$this->expectException(InvalidOperationException::class);
		$this->expectExceptionMessage("Code::nameof('str');");
		Code::nameof('str');
	}

	public function test_nameof_throw_not_symbol_int()
	{
		$this->expectException(InvalidOperationException::class);
		$this->expectExceptionMessage("Code::nameof(123);");
		Code::nameof(123);
	}

	#endregion
}

class LocalNameOf
{
	public const CONST = "CONST";
	public string $instance_member = "instance_member";
	public static string $static_member = "static_member";

	public stdClass $sub;

	public function __construct()
	{
		$this->sub = new stdClass();
		$this->sub->member = "sub->member";
	}
}
