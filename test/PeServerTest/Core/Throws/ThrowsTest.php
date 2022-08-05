<?php

declare(strict_types=1);

namespace PeServerTest\Core\Throws;

use \Error;
use \Exception;
use \InvalidArgumentException;
use \OutOfBoundsException;
use \Throwable;
use \UnexpectedValueException;
use \TypeError;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\Throws;
use PeServerTest\TestClass;

class ThrowsTest extends TestClass
{
	function provider_wrap()
	{
		return [
			[false, [InvalidArgumentException::class, OutOfBoundsException::class], UnexpectedValueException::class, fn () => throw new Exception()],
			[false, [InvalidArgumentException::class, OutOfBoundsException::class], UnexpectedValueException::class, fn () => throw new UnexpectedValueException()],
			[true, [InvalidArgumentException::class, OutOfBoundsException::class], UnexpectedValueException::class, fn () => throw new InvalidArgumentException()],
			[true, [InvalidArgumentException::class, OutOfBoundsException::class], UnexpectedValueException::class, fn () => throw new OutOfBoundsException()],
		];
	}

	/** @dataProvider provider_wrap */
	function test_wrap($catch, $catchExceptions, $throwException, $callback)
	{
		try {
			Throws::wrap($catchExceptions, $throwException, $callback);
		} catch (Throwable $ex) {
			if ($catch) {
				$this->assertTrue(is_a($ex, $throwException));
				return;
			} else {
				if (is_string($catchExceptions)) {
					$this->assertFalse(is_a($ex, $catchExceptions));
					return;
				} else {
					foreach ($catchExceptions as $catchException) {
						if (is_a($ex, $catchException)) {
							$this->fail();
						}
					}
					$this->success();
					return;
				}
			}
		}
		$this->fail();
	}

	function test_wrap_result()
	{
		$a = Throws::wrap(Exception::class, Error::class, function () {
		});
		$this->assertNull($a);

		$b = Throws::wrap(Exception::class, Error::class, fn () => null);
		$this->assertNull($b);

		$c = Throws::wrap(Exception::class, Error::class, fn () => 1);
		$this->assertSame(1, $c);
	}

	function test_wrap_stringCatchClassName_throw()
	{
		$this->expectException(TypeError::class);
		Throws::wrap('X', Error::class, fn () => throw new Exception());
		$this->fail();
	}

	function test_wrap_arrayCatchEmpty_throw()
	{
		$this->expectException(TypeError::class);
		Throws::wrap([], Error::class, fn () => throw new Exception());
		$this->fail();
	}

	function test_wrap_arrayCatchClassName0_throw()
	{
		$this->expectException(TypeError::class);
		Throws::wrap(['X', Exception::class], Error::class, fn () => throw new Exception());
		$this->fail();
	}

	function test_wrap_arrayCatchClassName1_throw()
	{
		$this->expectException(TypeError::class);
		Throws::wrap([Exception::class, 'X'], Error::class, fn () => throw new Exception());
		$this->fail();
	}

	function test_wrap_throwException_throw()
	{
		$this->expectException(TypeError::class);
		Throws::wrap(Exception::class, 'X', fn () => throw new Exception());
		$this->fail();
	}
}
