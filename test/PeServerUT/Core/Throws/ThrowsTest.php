<?php

declare(strict_types=1);

namespace PeServerUT\Core\Throws;

use Error;
use Exception;
use InvalidArgumentException;
use OutOfBoundsException;
use Throwable;
use UnexpectedValueException;
use TypeError;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\CoreException;
use PeServer\Core\Throws\InvalidException;
use PeServer\Core\Throws\Throws;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class ThrowsTest extends TestClass
{
	public static function provider_wrap()
	{
		return [
			[false, [InvalidArgumentException::class, OutOfBoundsException::class], UnexpectedValueException::class, fn () => throw new Exception()],
			[false, [InvalidArgumentException::class, OutOfBoundsException::class], UnexpectedValueException::class, fn () => throw new UnexpectedValueException()],
			[true, [InvalidArgumentException::class, OutOfBoundsException::class], UnexpectedValueException::class, fn () => throw new InvalidArgumentException()],
			[true, [InvalidArgumentException::class, OutOfBoundsException::class], UnexpectedValueException::class, fn () => throw new OutOfBoundsException()],
		];
	}

	#[DataProvider('provider_wrap')]
	public function test_wrap($catch, $catchExceptions, $throwException, $callback)
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

	public function test_wrap_result()
	{
		$a = Throws::wrap(Exception::class, Error::class, function () {
		});
		$this->assertNull($a);

		$b = Throws::wrap(Exception::class, Error::class, fn () => null);
		$this->assertNull($b);

		$c = Throws::wrap(Exception::class, Error::class, fn () => 1);
		$this->assertSame(1, $c);
	}

	public function test_wrap_stringCatchClassName_throw()
	{
		$this->expectException(TypeError::class);
		Throws::wrap('X', Error::class, fn () => throw new Exception());
		$this->fail();
	}

	public function test_wrap_arrayCatchEmpty_throw()
	{
		$this->expectException(TypeError::class);
		Throws::wrap([], Error::class, fn () => throw new Exception());
		$this->fail();
	}

	public function test_wrap_arrayCatchClassName0_throw()
	{
		$this->expectException(TypeError::class);
		Throws::wrap(['X', Exception::class], Error::class, fn () => throw new Exception());
		$this->fail();
	}

	public function test_wrap_arrayCatchClassName1_throw()
	{
		$this->expectException(TypeError::class);
		Throws::wrap([Exception::class, 'X'], Error::class, fn () => throw new Exception());
		$this->fail();
	}

	public function test_wrap_throwException_throw()
	{
		$this->expectException(TypeError::class);
		Throws::wrap(Exception::class, 'X', fn () => throw new Exception());
		$this->fail();
	}

	public function test_throwIf_true()
	{
		Throws::throwIf(true);
		$this->success();
	}

	public function test_throwIf_false_ee()
	{
		$this->expectException(InvalidException::class);
		Throws::throwIf(false);
		$this->fail();
	}

	public function test_throwIf_false_throw()
	{
		$this->expectException(Error::class);
		Throws::throwIf(false, '', Error::class);
		$this->fail();
	}

	public function test_throwIfNull()
	{
		$this->expectException(InvalidException::class);
		Throws::throwIfNull(null);
		$this->fail();

		Throws::throwIfNull(123);
		$this->success();
	}

	public function test_throwIfNullOrEmpty()
	{
		$this->expectException(InvalidException::class);
		Throws::throwIfNullOrEmpty(null);
		$this->fail();
		Throws::throwIfNullOrEmpty('');
		$this->fail();

		Throws::throwIfNull(' ');
		$this->success();
	}

	public function test_throwIfNullOrWhiteSpace()
	{
		$this->expectException(InvalidException::class);
		Throws::throwIfNullOrWhiteSpace(null);
		$this->fail();
		Throws::throwIfNullOrWhiteSpace('');
		$this->fail();
		Throws::throwIfNullOrWhiteSpace(' ');
		$this->fail();

		Throws::throwIfNullOrWhiteSpace(' a ');
		$this->success();
	}
}
