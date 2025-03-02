<?php

declare(strict_types=1);

namespace PeServerUT\Core\Collections;

use PeServer\Core\Collections\GeneratorIterator;
use PeServer\Core\Throws\CallbackTypeError;

use PeServerTest\TestClass;
use TypeError;

class GeneratorIteratorTest extends TestClass
{
	public static function staticCase()
	{
		yield 0;
		yield 1;
		yield 2;
	}

	public function instanceCase()
	{
		yield 'a';
		yield 'b';
		yield 'c';
	}

	public function test_static_create()
	{
		$expected = [0, 1, 2];

		$iterator = new GeneratorIterator([self::class, 'staticCase']);
		$actual1 = iterator_to_array($iterator);
		$actual2 = iterator_to_array($iterator);

		$this->assertSame($expected, $actual1);
		$this->assertSame($expected, $actual2);
	}

	public function test_instance_create()
	{
		$expected = ['a', 'b', 'c'];

		$iterator = new GeneratorIterator([$this, 'instanceCase']);
		$actual1 = iterator_to_array($iterator);
		$actual2 = iterator_to_array($iterator);

		$this->assertSame($expected, $actual1);
		$this->assertSame($expected, $actual2);
	}

	public function test_function_create()
	{
		$expected = [10, 20, 30];

		$iterator = new GeneratorIterator(function () {
			yield 10;
			yield 20;
			yield 30;
		});
		$actual1 = iterator_to_array($iterator);
		$actual2 = iterator_to_array($iterator);

		$this->assertSame($expected, $actual1);
		$this->assertSame($expected, $actual2);
	}

	public function test_create_callable_throw()
	{
		$this->expectException(CallbackTypeError::class);
		new GeneratorIterator('(^_^)');
		$this->fail();
	}

	public function test_create_generator_throw()
	{
		$this->expectException(TypeError::class);
		new GeneratorIterator(fn () => 1 + 1);
		$this->fail();
	}
}
