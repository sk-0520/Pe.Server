<?php

declare(strict_types=1);

namespace PeServerTest\Core\Collections;

use PeServer\Core\Collections\Dictionary;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\IndexOutOfRangeException;
use PeServer\Core\Throws\KeyNotFoundException;
use PeServer\Core\TypeUtility;
use PeServerTest\TestClass;
use \stdClass;
use \TypeError;


class DictionaryTest extends TestClass
{
	function test_create_empty_throw()
	{
		$this->expectException(ArgumentException::class);
		Dictionary::create([]);
		$this->fail();
	}

	function test_empty()
	{
		$actual = Dictionary::empty(TypeUtility::TYPE_UNKNOWN);
		$this->assertSame(0, $actual->count());
	}

	function test_offsetExists()
	{
		$actual = Dictionary::create(['a' => 'A', 'b' => 'B', '0' => 'o']);
		$this->assertFalse(isset($actual['A']));
		$this->assertTrue(isset($actual['a']));
		$this->assertTrue(isset($actual['b']));
		$this->assertTrue(isset($actual['0']));
	}

	function test_offsetExists_throw()
	{
		$actual = Dictionary::create(['a' => 'A', 'b' => 'B', '0' => 'o']);
		$this->expectException(TypeError::class);
		isset($actual[0]);
		$this->fail();
	}

	function test_offsetGet()
	{
		$actual = Dictionary::create(['a' => 'A', 'b' => 'B', '0' => 'o']);
		$this->assertSame('A', $actual['a']);
		$this->assertSame('B', $actual['b']);
		$this->assertSame('o', $actual['0']);
	}

	function test_offsetGet_null_throw()
	{
		$actual = Dictionary::create(['a' => 'A', 'b' => 'B', '0' => 'o']);
		$this->expectException(TypeError::class);
		$actual[null];
		$this->fail();
	}

	function test_offsetGet_not_string_throw()
	{
		$actual = Dictionary::create(['a' => 'A', 'b' => 'B', '0' => 'o']);
		$this->expectException(TypeError::class);
		$actual[0];
		$this->fail();
	}

	function test_offsetGet_notFound_throw()
	{
		$actual = Dictionary::create(['a' => 'A', 'b' => 'B', '0' => 'o']);
		$actual['a'];
		$this->expectException(KeyNotFoundException::class);
		$actual['A'];
		$this->fail();
	}

	function test_offsetSet()
	{
		$actual = Dictionary::create(['a' => 'A', 'b' => 'B', '0' => 'o']);
		$this->assertSame('A', $actual['a']);
		$this->assertSame('B', $actual['b']);
		$this->assertSame('o', $actual['0']);

		$actual['a'] = '10';
		$actual['b'] = $actual['b'] . $actual['b'];
		$actual['0'] .= 'o';

		$this->assertSame('10', $actual['a']);
		$this->assertSame('BB', $actual['b']);
		$this->assertSame('oo', $actual['0']);
	}

	function test_offsetUnset()
	{
		$actual = Dictionary::create(['a' => 'A', 'b' => 'B', '0' => 'o']);

		unset($actual['a']);
		$this->assertSame(2, $actual->count());

		unset($actual['b']);
		$this->assertSame(1, $actual->count());

		unset($actual['0']);
		$this->assertSame(0, $actual->count());
	}
}
